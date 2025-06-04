<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

function bsf_register_post_types(){
  /* events */
  $event_args = array(
    'labels' => [
            'name'          => __('Programpontok', 'bsf-plugin'),
            'singular_name' => __('Programpont', 'bsf-plugin'),
        ],
    'public'        => true,
    'has_archive'   => true,
    'menu_position'  => null,
    'show_in_menu' => 'bsf-events-main-page',
    'supports'      =>array('title')
  );

  register_post_type('bsf_event', $event_args);

  /* speakers */
  $speaker_args = array(
    'labels' => [
            'name'          => __('Előadók', 'bsf-plugin'),
            'singular_name' => __('Előadó', 'bsf-plugin'),
        ],
    'public'        => true,
    'has_archive'   => true,
    'menu_position'  => null,
    'show_in_menu' => 'bsf-events-main-page',
    'supports'      =>array('title', 'thumbnail')
  );

  register_post_type('bsf_speaker', $speaker_args);


 }
 add_action('init', 'bsf_register_post_types');