<div class="bsf-parent-class bsf-speaker-page-banner-outer-wrapper bsf-single-speaker-banner">
  <div class="bsf-container">
    <div class="bsf-page-banner-content">
      <?php if(carbon_get_post_meta(get_the_ID(),'bsf_avatar')): ?>
        <div class="bsf-speaker-avatar">
          <?php echo wp_get_attachment_image(carbon_get_post_meta(get_the_ID(),'bsf_avatar'), 'bsf_speakers_single_avatar'); ?>
        </div>
      <?php endif; ?>
      <div class="bsf-banner-inner-content">
        <div class="bsf-banner-text">
          <h1 class="bsf-banner-heading">
            <?php echo carbon_get_post_meta(get_the_ID(),'bsf_last_name'); ?> <?php echo carbon_get_post_meta(get_the_ID(),'bsf_first_name'); ?>
          </h1>
          <div class="bsf-page-banner-description bsf-text">
            <?php 
              $title = carbon_get_post_meta(get_the_ID(),'bsf_title');
              $company = carbon_get_post_meta(get_the_ID(),'bsf_company');
              $company_title = $company;
              if ($company && $title) {
                $company_title = $company . ' - ' . $title;
              } elseif ($title) {
                $company_title = $title;
              }
              echo esc_html($company_title);
            ?>
          </div>
        </div>
        <?php if(carbon_get_post_meta(get_the_ID(),'bsf_contact_info')): ?>
          <div class="bsf-speaker-social-contacts bsf-buttons-wrapper">
            <?php foreach(carbon_get_post_meta(get_the_ID(),'bsf_contact_info') as $contact): ?>
              <a href="<?php echo $contact['url']; ?>" class="bsf-button indigo" target="_blank">
                <?php echo $contact['label']; ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>