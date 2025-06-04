<div class="bsf-parent-class bsf-page-banner-outer-wrapper bsf-calendar-banner">
  <div class="bsf-container">
    <div class="bsf-page-banner-content">
      <div class="bsf-banner-text">
        <h1 class="bsf-banner-heading">
          <?php echo esc_html($shortcodeData['title']); ?>
        </h1>
        <div class="bsf-page-banner-description bsf-text">
          <?php echo wpautop(wp_kses_post($shortcodeData['description'])); ?>
        </div>
      </div>
    </div>
  </div>
</div>