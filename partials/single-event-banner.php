<div class="bsf-parent-class bsf-page-banner-outer-wrapper bsf-single-event-banner">
  <div class="bsf-container">
    <div class="bsf-page-banner-content">
      <div class="bsf-banner-text">
        <h1 class="bsf-banner-heading">
          <?php the_title(); ?>
        </h1>
        <div class="bsf-page-banner-description bsf-text">
          <?php echo bsf_get_event_meta(get_the_ID()); ?>
        </div>
        <div class="bsf-tags-addtocalendar">
          <?php echo bsf_get_event_tags_and_cal(get_the_ID(), '', true, false, false, true); ?>
        </div>
        <script>
          (function() {
            // Get elements relative to this card only
            const banner = document.currentScript.closest('.bsf-single-event-banner');
            const button = banner.querySelector('.add-to-calendar');
            const popup = banner.querySelector('.add-to-calendar-popup');
            
            // Toggle function for this specific instance
            function togglePopup(e) {
              e.stopPropagation();
              button.classList.toggle('active');
              popup.classList.toggle('active');
            }
            
            // Add click handler
            button.addEventListener('click', togglePopup);
            
            // Close when clicking outside (card-specific)
            document.addEventListener('click', function() {
              if (button.classList.contains('active')) {
                button.classList.remove('active');
                popup.classList.remove('active');
              }
            });
            
            // Prevent popup clicks from closing
            popup.addEventListener('click', function(e) {
              e.stopPropagation();
            });
          })();
        </script>
      </div>
    </div>
  </div>
</div>