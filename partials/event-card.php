<div class="bsf-event-card scroll-reveal revealed">
  <div class="bsf-tags-addtocalendar">
    <?php echo bsf_get_event_tags_and_cal(get_the_ID(), 'small'); ?>
  </div>
  
  
  <div class="bsf-event-card-text">
    <h2 class="bsf-event-card-title">
      <a href="<?php the_permalink(); ?>">
        <?php the_title(); ?>
      </a>
 
    </h2>

    <?php if(carbon_get_post_meta($post->ID, 'bsf_short_description')): ?>
      <div class="bsf-text bsf-event-card-short-description">
        <?php echo wpautop(carbon_get_post_meta($post->ID, 'bsf_short_description')); ?>
      </div>
    <?php endif; ?>

    <?php if(carbon_get_post_meta($post->ID, 'bsf_description')): ?>
      <div class="bsf-show-longer-description bsf-buttons-wrapper" id="description-holder-<?php the_ID(); ?>">
        <?php 
          $queryString = 'admin-ajax.php?action=bsf_get_event_description';
          $queryString .= '&eventId=' . $post->ID;
          $queryString .= '&nonce=' . wp_create_nonce('load_description');
        ?>
        <span class="bsf-cta-text-link bsf-show-desc small"
          hx-get="<?php echo esc_url(admin_url($queryString)); ?>"
          hx-swap="outerHTML"
          hx-target="#description-holder-<?php the_ID(); ?>"
        >
          <?php _e('Bővebben..', 'bsf-plugin'); ?>
        </span>
      </div>
    <?php endif; ?>
  </div>
  
  <div class="bsf-event-card-meta">
    <?php echo bsf_get_event_meta(get_the_ID()); ?>
  </div>
  
  
  <?php echo bsf_get_event_speakers(get_the_ID()); ?>

  <script>
  (function() {
    // Get elements relative to this card only
    const card = document.currentScript.closest('.bsf-event-card');
    const button = card.querySelector('.add-to-calendar');
    const popup = card.querySelector('.add-to-calendar-popup');
    
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