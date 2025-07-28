<?php
  $stageTax = get_taxonomy('bsf_stage');
  $eventLocationTax = get_taxonomy('bsf_event_location');

  $queryString = 'admin-ajax.php?action=bsf_filter_events';
  $queryString .= '&nonce=' . wp_create_nonce('filter_events');

  $loadBannerString = 'admin-ajax.php?action=bsf_load_banner';
  $loadBannerString .= '&nonce=' . wp_create_nonce('load_banner');

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

  // getting stages and checking if we are on a stage page

  if(! is_tax('bsf_stage')):
    $stages = get_terms( array(
      'taxonomy'   => 'bsf_stage',
      'hide_empty' => true,
      'orderby' => 'term_id',
      'order' => 'ASC'
    ) );
  endif;

  $locations = get_terms( array(
      'taxonomy'   => 'bsf_event_location',
      'hide_empty' => true,
      'orderby' => 'term_id',
      'order' => 'ASC'
  ) );

  $eventTags = get_terms( array(
      'taxonomy'   => 'bsf_event_tag',
      'hide_empty' => true,
      'orderby' => 'title',
      'order' => 'ASC'
  ) );

    $speakerIds = bsf_get_event_person_ids();
  if (!empty($speakerIds)) {
    $speakers = get_posts([
      'post_type'      => 'bsf_speaker',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'post__in'       => $speakerIds,
      'orderby'        => 'meta_value',
      'meta_key'       => '_bsf_last_name',
      'order'          => 'ASC',
    ]);
  }

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
  

  <form method="POST" id="bsf-sidebar-filter"
    hx-post="<?php echo esc_url(admin_url($queryString)); ?>" 
    hx-target=".bsf-event-list-wrapper"
    hx-trigger="change throttle:100ms"
    hx-indicator=".bsf-event-list-wrapper"

  >
    <div class="bsf-filters-inner-wrapper">
      <div class="bsf-text-dropdown-filters-wrapper">
        <div class="bsf-form-inputs-wrapper">

          <div class="bsf-form-input bsf-search-input">
            <input type="text" placeholder="<?php _e('Keresés...', 'bsf-plugin'); ?>" class="bsf-text-input" name="search" 
            hx-trigger="input changed delay:800ms, keyup[key=='Enter'], load"
            hx-post="<?php echo esc_url(admin_url($queryString)); ?>" 
            hx-target=".bsf-event-list-wrapper"
            hx-indicator=".bsf-event-list-wrapper"
            >
          </div>

          <?php if(!empty($subEventNames) && carbon_get_theme_option('bsf_show_event_filter')): ?>
            <div class="bsf-form-input bsf-dropdown-filter-input" id="bsf-sub-event-name-dropdown">
              <button type="button" class="dropdown-filter-label">
                <?php _e('Alesemény', 'bsf-plugin'); ?>
              </button>
              <div class="input-list">
                <div class="input-list-inner-wrapper">
                  <div class="input-line checkbox-line">
                    <label>
                        <?php _e('Összes', 'bsf-plugin'); ?>
                        <input 
                            type="radio" 
                            name="eventNamesArray[]" 
                            value="" 
                            checked
                            hx-post="<?php echo esc_url(admin_url($loadBannerString)); ?>" 
                            hx-target="#main-events-banner"
                            hx-indicator="#main-events-banner"
                        >
                        <span class="checkmark"></span>
                    </label>
                  </div>
                  <?php foreach($subEventNames as $subEventName): ?>
                    <div class="input-line checkbox-line">
                      <label>
                          <?php echo $subEventName->name; ?>
                          <input 
                              type="radio" 
                              name="eventNamesArray[]" 
                              value="<?php echo $subEventName->term_id; ?>"
                              hx-post="<?php echo esc_url(admin_url($loadBannerString)); ?>" 
                              hx-target="#main-events-banner"
                              hx-indicator="#main-events-banner" 
                          >
                          <span class="checkmark"></span>
                      </label>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if(!empty($stages) && ! is_tax('bsf_stage') && carbon_get_theme_option('bsf_show_stage_filter')): ?>
            <div class="bsf-form-input bsf-dropdown-filter-input" id="bsf-stage-dropdown">
              <button type="button" class="dropdown-filter-label">
                <?php echo $stageTax->labels->name; ?>
              </button>
              <div class="input-list">
                <div class="input-list-inner-wrapper">
                <div class="input-line checkbox-line">
                    <label>
                        <?php _e('Összes', 'bsf-plugin'); ?>
                        <input 
                            type="radio" 
                            name="stagesArray[]" 
                            value="" 
                            checked
                            hx-post="<?php echo esc_url(admin_url($loadBannerString)); ?>" 
                            hx-target="#main-events-banner"
                            hx-indicator="#main-events-banner"
                        >
                        <span class="checkmark"></span>
                    </label>
                  </div>
                  <?php foreach($stages as $stage): ?>
                    <div class="input-line checkbox-line">
                      <label>
                          <?php echo $stage->name; ?>
                          <input 
                              type="radio" 
                              name="stagesArray[]" 
                              value="<?php echo $stage->term_id; ?>"
                              hx-post="<?php echo esc_url(admin_url($loadBannerString)); ?>" 
                              hx-target="#main-events-banner"
                              hx-indicator="#main-events-banner" 
                          >
                          <span class="checkmark"></span>
                      </label>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if(!empty($locations) && carbon_get_theme_option('bsf_show_location_filter')): ?>
            <div class="bsf-form-input bsf-dropdown-filter-input" id="bsf-location-dropdown">
              <button type="button" class="dropdown-filter-label">
                <?php echo $eventLocationTax->labels->name; ?>
              </button>
              <div class="input-list">
                <div class="input-list-inner-wrapper">
                  <?php foreach($locations as $location): ?>
                    <div class="input-line checkbox-line">
                      <label>
                          <?php echo $location->name; ?>
                          <input
                              type="checkbox"
                              name="locationsArray[]"
                              value="<?php echo $location->term_id; ?>"
                          >
                          <span class="checkmark"></span>
                      </label>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php endif; ?>
 <?php if(!empty($speakers) && carbon_get_theme_option('bsf_show_speaker_filter')): ?>
            <div class="bsf-form-input bsf-dropdown-filter-input" id="bsf-speaker-dropdown">
              <button type="button" class="dropdown-filter-label">
                <?php _e('Előadók', 'bsf-plugin'); ?>
              </button>
              <div class="input-list">
              
                <div class="input-list-inner-wrapper">
                <div class="dropdown-search">
                  <input type="text" class="bsf-text-input speaker-search" placeholder="<?php _e('Keresés...', 'bsf-plugin'); ?>">
                </div>
                  <?php foreach($speakers as $sp): ?>
                    <div class="input-line checkbox-line">
                      <label>
                          <?php echo carbon_get_post_meta($sp->ID, 'bsf_last_name') . ' ' . carbon_get_post_meta($sp->ID, 'bsf_first_name'); ?>
                          <input
                              type="checkbox"
                              name="speakersArray[]"
                              value="<?php echo $sp->ID; ?>"
                          >
                          <span class="checkmark"></span>
                      </label>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php endif; ?>
<?php if(!empty($companies) && carbon_get_theme_option('bsf_show_company_filter')): ?>
  <div class="bsf-form-input bsf-dropdown-filter-input" id="bsf-company-dropdown">
    <button type="button" class="dropdown-filter-label">
      <?php _e('Cégek', 'bsf-plugin'); ?>
    </button>
    <div class="input-list">
      <div class="input-list-inner-wrapper">
        <div class="dropdown-search">
          <input type="text" class="bsf-text-input company-search" placeholder="<?php _e('Keresés...', 'bsf-plugin'); ?>">
        </div>
        <?php foreach($companies as $company): ?>
          <div class="input-line checkbox-line">
            <label>
                <?php echo esc_html($company); ?>
                <input
                    type="checkbox"
                    name="companiesArray[]"
                    value="<?php echo esc_attr($company); ?>"
                >
                <span class="checkmark"></span>
            </label>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>
          <?php if(carbon_get_theme_option('bsf_show_past_events_filter')): ?>
          <div class="input-line checkbox-line" id="bsf-past-toggle-line">
            <label>
              <?php _e('Korábbi programok megjelenítése', 'bsf-plugin'); ?>
              <input type="checkbox" name="showPast" id="bsf-show-past-events">
              <span class="checkmark"></span>
            </label>
          </div>
          <?php endif; ?>

        </div>
      </div>
      <?php if(!empty($eventTags) && carbon_get_theme_option('bsf_show_tag_filter')): ?>
        <div class="bsf-tag-filters-wrapper bsf-buttons-wrapper">
          <?php foreach($eventTags as $tag): ?>
            <input 
                  type="checkbox" 
                  name="tagsArray[]" 
                  value="<?php echo $tag->term_id; ?>" 
                  id="bsf-tag-input-<?php echo $tag->term_id; ?>"
              >
            <label class="bsf-event-tag bsf-button small outline-black" for="bsf-tag-input-<?php echo $tag->term_id; ?>">
              <?php echo $tag->name; ?>
            </label>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <a class="bsf-clear-filters bsf-cta-text-link bsf-close small" id="bsf-reset-filters">
        <?php _e('Feltételek törlése', 'bsf-plugin'); ?>
      </a>
    </div>
    <?php
      // additional special filter inputs 
      if(is_tax('bsf_stage')):
        $stageId =  get_queried_object()->term_id; 

        echo '<input type="hidden" name="stagesArray[]" value="' . $stageId . '">';
      endif;

      if($featured):
        echo '<input type="hidden" name="featured" value="' . $featured . '">';
      endif;
    ?>
  </form>
</div>

<script>
    // prevent submit
    const form = document.getElementById('bsf-sidebar-filter');
    const searchInput = form.querySelector('input[name="search"]');
    const eventNameGroup = document.getElementById('bsf-sub-event-name-dropdown');
    const stageGroup = document.getElementById('bsf-stage-dropdown');
    

    // Function to reset a radio group to "All" (empty value)
    function resetGroup(group) {
      const defaultRadio = group.querySelector('input[type="radio"][value=""]');
      if (defaultRadio) {
        defaultRadio.checked = true;
      }
    }

    // Set up mutual exclusivity
    function setupExclusivity(groupToWatch, groupToReset) {
      groupToWatch.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
          if (this.value !== '') { // Only reset if non-"All" option selected
            resetGroup(groupToReset);
            // Trigger HTMX update
            htmx.trigger(form, 'change');
          }
        });
      });
    }

    // Set up bidirectional exclusivity
    setupExclusivity(eventNameGroup, stageGroup);
    setupExclusivity(stageGroup, eventNameGroup);

    const speakerDropdown = document.getElementById("bsf-speaker-dropdown");
      if (speakerDropdown) {
        speakerDropdown.addEventListener("input", function(e) {
          if (e.target.classList.contains("speaker-search")) {
            const filter = e.target.value.toLowerCase();
            speakerDropdown.querySelectorAll(".checkbox-line").forEach(line => {
              const text = line.textContent.toLowerCase();
              line.style.display = text.includes(filter) ? "" : "none";
            });
          }
        });
      }

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

    // Reset form and trigger HTMX
    document.getElementById('bsf-reset-filters').addEventListener('click', function() {
        // Reset the form fields (uncheck checkboxes)
        form.reset();

        if (speakerSearchInput) {
          speakerSearchInput.value = '';
          speakerSearchInput.dispatchEvent(new Event('input'));
        }

        // Clear company filter checkboxes and search, and show all options
        const companyDropdown = document.getElementById('bsf-company-dropdown');
        if (companyDropdown) {
          // Uncheck all checkboxes
          companyDropdown.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
            cb.dispatchEvent(new Event('change', { bubbles: true }));
          });
          // Clear search input
          const companySearch = companyDropdown.querySelector('.company-search');
          if (companySearch) {
            companySearch.value = '';
            companyDropdown.querySelectorAll('.checkbox-line').forEach(line => {
              line.style.display = '';
            });
          }
          // Optionally close the dropdown (if you want)
          // const inputList = companyDropdown.querySelector('.input-list');
          // if (inputList) inputList.style.display = 'none';
        }
        // Also trigger a change event on the form to ensure HTMX updates
        htmx.trigger(form, 'change');

        // Remove all hidden inputs except nonce
        form.querySelectorAll('input[type="hidden"]').forEach(function(input) {
          if (!input.name.includes('nonce')) {
            input.remove();
          }
        });
        // Trigger change event again to apply cleared filters
        htmx.trigger(form, 'change');

    });

    

    form.dispatchEvent(new Event('change', { bubbles: true }));

    // remove hx-post or hx-get if target is missing
    document.addEventListener("DOMContentLoaded", function() {
      // Process all buttons with hx-target
      document.querySelectorAll("[hx-target]").forEach(button => {
        const targetSelector = button.getAttribute("hx-target");
        const target = document.querySelector(targetSelector);

        // If target is missing, disable HTMX
        if (!target) {
          button.removeAttribute("hx-post");
          button.removeAttribute("hx-get");
        }
      });
    });

    // Company dropdown search
    const companyDropdown = document.getElementById("bsf-company-dropdown");
    if (companyDropdown) {
      companyDropdown.addEventListener("input", function(e) {
        if (e.target.classList.contains("company-search")) {
          const filter = e.target.value.toLowerCase();
          companyDropdown.querySelectorAll(".checkbox-line").forEach(line => {
            const text = line.textContent.toLowerCase();
            line.style.display = text.includes(filter) ? "" : "none";
          });
        }
      });
    }
</script>
