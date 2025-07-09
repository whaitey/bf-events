<?php
  $date = carbon_get_post_meta($post->ID, 'bsf_date');
  $start = carbon_get_post_meta($post->ID, 'bsf_starting_time');
  $end = carbon_get_post_meta($post->ID, 'bsf_ending_time');
  $data_start = $date && $start ? $date . ' ' . $start : '';
  $data_end = $date && $end ? $date . ' ' . $end : '';
?>
<div class="bsf-event-card compact bsf-column" data-start="<?php echo esc_attr($data_start); ?>" data-end="<?php echo esc_attr($data_end); ?>">
  <div class="bsf-tags-addtocalendar">
    <?php echo bsf_get_event_tags_and_cal(get_the_ID(), 'small', true, false, false, true, true ); ?>
  </div>
  <div class="bsf-event-card-text">
    <h2 class="bsf-event-card-title">
      <a href="<?php the_permalink(); ?>">
        <?php the_title(); ?>
      </a>
    </h2>
    <div class="bsf-event-card-meta">
    <?php echo bsf_get_event_meta(get_the_ID()); ?>
    </div>
  </div>
</div>