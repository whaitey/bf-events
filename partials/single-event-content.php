<div class="bsf-parent-class bsf-single-event-content-outer-wrapper">
  <div class="bsf-container">
    <div class="bsf-single-event-content-tag-list">
      <?php echo bsf_get_event_tags_and_cal(get_the_ID(), 'small outline-black', true, true, true, false, true); ?>
    </div>

    <div class="bsf-single-event-content">
      <div class="bsf-single-event-description bsf-text">
        <?php echo wpautop(carbon_get_post_meta(get_the_ID(), 'bsf_description')); ?>
      </div>
    </div>

    <?php 
      $relevantSpeakers = bsf_get_relevant_speakers(get_the_ID());
      $moderators = bsf_get_relevant_moderators(get_the_ID());
    ?>

    <?php if($moderators): ?>
      <div class="bsf-single-event-speakers-wrapper">
        <h3 class="section-title">
          <?php _e('Moderátor', 'bsf-plugin'); ?>
        </h3>
        <div class="bsf-speakers-list-wrapper bsf-columns">
          <?php
            if ( $moderators ) :		

              $moderatorsQuery = new WP_Query( array( 
                'post_type' => 'bsf_speaker', 
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'post__in' => $moderators,
                'orderby' => 'meta_value', // Order by meta value
                'meta_key' => '_bsf_last_name', // Your meta key
                'order' => 'ASC', // A-Z order
                ) 
              );


              if ( $moderatorsQuery->have_posts() ) :								      
                while ( $moderatorsQuery->have_posts() ) : $moderatorsQuery->the_post();
                  global $post;
                  setup_postdata($post);
                  include BSF_PLUGIN_DIR . 'partials/speaker-card.php';
                endwhile;
                wp_reset_postdata();
            endif;


            endif;
          ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if($relevantSpeakers): ?>
      <div class="bsf-single-event-speakers-wrapper">
        <h3 class="section-title">
          <?php _e('Előadók', 'bsf-plugin'); ?>
        </h3>
        <div class="bsf-speakers-list-wrapper bsf-columns">
          <?php
            if ( $relevantSpeakers ) :		

              $speakersQuery = new WP_Query( array( 
                'post_type' => 'bsf_speaker', 
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'post__in' => $relevantSpeakers,
                'orderby' => 'meta_value', // Order by meta value
                'meta_key' => '_bsf_last_name', // Your meta key
                'order' => 'ASC', // A-Z order
                ) 
              );


              if ( $speakersQuery->have_posts() ) :								      
                while ( $speakersQuery->have_posts() ) : $speakersQuery->the_post();
                  global $post;
                  setup_postdata($post);
                  include BSF_PLUGIN_DIR . 'partials/speaker-card.php';
                endwhile;
                wp_reset_postdata();
            endif;


            endif;
          ?>
        </div>
      </div>
    <?php endif; ?>

    <?php bsf_display_events_page_button(); ?>
  </div>
</div>