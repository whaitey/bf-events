<?php
// events page banner shortcode

add_shortcode('bsf_eventsbanner', 'bsf_display_events_banner');

function bsf_display_events_banner($atts = array()) {
     // set up default parameters
     $atts = shortcode_atts(array(
        'name' => false, // Default to false
    ), $atts);
 
    $shortcodeData = [];

    if($atts['name']):
        $taxonomies = ['bsf_main_event_name', 'bsf_stage'];
        $search_term = $atts['name'];

        foreach($taxonomies as $taxonomy):
            $term = get_term_by('slug', $search_term, $taxonomy );

            if($term):
                $shortcodeData = [
                    'title' => $term->name,
                ];

                $description = carbon_get_term_meta($term->term_id, 'bsf_event_name_banner_description');
        
                if(isset($description)):
                    $shortcodeData['description'] = $description;
                endif;
        
                $termLogos = carbon_get_term_meta($term->term_id, 'bsf_event_name_logos');
        
                if(isset($termLogos)):
                    $shortcodeData['logos'] = $termLogos;
                endif;
        
                $termColor = carbon_get_term_meta($term->term_id, 'bsf_event_name_color');
        
                if(isset($termColor)):
                    $shortcodeData['color'] = $termColor;
                endif;  
            endif;

        endforeach;


        // Start output buffering
        ob_start();

        include BSF_PLUGIN_DIR . 'partials/events-banner.php';

        // Return buffered output
        return ob_get_clean();

    endif;
}

// events page event list shortcode

add_shortcode('bsf_eventscontent', 'bsf_display_event_list');

function bsf_display_event_list($atts = array()) {
     // set up default parameters

     $atts = shortcode_atts(array(
        'featured' => false, // Default to false
    ), $atts);


    $featured = filter_var($atts['featured'], FILTER_VALIDATE_BOOLEAN); // Convert to true/false


    // Start output buffering
    ob_start();

   include BSF_PLUGIN_DIR . 'partials/events-content.php';

    // Return buffered output
    return ob_get_clean();
    
}



// calendar page banner shortcode

add_shortcode('bsf_calendarbanner', 'bsf_display_calendar_banner');

function bsf_display_calendar_banner() {

    $calendarPageId = carbon_get_theme_option('bsf_calendar_page');
    $shortcodeData = [];

    if(isset($calendarPageId)):
        $shortcodeData = [
            'title' => get_the_title($calendarPageId)
        ];

        $description = carbon_get_post_meta($calendarPageId, 'bsf_calendar_page_banner_description');
        
        if(isset($description)):
            $shortcodeData['description'] = $description;
        endif;
    endif;


    // Start output buffering
    ob_start();

    include BSF_PLUGIN_DIR . 'partials/calendar-banner.php';

    // Return buffered output
    return ob_get_clean();
    
}



// calendar page content shortcode

add_shortcode('bsf_calendarcontent', 'bsf_display_calendar_content');

function bsf_display_calendar_content($atts = array()) {
    // set up default parameters

    $atts = shortcode_atts(array(
       //'featured' => false, // Default to false
   ), $atts);


   // Start output buffering
   ob_start();

  include BSF_PLUGIN_DIR . 'partials/calendar-content.php';

   // Return buffered output
   return ob_get_clean();
   
}

// speakers page banner shortcode

add_shortcode('bsf_speakersbanner', 'bsf_display_speakers_banner');

function bsf_display_speakers_banner() {

    $speakersPageId = carbon_get_theme_option('bsf_speakers_page');
    $shortcodeData = [];

    if(isset($speakersPageId)):
        $shortcodeData = [
            'title' => get_the_title($speakersPageId)
        ];

        $description = carbon_get_post_meta($speakersPageId, 'bsf_speakers_page_banner_description');
        
        if(isset($description)):
            $shortcodeData['description'] = $description;
        endif;
    endif;    


    // Start output buffering
    ob_start();

    include BSF_PLUGIN_DIR . 'partials/speakers-banner.php';

    // Return buffered output
    return ob_get_clean();
    
}

// speakers content shortcode

add_shortcode('bsf_speakerscontent', 'bsf_display_speakers_content');

function bsf_display_speakers_content($atts = array()) {
    // set up default parameters

    $atts = shortcode_atts(array(
       'hide_filter' => false, // Default to false
       'columns' => 3
   ), $atts);

   extract($atts);

   // Start output buffering
   ob_start();

  include BSF_PLUGIN_DIR . 'partials/speakers-content.php';

   // Return buffered output
   return ob_get_clean();
   
}