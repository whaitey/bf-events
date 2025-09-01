<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}


// adding custom submenus to the main menu item
function bsf_events_keep_menu_open($parent_file) {
    global $submenu_file, $current_screen;

    // List of taxonomies that should stay under the custom menu
    $custom_taxonomies = ['bsf_event_tag', 'bsf_event_location', 'bsf_main_event_name', 'bsf_stage']; 

    if ($current_screen->base === 'edit-tags' && in_array($current_screen->taxonomy, $custom_taxonomies)) {
      $parent_file  = 'bsf-events-main-page'; // Keep the main menu open
      $submenu_file = 'edit-tags.php?taxonomy=' . $current_screen->taxonomy . '&post_type=bsf_event'; // Highlight the submenu dynamically
    }

  return $parent_file;
}
add_filter('parent_file', 'bsf_events_keep_menu_open');


// add custom post, taxonomy and archive page into the template hierarchy from within the plugin

add_filter('template_include', function ($template) {
  $custom_templates = [
      'single'   => 'single-%s.php',
      'archive'  => 'archive-%s.php',
      'taxonomy' => 'taxonomy-%s.php'
  ];

  foreach ($custom_templates as $type => $file_pattern) {
      if (
          ($type === 'single' && is_singular()) ||
          ($type === 'archive' && is_post_type_archive()) ||
          ($type === 'taxonomy' && is_tax())
      ) {
          $object = get_queried_object();

          if ($object) {
              $slug = ($type === 'taxonomy') ? $object->taxonomy : $object->post_type;
              $plugin_template = BSF_PLUGIN_DIR . 'templates/' . sprintf($file_pattern, $slug);

              if (file_exists($plugin_template)) {
                  return $plugin_template;
              }
          }
      }
  }

  return $template;
});

// Generate clean permalinks for single bsf_event posts at the root (no base)
add_filter('post_type_link', function ($post_link, $post, $leavename) {
  if ($post->post_type === 'bsf_event' && $post->post_status === 'publish') {
    return home_url('/' . $post->post_name . '/');
  }
  return $post_link;
}, 10, 3);

// Add rewrite rule to resolve root-level slugs to bsf_event singles
add_action('init', function () {
  // Let existing pages/posts/taxonomies take precedence
  add_rewrite_rule('^([^/]+)/?$', 'index.php?post_type=bsf_event&name=$matches[1]', 'bottom');
});


add_action( 'pre_get_posts', function( $q )
{
    if( $title = $q->get( '_meta_or_title' ) )
    {
        add_filter( 'get_meta_sql', function( $sql ) use ( $title )
        {
            global $wpdb;

            // Only run once:
            static $nr = 0; 
            if( 0 != $nr++ ) return $sql;

            // Modified WHERE
            $sql['where'] = sprintf(
                " AND ( %s OR %s ) ",
                $wpdb->prepare( "{$wpdb->posts}.post_title like '%%%s%%'", $title),
                mb_substr( $sql['where'], 5, mb_strlen( $sql['where'] ) )
            );

            return $sql;
        });
    }
});