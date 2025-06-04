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
  
	if(!$speakers) return;
  $speakers = array_diff($speakers, $moderators);

  $speakerNum = count($speakers);
  $maxSpeakerDisplayNum = 4;

	$html = '<div class="bsf-event-card-speakers">';

  foreach($moderators as $moderator):
    $html .= '<div class="bsf-speaker-card-compact moderator">';
		$html .= '<div class="avatar">';
		$html .= wp_get_attachment_image(carbon_get_post_meta($moderator, 'bsf_avatar'), 'bsf_speaker_avatar_small');
    $html .= '</div>';
    $html .='<div class="bsf-speaker-text">';
    $html .= '<p class="speaker-name">' . carbon_get_post_meta($moderator, 'bsf_last_name') . ' ' . carbon_get_post_meta($moderator, 'bsf_first_name') . '</p>';
		$html .= '<p class="speaker-title">' . carbon_get_post_meta($moderator, 'bsf_title') . '</p>';
		$html .= '</div>';
		$html .= '<a href="' . get_permalink($moderator) . '" class="bsf-speaker-card-link"></a>';
		$html .= '</div>';
  endforeach;

  /*var_dump($moderatorIds);
  echo '<br><br>';
  var_dump($speakers);*/


	$firstFourSpeakers = array_slice($speakers, 0, $maxSpeakerDisplayNum);

	foreach($firstFourSpeakers as $speaker):
		$html .= '<div class="bsf-speaker-card-compact">';
		$html .= '<div class="avatar">';
		$html .= wp_get_attachment_image(carbon_get_post_meta($speaker, 'bsf_avatar'), 'bsf_speaker_avatar_small');
    $html .= '</div>';
    $html .='<div class="bsf-speaker-text">';
    $html .= '<p class="speaker-name">' . carbon_get_post_meta($speaker, 'bsf_last_name') . ' ' . carbon_get_post_meta($speaker, 'bsf_first_name') . '</p>';
		$html .= '<p class="speaker-title">' . carbon_get_post_meta($speaker, 'bsf_title') . '</p>';
		$html .= '</div>';
		$html .= '<a href="' . get_permalink($speaker) . '" class="bsf-speaker-card-link"></a>';
		$html .= '</div>';
  endforeach;

  if($speakerNum > $maxSpeakerDisplayNum):
    $queryString = 'admin-ajax.php?action=bsf_get_more_event_speakers';
    $queryString .= '&eventId=' . $eventId;
    $queryString .= '&offset=' . $maxSpeakerDisplayNum;
    $queryString .= '&nonce=' . wp_create_nonce('load_more_speakers');
  
    $html .= '<div class="bsf-show-more-speakers bsf-buttons-wrapper" id="more-speakers-button-' . $eventId . '">
      <span class="bsf-cta-text-link bsf-more-speakers small" 
        hx-get="' . esc_url(admin_url($queryString)) . '"
        hx-swap="outerHTML"
        hx-target="#more-speakers-button-' . $eventId . '"
        >' .
        __('További előadók', 'bsf-plugin')
      . '</span>
    </div>';
  endif;

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
  
  return $post_ids;
}