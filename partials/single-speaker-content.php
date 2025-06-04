<div class="bsf-parent-class bsf-single-speaker-content-outer-wrapper">
  <div class="bsf-container">

    <?php if(carbon_get_post_meta(get_the_ID(),'bsf_speaker_description')): ?>
    <div class="bsf-single-speaker-content">
      <div class="bsf-single-speaker-description bsf-text">
        <?php echo wpautop(carbon_get_post_meta(get_the_ID(), 'bsf_speaker_description')); ?>
      </div>
    </div>
    <?php endif; ?>

    <?php 
      $relevantEvents = bsf_get_relevant_events(get_the_ID());
    ?>

    <?php if($relevantEvents): ?>
    <div class="bsf-single-speaker-events-wrapper">
      <h3 class="section-title">
        <?php _e('Események', 'bsf-plugin'); ?>
      </h3>
      <div class="bsf-events-list-wrapper bsf-columns">
        <?php
          if ( $relevantEvents ) :								      
              foreach($relevantEvents as $event): 
                $post = get_post($event -> ID);
                setup_postdata($post);
              
                include BSF_PLUGIN_DIR . 'partials/event-card-compact.php';
            
              endforeach;
              wp_reset_postdata();
          endif;
        ?>
      </div>
    </div>
    <?php endif; ?>

    <?php bsf_display_speakers_page_button(); ?>
  </div>
</div>