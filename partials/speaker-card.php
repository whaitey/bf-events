<div class="bsf-speaker-card bsf-column">
  <?php 
      $avatar = carbon_get_post_meta($post->ID, 'bsf_avatar');
      $firstname = carbon_get_post_meta($post->ID, 'bsf_first_name');
      $lastname = carbon_get_post_meta($post->ID, 'bsf_last_name');
      $title = carbon_get_post_meta($post->ID, 'bsf_title');
      $company = carbon_get_post_meta(get_the_ID(), 'bsf_company');
      $company_title = $company;
      if ($company && $title) {
        $company_title = $company . ' - ' . $title;
      } elseif ($title) {
        $company_title = $title;
      }
  ?>

  <div class="avatar">
    <?php echo wp_get_attachment_image($avatar, 'bsf_speakers_list_avatar'); ?>
  </div>
  <div class="card-content">
    <h4 class="speaker-name">
      <?php echo $lastname . ' ' . $firstname; ?>
    </h4>
    <p class="speaker-title"><?php echo esc_html($company_title); ?></p>
  </div>
  <a href="<?php the_permalink(); ?>" class="speaker-card-link"></a>
</div>