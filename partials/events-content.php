<!-- if $featured works -->

<div class="bsf-parent-class">
  <div class="bsf-container">
    <div class="bsf-event-list-outer-wrapper">
      <div class="bsf-filter-outer-wrapper" id="bsf-filters-main-parent">
        
        <?php require BSF_PLUGIN_DIR . 'partials/events-filters.php'; ?>

        <?php bsf_display_calendar_page_button(); ?>
      </div>
      <?php bsf_display_mobile_filters_button(); ?>
      <div class="bsf-event-list-wrapper">

      </div>
      <div class="bsf-buttons-wrapper">
        <button id="bsf-show-past-events" class="bsf-button small" style="display:none;">
          <?php _e('Korábbi programok megjelenítése', 'bsf-plugin'); ?>
        </button>
      </div>
    </div>
  </div></div>