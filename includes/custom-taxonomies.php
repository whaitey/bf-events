<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

function bsf_register_taxonomies(){
  // main event
  register_taxonomy('bsf_main_event_name', 'bsf_event', [
    'labels' => [
        'name'              => __('Események', 'bsf-plugin'),
        'singular_name'     => __('Esemény', 'bsf-plugin'),
        'search_items'      => __('Események keresése', 'bsf-plugin'),
        'all_items'         => __('Összes esemény', 'bsf-plugin'),
        'edit_item'         => __('Esemény szerkesztése', 'bsf-plugin'),
        'update_item'       => __('Esemény frissítése', 'bsf-plugin'),
        'add_new_item'      => __('Új esemény létrehozása', 'bsf-plugin'),
        'new_item_name'     => __('Új esemény neve', 'bsf-plugin'),
        'menu_name'         => __('Események', 'bsf-plugin'),
    ],
    'public'            => true,
    'hierarchical'      => true, 
    'show_admin_column' => true,
    'show_ui'           => true,
    'show_in_menu'      => false,
    'query_var'         => true,
    'rewrite'           => ['slug' => 'main-event-name'],
  ]);

  // event tags
  register_taxonomy('bsf_event_tag', 'bsf_event', [
    'labels' => [
        'name'              => __('Címkék', 'bsf-plugin'),
        'singular_name'     => __('Címke', 'bsf-plugin'),
        'search_items'      => __('Címkék keresése', 'bsf-plugin'),
        'all_items'         => __('Összes címke', 'bsf-plugin'),
        'edit_item'         => __('Címke szerkesztése', 'bsf-plugin'),
        'update_item'       => __('Címke frissítése', 'bsf-plugin'),
        'add_new_item'      => __('Új címke létrehozása', 'bsf-plugin'),
        'new_item_name'     => __('Új címke neve', 'bsf-plugin'),
        'menu_name'         => __('Címkék', 'bsf-plugin'),
    ],
    'public'            => true,
    'hierarchical'      => true, 
    'show_admin_column' => true,
    'show_ui'           => true,
    'show_in_menu'      => false,
    'query_var'         => true,
    'rewrite'           => ['slug' => 'event-tag'],
  ]);

  // locations
  register_taxonomy('bsf_event_location', 'bsf_event', [
    'labels' => [
        'name'              => __('Helyszínek', 'bsf-plugin'),
        'singular_name'     => __('Helyszín', 'bsf-plugin'),
        'search_items'      => __('Helyszínek keresése', 'bsf-plugin'),
        'all_items'         => __('Összes helyszín', 'bsf-plugin'),
        'edit_item'         => __('Helyszín szerkesztése', 'bsf-plugin'),
        'update_item'       => __('Helyszín frissítése', 'bsf-plugin'),
        'add_new_item'      => __('Új helyszín létrehozása', 'bsf-plugin'),
        'new_item_name'     => __('Új helyszín neve', 'bsf-plugin'),
        'menu_name'         => __('Helyszínek', 'bsf-plugin'),
    ],
    'public'            => true,
    'hierarchical'      => true, 
    'show_admin_column' => true,
    'show_ui'           => true,
    'show_in_menu'      => false,
    'query_var'         => true,
    'rewrite'           => ['slug' => 'event-location'],
  ]);

  // stages

  register_taxonomy('bsf_stage', 'bsf_event', [
    'labels' => [
        'name'              => __('Színpadok', 'bsf-plugin'),
        'singular_name'     => __('Színpad', 'bsf-plugin'),
        'search_items'      => __('Színpadok keresése', 'bsf-plugin'),
        'all_items'         => __('Összes színpad', 'bsf-plugin'),
        'edit_item'         => __('Színpad szerkesztése', 'bsf-plugin'),
        'update_item'       => __('Színpad frissítése', 'bsf-plugin'),
        'add_new_item'      => __('Új színpad létrehozása', 'bsf-plugin'),
        'new_item_name'     => __('Új színpad neve', 'bsf-plugin'),
        'menu_name'         => __('Színpadok', 'bsf-plugin'),
    ],
    'public'            => true,
    'hierarchical'      => true, 
    'show_admin_column' => true,
    'show_ui'           => true,
    'show_in_menu'      => false,
    'query_var'         => true,
    'rewrite'           => ['slug' => 'stage'],
  ]);
}

add_action('init', 'bsf_register_taxonomies');