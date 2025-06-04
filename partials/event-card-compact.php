<div class="bsf-event-card compact bsf-column">  
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