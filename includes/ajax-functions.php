<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// filter events

function bsf_filter_events() {

  if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'filter_events')) {
    wp_send_json_error(['message' => 'Invalid nonce'], 403);
    wp_die();
  }

  if (!defined('DOING_AJAX') || !DOING_AJAX) {
      wp_send_json_error(['message' => 'Invalid request'], 403);
      wp_die();
  }

  $search_query = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';


  $featured = isset($_POST['featured']) ? intval($_POST['featured']) : 0;
  $page = isset($_POST['currentpage']) ? intval($_POST['currentpage']) : 1;
  $nextPage = $page + 1;

  $eventNames = isset($_POST['eventNamesArray']) ? array_map('intval', (array)$_POST['eventNamesArray']) : [];
  $stages = isset($_POST['stagesArray']) ? array_map('intval', (array)$_POST['stagesArray']) : [];
  $locations = isset($_POST['locationsArray']) ? array_map('intval', (array)$_POST['locationsArray']) : [];
  $tags = isset($_POST['tagsArray']) ? array_map('intval', (array)$_POST['tagsArray']) : [];
  $speakers = isset($_POST['speakersArray']) ? array_map('intval', (array)$_POST['speakersArray']) : [];
  $companies = isset($_POST['companiesArray']) ? (array)$_POST['companiesArray'] : [];

  // Remove empty values (including empty strings and zeros)
  $eventNames = array_filter($eventNames);
  $stages = array_filter($stages);
  $locations = array_filter($locations);
  $tags = array_filter($tags);
  $speakers = array_filter($speakers);

  // Initialize tax query
  $taxquery = ['relation' => 'AND'];

  // Only add tax queries if we have valid term IDs
  if (!empty($eventNames)) {
      $taxquery[] = [
          'taxonomy' => 'bsf_main_event_name',
          'field' => 'term_id',
          'terms' => $eventNames,
          'operator' => 'IN'
      ];
  }

  if (!empty($stages)) {
      $taxquery[] = [
          'taxonomy' => 'bsf_stage',
          'field' => 'term_id',
          'terms' => $stages,
          'operator' => 'IN'
      ];
  }

  if (!empty($locations)) {
      $taxquery[] = [
          'taxonomy' => 'bsf_event_location',
          'field' => 'term_id',
          'terms' => $locations,
          'operator' => 'IN'
      ];
  }

  if (!empty($tags)) {
      $taxquery[] = [
          'taxonomy' => 'bsf_event_tag',
          'field' => 'term_id',
          'terms' => $tags,
          'operator' => 'IN'
      ];
  }

  // If all tax arrays are empty, set taxquery to empty array
  if (empty($eventNames) && empty($stages) && empty($locations) && empty($tags)) {
      $taxquery = [];
  }



  $meta_query = array(
    'relation' => 'AND',

    'starting_time' => array(
        'key' => '_bsf_starting_time',
        'compare' => 'EXISTS',
    )
  );

if (!empty($speakers)) {
    $speaker_meta_or = ['relation' => 'OR'];
    foreach ($speakers as $sid) {
      $speaker_meta_or[] = [
        'key' => 'bsf_speakers',
        'value' => $sid,
        'compare' => 'LIKE'
      ];
      $speaker_meta_or[] = [
        'key' => 'bsf_moderators',
        'value' => $sid,
        'compare' => 'LIKE'
      ];
    }
    $meta_query[] = $speaker_meta_or;
  }

  $search_event_ids = [];

  if ($featured && empty($eventNames) && empty($stages) && empty($locations) && empty($tags) && empty($search_query)) {
    $meta_query[] = array(
        'key' => '_bsf_featured',
        'value' => 'yes',
        'compare' => '='
    );
  }

  if (!empty($search_query)) {
    $search_event_ids = bsf_search_event_ids_full($search_query);
}

  // If companies are selected, find all speaker IDs with those companies
  $company_speaker_ids = [];
  if (!empty($companies)) {
    $args = array(
      'post_type' => 'bsf_speaker',
      'posts_per_page' => -1,
      'post_status' => 'publish',
      'fields' => 'ids',
      'meta_query' => array(
        'relation' => 'OR',
        array_map(function($company) {
          return array(
            'key' => 'bsf_company',
            'value' => $company,
            'compare' => 'LIKE',
          );
        }, $companies)
      ),
    );
    $company_speaker_ids = get_posts($args);
  }

  // If company_speaker_ids is set, filter events to only those with these speakers/moderators
  $event_ids_by_company = [];
  if (!empty($company_speaker_ids)) {
    $all_event_ids = get_posts([
      'post_type'      => 'bsf_event',
      'posts_per_page' => -1,
      'fields'         => 'ids',
      'post_status'    => 'publish',
    ]);
    foreach ($all_event_ids as $event_id) {
      $speakers = bsf_get_relevant_speakers($event_id);
      $moderators = bsf_get_relevant_moderators($event_id);
      $all_people = array_merge((array)$speakers, (array)$moderators);
      if (array_intersect($company_speaker_ids, $all_people)) {
        $event_ids_by_company[] = $event_id;
      }
    }
  }

  $company = isset($_POST['company']) ? sanitize_text_field($_POST['company']) : '';

  if (!empty($company)) {
    $meta_query[] = array(
      'key' => 'bsf_company',
      'value' => $company,
      'compare' => 'LIKE',
    );
  }

  
  $query_args = array(
      'post_type' => 'bsf_event',
      'posts_per_page' => 30,
      'paged' => $page,
      'post_status' => 'publish',
      'meta_key' => '_bsf_featured',
      'orderby' => array(
          'meta_value' => 'DESC',
          'starting_time' => 'ASC'
      ),
      'order' => 'asc',
      'meta_query' => $meta_query,
      'tax_query' => $taxquery,
      's' => $search_query
    );

  if (!empty($search_query)) {
    $query_args['post__in'] = !empty($search_event_ids) ? $search_event_ids : array(0);
  }

  // If filtering by company, add post__in to query args
  if (!empty($event_ids_by_company)) {
    $query_args['post__in'] = $event_ids_by_company;
  } else if (!empty($search_event_ids)) {
    $query_args['post__in'] = $search_event_ids;
  } else {
    // If neither company nor search filters are active, do not set post__in (show all events)
    unset($query_args['post__in']);
  }

  $eventsQuery = new WP_Query($query_args);

if ($eventsQuery->have_posts()) {
    error_log('Események a lekérdezésben: ' . print_r($eventsQuery->posts, true));
}


  if ( $eventsQuery->have_posts() ) :								      
      while ( $eventsQuery->have_posts() ) : $eventsQuery->the_post();
        global $post;
        setup_postdata($post);
        include BSF_PLUGIN_DIR . 'partials/event-card.php';
    
      endwhile;
      wp_reset_postdata();
  else:
    echo '<h3 class="bsf-no-hits">' . __('Nem található a feltételeknek megfelelő esemény.', 'bsf-plugin') . '</h3>';
  endif;

  if($eventsQuery->max_num_pages > $page) :
    
    $queryString = 'admin-ajax.php?action=bsf_filter_events';
    $queryString .= '&nonce=' . wp_create_nonce('filter_events');
    $hx_vals = json_encode(['currentpage' => $nextPage]);
    
    $button = '<div class="bsf-buttons-wrapper center" id="bsf-load-more-events-wrapper">
        <button 
        class="bsf-button indigo" 
        id="bsf-events-load-more-button"
        hx-post="' . esc_url(admin_url($queryString)) . '"
        hx-vals=' . $hx_vals . '
        hx-include="#bsf-sidebar-filter"
        hx-swap="outerHTML"
        hx-target="#bsf-load-more-events-wrapper"
        >
        ' . __('Több esemény betöltése', 'bsf-plugin') . '
      </button>
    </div>';
    
    echo $button;
  endif;

  wp_die();
}

add_action('wp_ajax_bsf_filter_events', 'bsf_filter_events');
add_action('wp_ajax_nopriv_bsf_filter_events', 'bsf_filter_events');

// load event description
function bsf_get_event_description() {
  if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'load_description')) {
    wp_send_json_error(['message' => 'Invalid nonce'], 403);
    wp_die();
  }

  if (!defined('DOING_AJAX') || !DOING_AJAX) {
    wp_send_json_error(['message' => 'Invalid request'], 403);
    wp_die();
  }

  $eventId = intval(sanitize_text_field($_GET['eventId'] ?? ''));
  $eventDesc = wpautop(carbon_get_post_meta($eventId, 'bsf_description'));
  $eventUrl = get_permalink($eventId);

  $descHtml = '<div class="bsf-text bsf-event-card-long-description">' .
    $eventDesc .
    '<div class="bsf-buttons-wrapper">
      <a href="' . $eventUrl . '" class="bsf-button small indigo">' . __('Esemény megtekintése', 'bsf-plugin') . '</a>    
    </div>
  </div>';

  echo $descHtml;

  wp_die();
}

add_action('wp_ajax_bsf_get_event_description', 'bsf_get_event_description');
add_action('wp_ajax_nopriv_bsf_get_event_description', 'bsf_get_event_description');


//load more speakers on the event card
function bsf_get_more_event_speakers(){
  if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'load_more_speakers')) {
    wp_send_json_error(['message' => 'Invalid nonce'], 403);
    wp_die();
  }

  if (!defined('DOING_AJAX') || !DOING_AJAX) {
    wp_send_json_error(['message' => 'Invalid request'], 403);
    wp_die();
  }

  $eventId = intval(sanitize_text_field($_GET['eventId'] ?? ''));
  $offset = intval(sanitize_text_field($_GET['offset'] ?? ''));
  
  $html = '';
  $speakers = carbon_get_post_meta($eventId, 'bsf_speakers');
  $lastSpeakers = array_slice($speakers, $offset);



  foreach($lastSpeakers as $speaker):
    
		$html .= '<div class="bsf-speaker-card-compact">';
		$html .= '<div class="avatar">';
		$html .= wp_get_attachment_image(carbon_get_post_meta($speaker['id'], 'bsf_avatar'), 'bsf_speaker_avatar_small');
    $html .= '</div>';
    $html .='<div class="bsf-speaker-text">';
    $html .= '<p class="speaker-name">' . carbon_get_post_meta($speaker['id'], 'bsf_last_name') . ' ' . carbon_get_post_meta($speaker['id'], 'bsf_first_name') . '</p>';
    $title = carbon_get_post_meta($speaker['id'], 'bsf_title');
    $company = carbon_get_post_meta($speaker['id'], 'bsf_company');
    $company_title = $company;
    if ($company && $title) {
      $company_title = $company . ' - ' . $title;
    } elseif ($title) {
      $company_title = $title;
    }
    $html .= '<p class="speaker-title">' . esc_html($company_title) . '</p>';
		$html .= '</div>';
		$html .= '<a href="' . get_permalink($speaker['id']) . '" class="bsf-speaker-card-link"></a>';
		$html .= '</div>';
  endforeach;

  

  echo $html;

  wp_die();
}

add_action('wp_ajax_bsf_get_more_event_speakers', 'bsf_get_more_event_speakers');
add_action('wp_ajax_nopriv_bsf_get_more_event_speakers', 'bsf_get_more_event_speakers');


// filter speakers

function bsf_filter_speakers() {

  if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'filter_speakers')) {
    wp_send_json_error(['message' => 'Invalid nonce'], 403);
    wp_die();
  }

  if (!defined('DOING_AJAX') || !DOING_AJAX) {
    wp_send_json_error(['message' => 'Invalid request'], 403);
    wp_die();
  }

  $search_query = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
  $eventNames = $_POST['eventNamesArray'];
  $stages = $_POST['stagesArray'];
  $page = isset($_POST['currentpage']) ? intval($_POST['currentpage']) : 1;
  $nextPage = $page + 1;
  $company = isset($_POST['company']) ? sanitize_text_field($_POST['company']) : '';

  $taxquery = [];

  if (!empty($eventNames)) {
    $taxquery[] = [
      'taxonomy' => 'bsf_main_event_name',
      'field' => 'term_id',
      'terms' => $eventNames
    ];
  }

  if (!empty($stages)) {
    $taxquery[] = [
      'taxonomy' => 'bsf_stage',
      'field' => 'term_id',
      'terms' => $stages
    ];
  }

  $eventsQuery = new WP_Query([
    'post_type' => 'bsf_event',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'tax_query' => $taxquery,
    'fields' => 'ids'
  ]);

  $person_ids = [];

  if ($eventsQuery->have_posts()) {
    foreach ($eventsQuery->posts as $event_id) {

      $speakers = carbon_get_post_meta($event_id, 'bsf_speakers');
      if (!empty($speakers)) {
        foreach ($speakers as $speaker) {
          if (!empty($speaker['id'])) {
            $person_ids[$speaker['id']] = true;
          }
        }
      }

      $moderators = carbon_get_post_meta($event_id, 'bsf_moderators');
      if (!empty($moderators)) {
        foreach ($moderators as $moderator) {
          if (!empty($moderator['id'])) {
            $person_ids[$moderator['id']] = true;
          }
        }
      }
    }

    $person_ids = array_keys($person_ids);
  }

  $meta_query = [
    'relation' => 'AND',
  ];
  if (!empty($company)) {
    $meta_query[] = [
      'key' => 'bsf_company',
      'value' => $company,
      'compare' => 'LIKE',
    ];
  }

  $speakersQuery = new WP_Query([
    'post_type' => 'bsf_speaker',
    'posts_per_page' => 30,
    'post_status' => 'publish',
    'post__in' => $person_ids,
    'paged' => $page,
    'orderby' => 'meta_value',
    'meta_key' => '_bsf_last_name',
    'order' => 'ASC',
    'meta_query' => array_merge([
      'relation' => 'OR',
      [
        'key' => 'bsf_first_name',
        'value' => $search_query,
        'compare' => 'LIKE',
      ],
      [
        'key' => 'bsf_last_name',
        'value' => $search_query,
        'compare' => 'LIKE',
      ],
      [
        'key' => 'bsf_speaker_description',
        'value' => $search_query,
        'compare' => 'LIKE',
      ],
      [
        'key' => 'bsf_title',
        'value' => $search_query,
        'compare' => 'LIKE',
      ]
    ], $meta_query),
  ]);

  if ($speakersQuery->have_posts()) {
    while ($speakersQuery->have_posts()) {
      $speakersQuery->the_post();
      global $post;
      setup_postdata($post);
      include BSF_PLUGIN_DIR . 'partials/speaker-card.php';
    }
    wp_reset_postdata();
  } else {
    echo '<h3 class="bsf-no-hits">' . __('Nem található a feltételeknek megfelelő személy.', 'bsf-plugin') . '</h3>';
  }

  if ($speakersQuery->max_num_pages > $page) {
    $queryString = 'admin-ajax.php?action=bsf_filter_speakers';
    $queryString .= '&nonce=' . wp_create_nonce('filter_speakers');
    $hx_vals = json_encode(['currentpage' => $nextPage]);

    $button = '<div class="bsf-buttons-wrapper center" id="bsf-load-more-speakers-wrapper">
      <button 
        class="bsf-button indigo" 
        id="bsf-speakers-load-more-button"
        hx-post="' . esc_url(admin_url($queryString)) . '"
        hx-vals=' . $hx_vals . '
        hx-include="#bsf-sidebar-filter"
        hx-swap="outerHTML"
        hx-target="#bsf-load-more-speakers-wrapper"
      >
        ' . __('Több előadó betöltése', 'bsf-plugin') . '
      </button>
    </div>';

    echo $button;
  }

  wp_die();
}

add_action('wp_ajax_bsf_filter_speakers', 'bsf_filter_speakers');
add_action('wp_ajax_nopriv_bsf_filter_speakers', 'bsf_filter_speakers');


// load page banner on events page


function bsf_load_banner() {
  // Security checks
  if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'load_banner')) {
    status_header(403);
    die('Invalid nonce');
  }

  if (!defined('DOING_AJAX') || !DOING_AJAX) {
      status_header(403);
      die('Invalid request');
  }

  $eventNames = isset($_POST['eventNamesArray']) ? array_map('intval', (array)$_POST['eventNamesArray']) : [];
  $stages = isset($_POST['stagesArray']) ? array_map('intval', (array)$_POST['stagesArray']) : [];

  $filteredEventNames = array_filter($eventNames);
  $filteredStages = array_filter($stages);

  // Special case: Empty filters - return HTTP 204 (No Content)
  if (empty($filteredStages) && empty($filteredEventNames)) {
      status_header(204); // Critical - tells HTMX to ignore response
      die();
  }

  // Normal processing for valid filters
  if (!empty($filteredStages) && empty($filteredEventNames)) {
      $term_id = reset($filteredStages);
      $taxonomy = 'bsf_stage';
  } else {
      $term_id = reset($filteredEventNames);
      $taxonomy = 'bsf_main_event_name';
  }

  $term = get_term($term_id, $taxonomy);
  if (!$term) {
      status_header(204); // Term not found - no update
      die();
  }

  // Prepare data
  $shortcodeData = ['title' => $term->name];
  
  if ($description = carbon_get_term_meta($term_id, 'bsf_event_name_banner_description')) {
      $shortcodeData['description'] = $description;
  }
  
  if ($termLogos = carbon_get_term_meta($term_id, 'bsf_event_name_logos')) {
      $shortcodeData['logos'] = $termLogos;
  }
  
  if ($termColor = carbon_get_term_meta($term_id, 'bsf_event_name_color')) {
      $shortcodeData['color'] = $termColor;
  }

  // Directly include the template (HTMX expects raw HTML)
  include BSF_PLUGIN_DIR . 'partials/events-banner.php';
  die();
}

add_action('wp_ajax_bsf_load_banner', 'bsf_load_banner');
add_action('wp_ajax_nopriv_bsf_load_banner', 'bsf_load_banner');

// Reset banner to original Main Event title
function bsf_reset_banner() {
    // Security checks
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reset_banner')) {
        status_header(403);
        die('Invalid nonce');
    }

    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        status_header(403);
        die('Invalid request');
    }

    // Get the main events page ID
    $mainEventPageId = carbon_get_theme_option('bsf_main_event_page');
    
    if (!$mainEventPageId) {
        status_header(204);
        die();
    }

    // Get the page title as the default banner title
    $pageTitle = get_the_title($mainEventPageId);
    
    // Prepare default banner data
    $shortcodeData = [
        'title' => $pageTitle,
        'description' => '',
        'logos' => null
    ];

    // Directly include the template (HTMX expects raw HTML)
    include BSF_PLUGIN_DIR . 'partials/events-banner.php';
    die();
}

add_action('wp_ajax_bsf_reset_banner', 'bsf_reset_banner');
add_action('wp_ajax_nopriv_bsf_reset_banner', 'bsf_reset_banner');