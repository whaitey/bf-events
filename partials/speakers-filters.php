<?php
  $stageTax = get_taxonomy('bsf_stage');

  $queryString = 'admin-ajax.php?action=bsf_filter_speakers';
  $queryString .= '&nonce=' . wp_create_nonce('filter_speakers');

  $mainEventNames = get_terms( array(
      'taxonomy'   => 'bsf_main_event_name',
      'hide_empty' => true,
      'orderby' => 'term_id',
      'order' => 'ASC'
  ) );

  // getting sub event names

  $subEventNames = [];

  if (!empty($mainEventNames) && !is_wp_error($mainEventNames)) {
    foreach ($mainEventNames as $parent) {
        $children = get_terms([
            'taxonomy'   => 'bsf_main_event_name',
            'hide_empty' => true, // Again, exclude empty terms
            'parent'     => $parent->term_id, // Get terms that have a parent
        ]);

        $subEventNames = array_merge($subEventNames, $children);
    }
  }

  // getting stages

  $stages = get_terms( array(
    'taxonomy'   => 'bsf_stage',
    'hide_empty' => true,
    'orderby' => 'term_id',
    'order' => 'ASC'
  ) );

  // Get all unique company names from published speakers
  $company_args = array(
      'post_type'      => 'bsf_speaker',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'fields'         => 'ids',
  );
  $company_speaker_ids = get_posts($company_args);
  $companies = [];
  foreach ($company_speaker_ids as $sid) {
      $company = carbon_get_post_meta($sid, 'bsf_company');
      if (!empty($company)) {
          $companies[] = $company;
      }
  }
  $companies = array_unique($companies);
  sort($companies, SORT_LOCALE_STRING);

?>
<div class="bsf-filter-widget sidebar-filter">
  <h3 class="bsf-widget-title">
    <?php _e('Szűrés', 'bsf-plugin'); ?>
    <span id="filters-mobile-close"></span>
  </h3>
  <form action="" id="bsf-sidebar-filter"
    hx-post="<?php echo esc_url(admin_url($queryString)); ?>" 
    hx-target=".bsf-speakers-list-wrapper"
    hx-trigger="change"
    hx-indicator=".bsf-speakers-list-wrapper"
    >
    <div class="bsf-filters-inner-wrapper">
      <div class="bsf-text-dropdown-filters-wrapper">
        <div class="bsf-form-inputs-wrapper">

          <div class="bsf-form-input bsf-search-input">
          <input type="text" placeholder="<?php _e('Keresés...', 'bsf-plugin'); ?>" class="bsf-text-input" name="search" 
            hx-trigger="input changed delay:800ms, keyup[key=='Enter'], load"
            hx-post="<?php echo esc_url(admin_url($queryString)); ?>" 
            hx-target=".bsf-speakers-list-wrapper"
            hx-indicator=".bsf-speakers-list-wrapper"
            >
          </div>

          <?php if(!empty($subEventNames)): ?>
            <div class="bsf-form-input bsf-dropdown-filter-input" id="bsf-sub-event-name-dropdown">
              <button type="button" class="dropdown-filter-label">
                <?php _e('Alesemény', 'bsf-plugin'); ?>
              </button>
              <div class="input-list">
                <div class="input-list-inner-wrapper">
                  <?php foreach($subEventNames as $subEventName): ?>
                    <div class="input-line checkbox-line">
                      <label>
                          <?php echo $subEventName->name; ?>
                          <input 
                              type="checkbox" 
                              name="eventNamesArray[]" 
                              value="<?php echo $subEventName->term_id; ?>" 
                          >
                          <span class="checkmark"></span>
                      </label>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if(!empty($stages)): ?>
            <div class="bsf-form-input bsf-dropdown-filter-input" id="bsf-stage-dropdown">
              <button type="button" class="dropdown-filter-label">
                <?php echo $stageTax->labels->name; ?>
              </button>
              <div class="input-list">
                <div class="input-list-inner-wrapper">
                  <?php foreach($stages as $stage): ?>
                    <div class="input-line checkbox-line">
                      <label>
                          <?php echo $stage->name; ?>
                          <input 
                              type="checkbox" 
                              name="stagesArray[]" 
                              value="<?php echo $stage->term_id; ?>" 
                          >
                          <span class="checkmark"></span>
                      </label>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <div class="bsf-form-input bsf-company-filter">
            <select name="company" class="bsf-text-input">
              <option value=""><?php _e('Cég szűrés...', 'bsf-plugin'); ?></option>
              <?php foreach ($companies as $company): ?>
                <option value="<?php echo esc_attr($company); ?>"><?php echo esc_html($company); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

        </div>
      </div>
      <button type="button" class="bsf-button small outline-black" id="bsf-clear-all-filters">
        <?php _e('Feltételek törlése', 'bsf-plugin'); ?>
      </button>
    </div>
  </form>
</div>

<script>
  const form = document.getElementById('bsf-sidebar-filter');
  const searchInput = form.querySelector('input[name="search"]');

  searchInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault(); // prevent native form submit
        htmx.trigger(form, 'change');
      }
    });

    // Extra: prevent form submission as a final fallback
    form.addEventListener('submit', function (e) {
      e.preventDefault();
    });

    // Clear all filters and reload speakers
    document.getElementById('bsf-clear-all-filters').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Clear the stored filter state
        if (typeof sessionStorage !== 'undefined') {
            sessionStorage.removeItem('bsfEventFiltersState');
        }
        
        // Reset all form fields
        const form = document.getElementById('bsf-sidebar-filter');
        if (form) {
            form.reset();
            
            // Clear all checkboxes
            form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Clear search input
            const searchInput = form.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.value = '';
            }
            
            // Clear company select
            const companySelect = form.querySelector('select[name="company"]');
            if (companySelect) {
                companySelect.value = '';
            }
            
            // Clear stage dropdown
            const stageDropdown = document.getElementById('bsf-stage-dropdown');
            if (stageDropdown) {
                stageDropdown.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    cb.checked = false;
                });
            }
        }
        
        // Trigger HTMX to reload speakers with no filters
        if (typeof htmx !== 'undefined') {
            htmx.trigger(form, 'change');
        }
    });
</script>