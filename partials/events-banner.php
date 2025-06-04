<div class="bsf-parent-class bsf-page-banner-outer-wrapper bsf-events-banner" id="main-events-banner">
  <div class="bsf-events-banner">
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

        <?php if($shortcodeData['logos']): ?>
          <div class="bsf-logos-wrapper">
            <?php foreach($shortcodeData['logos'] as $logo): ?>
              <div class="bsf-logo-wrapper">
                <?php echo wp_get_attachment_image($logo['image'], 'bsf_events_banner_logo'); ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      
    </div>
  </div>
</div>


