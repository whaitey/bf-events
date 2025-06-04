<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// calendar stages filter

function bsf_get_calendar_stages_filter() {
  $queryString = 'admin-ajax.php?action=bsf_filter_calendar_stages';
  $queryString .= '&nonce=' . wp_create_nonce('filter_stages');

  $stages = get_terms( array(
    'taxonomy'   => 'bsf_stage',
    'hide_empty' => true,
  ) );

  $filterhtml = '<div class="bsf-filter-widget stages-filter">
    <h3 class="bsf-widget-title">' . __('Színpad', 'bsf-plugin') . '<span id="filters-mobile-close"></span></h3>
    <form method="POST" id="bsf-calendar-filter"
      hx-post="' . esc_url(admin_url($queryString)) . '" 
      hx-target=".bsf-stages-moving-wrapper"
      hx-trigger="change, load"
      hx-indicator=".bsf-stages-moving-wrapper"
    >
    <div class="bsf-stages-filter-wrapper bsf-buttons-wrapper">
      <button type="button" class="bsf-stage-tag bsf-button small gold" id="bsf-reset-stages">' . __('Összes színpad', 'bsf-plugin') . '</button>';

    if(isset($stages)):
      foreach($stages as $stage):
        $filterhtml .= '<input 
          type="checkbox" 
          name="stagesArray[]" 
          value="' . $stage->term_id . '" 
          id="bsf-stage-input-' . $stage->term_id . '"
        >';
        $filterhtml .= '<label class="bsf-stage-tag bsf-button small outline-black" for="bsf-stage-input-' . $stage->term_id . '">';
        $filterhtml .= $stage->name;
        $filterhtml .= '</label>';
      endforeach;
    endif;

  $filterhtml .= '</div>
  </form>
  </div>';


  return $filterhtml;
}

// get time column

function bsf_get_time_column() {
  $calendarDayStart = carbon_get_theme_option('bsf_day_start') ? carbon_get_theme_option('bsf_day_start') : 0;
  $calendarDayEnd = carbon_get_theme_option('bsf_day_end') ? carbon_get_theme_option('bsf_day_end') : 23;
  $numberOfHours = $calendarDayEnd - $calendarDayStart;

  for ($i = $calendarDayStart; $i < $calendarDayEnd; $i++) {
    echo '<div class="hour-unit"><span class="value">' . sprintf('%02d', $i) . ':00</span></div>';
  }
}

// ajax filtering

function bsf_filter_calendar_stages() {

  if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'filter_stages')) {
    wp_send_json_error(['message' => 'Invalid nonce'], 403);
    wp_die();
  }

  if (!defined('DOING_AJAX') || !DOING_AJAX) {
      wp_send_json_error(['message' => 'Invalid request'], 403);
      wp_die();
  }

  $stages = $_POST['stagesArray'];
  $html = '';
  $eventshtml = '';

  $calendarDayStart = carbon_get_theme_option('bsf_day_start') ? carbon_get_theme_option('bsf_day_start') : 0;
  $calendarDayEnd = carbon_get_theme_option('bsf_day_end') ? carbon_get_theme_option('bsf_day_end') : 23;


    $allStages = get_terms( array(
      'taxonomy'   => 'bsf_stage',
      'hide_empty' => true,
      'orderby' => 'term_id',
      'order' => 'ASC',
      'include' => $stages
    ) );



    foreach($allStages as $stageColumn):
      //getting the relevant event cards per stage column
      $eventsHtml = '';

      $eventsPerStage = get_posts(array(
        'post_type' => 'bsf_event',
        'posts_per_page' => -1,
        'orderby' => 'starting_time',
        'order' => 'asc',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'bsf_starting_time',
                'compare' => 'EXISTS',
            ),
            array(
                'key' => 'bsf_starting_time',
                'value' => $calendarDayStart,
                'compare' => '>=', // Greater than or equal to
                'type' => 'NUMERIC', // Important for numeric comparisons
            ),
            array(
                'key' => 'bsf_starting_time',
                'value' => $calendarDayEnd,
                'compare' => '<', 
                'type' => 'NUMERIC',
            ),
            array(
                'key' => 'bsf_ending_time',
                'value' => $calendarDayEnd,
                'compare' => '<=', 
                'type' => 'NUMERIC',
            ),
        ),
        'tax_query' => array(
          array(
              'taxonomy' => 'bsf_stage',
              'field' => 'term_id',
              'include_children' => false,
              'terms' => $stageColumn -> term_id,
          ),
        ),
      ));

      if($eventsPerStage):
        foreach($eventsPerStage as $event):
          $eventsHtml .= bsf_get_calendar_event_card($event -> ID);
        endforeach;
      endif;
      
      $html .= '<div class="bsf-single-stage-column">
              <div class="bsf-calendar-stage-label">
                <div class="label-inner"><p class="label">' . $stageColumn -> name . '</p></div>
              </div>
              <div class="bsf-stage-wrapper" id="stage-' . $stageColumn -> ID . '">';
      $html .= $eventsHtml;
      $html .= '</div>
            </div>';

    endforeach;
  
  echo $html;
  wp_die();
}

add_action('wp_ajax_bsf_filter_calendar_stages', 'bsf_filter_calendar_stages');
add_action('wp_ajax_nopriv_bsf_filter_calendar_stages', 'bsf_filter_calendar_stages');

// load event card in the modal ajax function

function bsf_load_calendar_event_card() {
   // Verify nonce for security
   if (!isset($_GET['post_id']) || !isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'load_modal_event')) {
    wp_send_json_error(['message' => 'Invalid request']);
    wp_die();
  }

  $post_id = intval($_GET['post_id']);
  global $post; 
  $post = get_post($post_id);

  if (!$post) {
    wp_send_json_error(['message' => 'Event not found']);
    wp_die();
  }

  setup_postdata($post);

  // Include the template for the modal content
  ob_start();
  include BSF_PLUGIN_DIR . 'partials/event-card.php';
  $content = ob_get_clean();

  wp_reset_postdata();

  echo $content;
  wp_die();
}

add_action('wp_ajax_bsf_load_calendar_event_card', 'bsf_load_calendar_event_card');
add_action('wp_ajax_nopriv_bsf_load_calendar_event_card', 'bsf_load_calendar_event_card');

// helper function to get the single cards into the calendar, with height and position calculation

function bsf_get_calendar_event_card($eventId) {
  $calendarDayStart = carbon_get_theme_option('bsf_day_start') ? carbon_get_theme_option('bsf_day_start') : 0;
  $calendarDayEnd = carbon_get_theme_option('bsf_day_end') ? carbon_get_theme_option('bsf_day_end') : 23;
  $numberOfHours = $calendarDayEnd - $calendarDayStart;

  $dayInMinutes = $numberOfHours * 60;
  $dayStartInMinutes = $calendarDayStart * 60;
  $dayEndInMinutes = $calendarDayEnd * 60;

  $eventStart = carbon_get_post_meta($eventId, 'bsf_starting_time');
  $eventEnd = carbon_get_post_meta($eventId, 'bsf_ending_time');

  $displayStart = $eventStart ? date('H:i', strtotime($eventStart)) : '';
  $displayEnd = $eventEnd ? date('H:i', strtotime($eventEnd)) : '';

  $start_timestamp = strtotime($eventStart);
  $end_timestamp = strtotime($eventEnd);

  $start_total_minutes = date('H', $start_timestamp) * 60 + date('i', $start_timestamp);
  $end_total_minutes = date('H', $end_timestamp) * 60 + date('i', $end_timestamp);

  // getting the top position(percentage of a day)

  $topPosition = ($start_total_minutes - $dayStartInMinutes) / $dayInMinutes * 100 . '%';

  // getting the height(percentage of a day)

  $height = ($end_total_minutes - $start_total_minutes) / $dayInMinutes * 100 . '%';

  $cardHtml = '<div class="event-card" id="event-card-' . $eventId . '" style="top: ' . $topPosition . '; height: ' . $height . ';">';
  $cardHtml .= '<div class="event-card-inner">';
  $cardHtml .= '<p class="event-text">';
  $cardHtml .= '<span class="time">' . $displayStart . ' - ' . $displayEnd . '</span>' . ' ' . get_the_title($eventId);
  $cardHtml .= '</p>';
  $cardHtml .= '</div>';
  $cardHtml .= '<a href="#" class="bsf-open-modal event-card-link"
  hx-get="' . esc_url(admin_url('admin-ajax.php?action=bsf_load_calendar_event_card&post_id=' . $eventId . '&nonce=' . wp_create_nonce('load_modal_event'))) . '"
  hx-target="#bsf-modal-content"
  hx-trigger="click"
  hx-swap="innerHTML"></a>';
  $cardHtml .= '</div>';

  return $cardHtml;
}