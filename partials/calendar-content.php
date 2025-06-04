<div class="bsf-parent-class bsf-calendar-content-outer-wrapper">
  <div class="bsf-container">
    <div class="bsf-calendar-content-filters-wrapper bsf-filter-outer-wrapper" id="bsf-filters-main-parent">
      <?php echo bsf_get_calendar_stages_filter(); ?>
      <?php bsf_display_calendar_filter_buttons(); ?>
    </div>
    <?php bsf_display_mobile_filters_button(); ?>
    <div class="bsf-calendar-outer-wrapper">
      <div class="bsf-calendar-table">
        <div class="bsf-time-column">
          <?php bsf_get_time_column(); ?>
        </div>
        <div class="bsf-stages-column">
          <div class="bsf-stages-moving-wrapper">

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="bsf-modal" class="bsf-modal hidden bsf-parent-class">
  <div class="bsf-modal-content" id="bsf-modal-content">
    <!-- Content will be loaded here -->
  </div>
</div>

<script>
    // Reset form and trigger HTMX
    document.getElementById('bsf-reset-stages').addEventListener('click', function() {
        // Reset the form fields (uncheck checkboxes)
        document.getElementById('bsf-calendar-filter').reset();

        // Optionally, trigger HTMX to reload the posts
        htmx.trigger('#bsf-calendar-filter', 'change'); // This will simulate a change event after resetting
    });
</script>