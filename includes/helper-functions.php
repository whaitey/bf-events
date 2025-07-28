<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}


// get event tags and calendar link

function bsf_get_event_tags_and_cal($postId, $buttonClass = '', $hideFeatured = false, $hideEventName = false, $hideStage = false, $hideTags = false, $hideCalendar = false ) {
  $postId = $postId;
  $tagList = '';
  $calendarHtml;
  $buttonClass = $buttonClass ? $buttonClass : '';

  // is it featured?
  
  $featured = carbon_get_post_meta( $postId, 'bsf_featured');

  if($featured && !$hideFeatured):
    $tagList .= '<span class="bsf-event-tag indigo featured ' . $buttonClass . '">' . __('Kiemelt', 'bsf-plugin') . '</span>';
  endif;

  // get the sub-event

  $eventTerms = get_the_terms($postId, 'bsf_main_event_name');
  $subEvents = array();

  if ($eventTerms && !is_wp_error($eventTerms) && !$hideEventName):

    foreach ($eventTerms as $term):
      if ($term->parent != 0) :
        $subEvents[] = $term;
      endif;
    endforeach;
  endif;

  if (!empty($subEvents) && !$hideEventName):
    $tagColor = carbon_get_term_meta( $subEvents[0]->term_id, 'bsf_event_name_color');
    $style;
    
    if($tagColor):
      $style = 'style="--tag-bg: ' . $tagColor . ';"';
    endif;

    $tagList .= '<span class="bsf-event-tag gold ' . $buttonClass . '" ' . $style . '>' . esc_html($subEvents[0]->name) . '</span>';
  endif;



  // get stage name

  $stageTerms = get_the_terms($postId, 'bsf_stage');

  if (!empty($stageTerms) && !$hideStage ):
    $tagColor = carbon_get_term_meta( $stageTerms[0]->term_id, 'bsf_event_name_color');
    $style;

    if($tagColor):
      $style = 'style="--tag-bg: ' . $tagColor . ';"';
    endif;

   $tagList .= '<a href="' . get_term_link($stageTerms[0]->term_id) . '" class="bsf-event-tag bsf-stage-link gold ' . $buttonClass . '" ' . $style . '>' . esc_html($stageTerms[0]->name) . '</a>';
  endif;

  // other tags

  $eventTagTerms = get_the_terms($postId, 'bsf_event_tag');

  if (!empty($eventTagTerms) && !$hideTags ):
    
    foreach ($eventTagTerms as $term):
        $tagList .= '<span class="bsf-event-tag white ' . $buttonClass . '">' . esc_html($term->name) . '</span>';
    endforeach;
    
  endif;
  

  // add to calendar link

  
  $calendarHtml = '';
  $html = '';
  $links = get_calendar_links($postId);

  if(!$hideCalendar):
    $calendarHtml = '<button class="bsf-button add-to-calendar ' . $buttonClass . '"><i></i><span class="label">' . __('Hozzáadás a naptáramhoz', 'bsf-plugin') . '</span></button>';
    $calendarHtml .= '<div class="add-to-calendar-popup"><div class="add-to-calendar-inner bsf-buttons-wrapper">
    <a class="add-to-calendar-button google bsf-button small white" href="' . esc_url($links['google']) . '" target="_blank" title="Google Calendar">Google</a>
    <a class="add-to-calendar-button outlook bsf-button small white" href="' . esc_url($links['outlook']) . '" target="_blank" title="Outlook">Outlook</a>
    <a class="add-to-calendar-button apple bsf-button small white" href="' . esc_url($links['apple']) . '" target="_blank" title="File letöltése">Apple/ICS</a>
  </div>
  </div>';
  endif;


  if(isset($calendarLink) || isset($tagList)):
    $html .= '<div class="bsf-tag-list">';
    $html .= $tagList;
    $html .= '</div>';
    $html .= $calendarHtml;

    return $html;
  endif;
}


function bsf_get_event_meta($postId) {
  // return event date, time and location
  $html = '';
  $date = carbon_get_post_meta($postId, 'bsf_date');
  
  $start = carbon_get_post_meta($postId, 'bsf_starting_time');
  $end = carbon_get_post_meta($postId, 'bsf_ending_time');
  $location = get_the_terms($postId, 'bsf_event_location');

  $start = $start ? date('H:i', strtotime($start)) : '';
  $end = $end ? date('H:i', strtotime($end)) : '';

  $html .= '<time datetime="' . $date . ' ' . $start . '">';

  //if(! empty($date)):
    //$html .= '<span class="date">' . $date . '</span>';
    //$html .= ' | ';
  //endif;

  if(isset($start) && isset($end)):
    $html .= '<span class="start-end">';
    $html .= $start;
    $html .= ' - ';
    $html .= $end;
    $html .= '</span>';
  endif;

  $html .= '</time>';

  if(isset($location) && $location != ''):
    $html .= '<p class="location">' . $location[0]->name . '</p>';
  endif;



  return $html;

}

function bsf_get_event_speakers($eventId) {
    $speakers = bsf_get_relevant_speakers($eventId);
    $moderators = bsf_get_relevant_moderators($eventId);
    $moderators = isset($moderators) && is_array($moderators) ? $moderators : [];
    if (!$speakers && !$moderators) return '';
    $speakers = array_diff($speakers, $moderators);
    $speakerNum = count($speakers);
    $maxSpeakerDisplayNum = 4;
    $html = '<div class="bsf-event-card-speakers">';
    // Speakers first
    $firstFourSpeakers = array_slice($speakers, 0, $maxSpeakerDisplayNum);
    if (!empty($firstFourSpeakers)) {
        $html .= '<div class="bsf-speaker-group speakers">';
        $html .= '<span class="bsf-speaker-group-title">' . esc_html__('Előadók:', 'bsf-plugin') . '</span>';
        $html .= '<div class="bsf-speaker-list">';
        foreach ($firstFourSpeakers as $speaker) {
            $html .= '<div class="bsf-speaker-card-compact">';
            $html .= '<div class="avatar">' . wp_get_attachment_image(carbon_get_post_meta($speaker, 'bsf_avatar'), 'bsf_speaker_avatar_small') . '</div>';
            $html .= '<div class="bsf-speaker-text">';
            $html .= '<p class="speaker-name">' . esc_html(carbon_get_post_meta($speaker, 'bsf_last_name')) . ' ' . esc_html(carbon_get_post_meta($speaker, 'bsf_first_name')) . '</p>';
            $title = carbon_get_post_meta($speaker, 'bsf_title');
            $company = carbon_get_post_meta($speaker, 'bsf_company');
            $company_title = $company;
            if ($company && $title) {
              $company_title = $company . ' - ' . $title;
            } elseif ($title) {
              $company_title = $title;
            }
            $html .= '<p class="speaker-title">' . esc_html($company_title) . '</p>';
            $html .= '</div>';
            $html .= '<a href="' . esc_url(get_permalink($speaker)) . '" class="bsf-speaker-card-link"></a>';
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
    }
    // Moderators second
    if (!empty($moderators)) {
        $html .= '<div class="bsf-speaker-group moderators">';
        $html .= '<span class="bsf-speaker-group-title">' . esc_html__('Moderátor:', 'bsf-plugin') . '</span>';
        $html .= '<div class="bsf-speaker-list">';
        foreach ($moderators as $moderator) {
            $html .= '<div class="bsf-speaker-card-compact">'; // removed 'moderator' class
            $html .= '<div class="avatar">' . wp_get_attachment_image(carbon_get_post_meta($moderator, 'bsf_avatar'), 'bsf_speaker_avatar_small') . '</div>';
            $html .= '<div class="bsf-speaker-text">';
            $html .= '<p class="speaker-name">' . esc_html(carbon_get_post_meta($moderator, 'bsf_last_name')) . ' ' . esc_html(carbon_get_post_meta($moderator, 'bsf_first_name')) . '</p>';
            $title = carbon_get_post_meta($moderator, 'bsf_title');
            $company = carbon_get_post_meta($moderator, 'bsf_company');
            $company_title = $company;
            if ($company && $title) {
              $company_title = $company . ' - ' . $title;
            } elseif ($title) {
              $company_title = $title;
            }
            $html .= '<p class="speaker-title">' . esc_html($company_title) . '</p>';
            $html .= '</div>';
            $html .= '<a href="' . esc_url(get_permalink($moderator)) . '" class="bsf-speaker-card-link"></a>';
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
    }
    if ($speakerNum > $maxSpeakerDisplayNum) {
        $queryString = 'admin-ajax.php?action=bsf_get_more_event_speakers';
        $queryString .= '&eventId=' . esc_attr($eventId);
        $queryString .= '&offset=' . esc_attr($maxSpeakerDisplayNum);
        $queryString .= '&nonce=' . esc_attr(wp_create_nonce('load_more_speakers'));
        $html .= '<div class="bsf-show-more-speakers bsf-buttons-wrapper" id="more-speakers-button-' . esc_attr($eventId) . '">';
        $html .= '<span class="bsf-cta-text-link bsf-more-speakers small" 
            hx-get="' . esc_url(admin_url($queryString)) . '"
            hx-swap="outerHTML"
            hx-target="#more-speakers-button-' . esc_attr($eventId) . '"
            >' . esc_html__('További előadók', 'bsf-plugin') . '</span>';
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
}

// getting all pages as options in carbon fields select

function bsf_get_all_pages_as_options() {
  $pages = get_pages();
  $options = [];

  foreach ($pages as $page) {
      $options[$page->ID] = $page->post_title;
  }

  return $options;
}

// daypicker filter

function bsf_get_daypicker_filter() {
  $filterhtml = '<div class="bsf-filter-widget daypicker">
          <h3 class="bsf-widget-title">
            Február 2025
          </h3>
          <div class="bsf-daypicker-days bsf-buttons-wrapper">
            <a class="bsf-daypicker-day bsf-button circular active">10</a>
            <a class="bsf-daypicker-day bsf-button circular">11</a>
            <a class="bsf-daypicker-day bsf-button circular">12</a>
            <a class="bsf-daypicker-day bsf-button circular">13</a>
          </div>
        </div>';
    
    return $filterhtml;
}



// display calendar page button

function bsf_display_calendar_page_button() {
  $calendarPageId = carbon_get_theme_option('bsf_calendar_page');
  $speakersPageId = carbon_get_theme_option('bsf_speakers_page');
  $buttons = '<div class="bsf-buttons-wrapper">';

  $buttons .= '<button type="button" class="bsf-button small indigo bsf-apply-filters-button">
        ' . __('Szűrők alkalmazása', 'bsf-plugin') . '
      </button>';

  if(isset($calendarPageId)):
    $url = get_permalink($calendarPageId);
    $buttons .= '<a class="bsf-button indigo bsf-switch-view-button" href="' . $url . '">
        ' . __('Órarend nézet', 'bsf-plugin') . '
      </a>';
  endif;

  if(isset($speakersPageId)):
    $url = get_permalink($speakersPageId);
    $buttons .='
      <a class="bsf-button indigo bsf-speakers-page-button" href="' . $url . '">' 
        . __('Összes előadó', 'bsf-plugin') .
      '</a>';
  endif;

  

  $buttons .= '</div>';

  echo $buttons;
}

// display filters button on mobile

function bsf_display_mobile_filters_button() {
  
  $button = '';
  
    $button = '<div class="bsf-buttons-wrapper bsf-filters-button-wrapper">
      <span role="button" class="bsf-button indigo bsf-show-filters-button">
        ' . __('Szűrés', 'bsf-plugin') . '
      </span>
    </div>';

  echo $button;
}

// display events page button

function bsf_display_events_page_button() {
  $eventsPageId = carbon_get_theme_option('bsf_main_event_page');
  $button = '';

  if(isset($eventsPageId)):
    $url = get_permalink($eventsPageId);
    $button = '<div class="bsf-buttons-wrapper">
      <a class="bsf-button indigo bsf-events-page-button" href="' . $url . '">
        <i></i>
        <span class="label">' . __(' VISSZA A LISTA NÉZETHEZ', 'bsf-plugin') . '</span>
      </a>
    </div>';
  endif;

  echo $button;
}

// display speakers page button

function bsf_display_speakers_filter_buttons() {
  $eventsPageId = carbon_get_theme_option('bsf_main_event_page');
  $button = '';

  if(isset($eventsPageId)):
    $url = get_permalink($eventsPageId);
    $button = '<div class="bsf-buttons-wrapper">
      <a class="bsf-button large indigo bsf-events-page-button bsf-switch-view-button" href="' . $url . '">
        ' . __(' LISTA NÉZET', 'bsf-plugin') . '
      </a>
      <button type="button" class="bsf-button small indigo bsf-apply-filters-button">
        ' . __('Szűrők alkalmazása', 'bsf-plugin') . '
      </button>
    </div>';
  endif;

  echo $button;
}

// display calendar page filters button on mobile

function bsf_display_calendar_filter_buttons() {
  $eventsPageId = carbon_get_theme_option('bsf_main_event_page');
  $button = '';

  if(isset($eventsPageId)):
    $url = get_permalink($eventsPageId);
    $button = '<div class="bsf-buttons-wrapper">
      <a class="bsf-button small white bsf-events-page-button" href="' . $url . '">
        ' . __(' LISTA NÉZET', 'bsf-plugin') . '
      </a>
      <button type="button" class="bsf-button small indigo bsf-apply-filters-button">
        ' . __('Szűrők alkalmazása', 'bsf-plugin') . '
      </button>
    </div>';
  endif;

  echo $button;
}


// display speakers page button

function bsf_display_speakers_page_button() {
  $speakersPageId = carbon_get_theme_option('bsf_speakers_page');
  $button = '';

  if(isset($speakersPageId)):
    $url = get_permalink($speakersPageId);
    $button = '<div class="bsf-buttons-wrapper">
      <a class="bsf-button indigo bsf-speakers-page-button" href="' . $url . '">' 
        . __('Összes előadó', 'bsf-plugin') .
      '</a>
    </div>';
  endif;

  echo $button;
}

// display single event banner tags

function bsf_get_single_event_banner_tags($postId) {
  $allTags = [];

  $stage = get_the_terms($postId, 'bsf_stage');

  foreach($stage as $tag):
    $allTags[] = $tag;
  endforeach;

  $html = '';

  if(!empty($allTags)):
    foreach($allTags as $tag):
      $html .= '<span class="bsf-tag bsf-button default gold">' . $tag->name . '</span>';
    endforeach;
  endif;

  return $html;
}

function bsf_get_event_person_ids() {
  $event_ids = get_posts([
    'post_type'      => 'bsf_event',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'post_status'    => 'publish',
  ]);

  $ids = [];

  foreach ($event_ids as $eid) {
    $speakers   = carbon_get_post_meta($eid, 'bsf_speakers');
    $moderators = carbon_get_post_meta($eid, 'bsf_moderators');

    $ids = array_merge(
      $ids,
      wp_list_pluck((array) $speakers, 'id'),
      wp_list_pluck((array) $moderators, 'id')
    );
  }

  $ids = array_unique(array_filter($ids));

  return $ids;
}

// get relevant events to a speaker

function bsf_get_relevant_events($speakerId) {

  // return ids
  $events = get_posts(array(
    'post_type' => 'bsf_event',
    'posts_per_page' => -1,
    'fields'          => 'ids',
    'meta_query' => array(
      'relation' => 'OR',
      array(
        'key' => 'bsf_speakers',
        'value' => $speakerId,
        'compare' => 'LIKE'
      ),
      array(
        'key' => 'bsf_moderators',
        'value' => $speakerId,
        'compare' => 'LIKE'
      )
    )
  ));
  
  //order them by starting time

  $orderedEvents = get_posts(array(
    'post_type' => 'bsf_event',
    'posts_per_page' => -1,
    'post__in' => $events,
    'orderby' => 'starting_time',
    'order' => 'asc',
    'meta_query' => array(
      'starting_time' => array(
            'key' => 'bsf_starting_time',
            'compare' => 'EXISTS',
        )
      )
    ));

  return $orderedEvents;
}

// get relevant speakers to an event

function bsf_get_relevant_speakers($eventId) {
  $speakers = carbon_get_post_meta($eventId, 'bsf_speakers');

  $post_ids = wp_list_pluck($speakers, 'id');
  
  return $post_ids;
}
// get relevant moderators to an event

function bsf_get_relevant_moderators($eventId) {
  $moderators = carbon_get_post_meta($eventId, 'bsf_moderators');

  $post_ids = wp_list_pluck($moderators, 'id');

  return $post_ids;}

// Find speaker IDs matching a search term by name or title
function bsf_search_speaker_ids($term) {
  global $wpdb;

  $term = trim($term);
  if ($term === '') {
    return [];
  }

  $like = '%' . $wpdb->esc_like($term) . '%';

  $sql = $wpdb->prepare(
    "SELECT p.ID
     FROM {$wpdb->posts} p
     LEFT JOIN {$wpdb->postmeta} fn ON p.ID = fn.post_id AND fn.meta_key = '_bsf_first_name'
     LEFT JOIN {$wpdb->postmeta} ln ON p.ID = ln.post_id AND ln.meta_key = '_bsf_last_name'
     LEFT JOIN {$wpdb->postmeta} ti ON p.ID = ti.post_id AND ti.meta_key = '_bsf_title'
     LEFT JOIN {$wpdb->postmeta} co ON p.ID = co.post_id AND co.meta_key = '_bsf_company'
     WHERE p.post_type = 'bsf_speaker' AND p.post_status = 'publish'
       AND (fn.meta_value LIKE %s OR ln.meta_value LIKE %s OR ti.meta_value LIKE %s OR co.meta_value LIKE %s)",
    $like,
    $like,
    $like,
    $like
  );

  return $wpdb->get_col($sql);
}

// Get IDs of events featuring any of the specified speakers
function bsf_get_event_ids_for_speakers($speaker_ids) {
    if (empty($speaker_ids)) return [];
    $event_ids = [];
    $events = get_posts([
        'post_type'      => 'bsf_event',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);
    foreach ($events as $event_id) {
        // Előadók
        $speakers = carbon_get_post_meta($event_id, 'bsf_speakers');
        foreach ((array)$speakers as $sp) {
            if (!empty($sp['id']) && in_array($sp['id'], $speaker_ids)) {
                $event_ids[] = $event_id;
                continue 2;
            }
        }
        // Moderátorok
        $moderators = carbon_get_post_meta($event_id, 'bsf_moderators');
        foreach ((array)$moderators as $mod) {
            if (!empty($mod['id']) && in_array($mod['id'], $speaker_ids)) {
                $event_ids[] = $event_id;
                continue 2;
            }
        }
    }
    return array_unique($event_ids);
}


// Search events by title, content, and descriptions
function bsf_search_event_ids($term) {
  $args = [
    'post_type'      => 'bsf_event',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    's'              => $term,
    'fields'         => 'ids',
    'meta_query'     => [
      'relation' => 'OR',
      [
        'key'     => '_bsf_description',
        'value'   => $term,
        'compare' => 'LIKE',
      ],
      [
        'key'     => '_bsf_short_description',
        'value'   => $term,
        'compare' => 'LIKE',
      ],
    ],
  ];

  $query = new WP_Query($args);
  $ids = $query->posts;
  wp_reset_postdata();
  return $ids;
}

// Search events connected to taxonomy terms containing the term
function bsf_search_taxonomy_event_ids($term) {
  $taxonomies = ['bsf_main_event_name', 'bsf_stage', 'bsf_event_location', 'bsf_event_tag'];
  $event_ids = [];
  foreach ($taxonomies as $tax) {
    $tids = get_terms([
      'taxonomy'   => $tax,
      'name__like' => $term,
      'fields'     => 'ids',
      'hide_empty' => false,
    ]);
    if (empty($tids) || is_wp_error($tids)) {
      continue;
    }
    $posts = get_posts([
      'post_type'      => 'bsf_event',
      'posts_per_page' => -1,
      'fields'         => 'ids',
      'tax_query'      => [
        [
          'taxonomy' => $tax,
          'field'    => 'term_id',
          'terms'    => $tids,
        ],
      ],
    ]);
    if ($posts) {
      $event_ids = array_merge($event_ids, $posts);
    }
  }
  return array_unique($event_ids);
}

function bsf_search_event_ids_full($search_query) {
    // 1. Esemény keresés cím, leírás, rövid leírás alapján
    $event_ids_by_title_desc = bsf_search_event_ids($search_query);

    // 2. Előadók keresése név/titulus alapján
    $speaker_ids = bsf_search_speaker_ids($search_query);

    // 3. Események keresése előadó/moderátor ID alapján (PHP-ban)
    $event_ids_by_speakers = bsf_get_event_ids_for_speakers($speaker_ids);

    // 4. Taxonómia keresés (ha kell)
    $event_ids_by_tax = bsf_search_taxonomy_event_ids($search_query);

    // 5. Eredmények egyesítése, duplikációk nélkül
    $search_event_ids = array_unique(array_merge(
        $event_ids_by_title_desc,
        $event_ids_by_speakers,
        $event_ids_by_tax
    ));

    return $search_event_ids;
}
