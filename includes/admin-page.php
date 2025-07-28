<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Create main admin menu
function custom_plugin_admin_menu() {
  add_menu_page(
      'BSF Events',
      'BSF Events',
      'manage_options',
      'bsf-events-main-page',
      'bsf_events_dashboard_page_callback',
      'dashicons-calendar'
  );
}
add_action('admin_menu', 'custom_plugin_admin_menu');

function bsf_events_dashboard_page_callback() {
  echo '<div class="wrap"><h1>Custom Plugin Dashboard</h1><p>Welcome to the Custom Plugin.</p></div>';
}


function bsf_add_taxonomies_to_menu() {
  add_submenu_page(
      'bsf-events-main-page', 
      'Események',
      'Események',
      'manage_options',
      'edit-tags.php?taxonomy=bsf_main_event_name&post_type=bsf_event'
  );

  add_submenu_page(
      'bsf-events-main-page', 
      'Címkék',
      'Címkék',
      'manage_options',
      'edit-tags.php?taxonomy=bsf_event_tag&post_type=bsf_event'
  );

  add_submenu_page(
      'bsf-events-main-page', 
      'Helyszínek',
      'Helyszínek',
      'manage_options',
      'edit-tags.php?taxonomy=bsf_event_location&post_type=bsf_event'
  );

  add_submenu_page(
      'bsf-events-main-page', 
      'Színpadok',
      'Színpadok',
      'manage_options',
      'edit-tags.php?taxonomy=bsf_stage&post_type=bsf_event'
  );
}
add_action('admin_menu', 'bsf_add_taxonomies_to_menu');

// Register Carbon Fields settings page inside plugin menu
add_action('carbon_fields_register_fields', function () {
  Container::make('theme_options', __('Beállítások', 'bsf_plugin'))
      ->set_page_parent('bsf-events-main-page') // Attach to menu
      ->add_tab( __('Oldalak', 'bsf_plugin'), array(
            Field::make( 'select', 'bsf_main_event_page', __('Események oldal', 'bsf_plugin'))
            ->set_options(bsf_get_all_pages_as_options())
            ->set_required(true),
            Field::make( 'select', 'bsf_calendar_page', __('Órarend oldal', 'bsf_plugin'))
            ->set_options(bsf_get_all_pages_as_options())
            ->set_required(true),
            Field::make( 'select', 'bsf_speakers_page', __('Előadók oldal', 'bsf_plugin'))
            ->set_options(bsf_get_all_pages_as_options())
            ->set_required(true),
            
        ) )
      ->add_tab( __('Órarend nézet', 'bsf_plugin'), array(
            Field::make( 'number', 'bsf_day_start', __('Nap kezdete', 'bsf_plugin'))
            ->set_required(true)
            ->set_width(50)
            ->set_min(0)
            ->set_max(23),
            Field::make( 'number', 'bsf_day_end', __('Nap vége', 'bsf_plugin'))
            ->set_required(true)
            ->set_width(50)
            ->set_min(0)
            ->set_max(23)

            
        ) )
      ->add_tab( __('Szűrők', 'bsf_plugin'), array(
            Field::make( 'checkbox', 'bsf_show_event_filter', __('Alesemény szűrő megjelenítése', 'bsf_plugin'))
            ->set_default_value(true)
            ->set_help_text(__('Bekapcsolja vagy kikapcsolja az alesemény szűrőt a frontend oldalon', 'bsf_plugin')),
            
            Field::make( 'checkbox', 'bsf_show_stage_filter', __('Színpad szűrő megjelenítése', 'bsf_plugin'))
            ->set_default_value(true)
            ->set_help_text(__('Bekapcsolja vagy kikapcsolja a színpad szűrőt a frontend oldalon', 'bsf_plugin')),
            
            Field::make( 'checkbox', 'bsf_show_location_filter', __('Helyszín szűrő megjelenítése', 'bsf_plugin'))
            ->set_default_value(true)
            ->set_help_text(__('Bekapcsolja vagy kikapcsolja a helyszín szűrőt a frontend oldalon', 'bsf_plugin')),
            
            Field::make( 'checkbox', 'bsf_show_speaker_filter', __('Előadó szűrő megjelenítése', 'bsf_plugin'))
            ->set_default_value(true)
            ->set_help_text(__('Bekapcsolja vagy kikapcsolja az előadó szűrőt a frontend oldalon', 'bsf_plugin')),
            
            Field::make( 'checkbox', 'bsf_show_company_filter', __('Cég szűrő megjelenítése', 'bsf_plugin'))
            ->set_default_value(true)
            ->set_help_text(__('Bekapcsolja vagy kikapcsolja a cég szűrőt a frontend oldalon', 'bsf_plugin')),
            
            Field::make( 'checkbox', 'bsf_show_tag_filter', __('Címke szűrő megjelenítése', 'bsf_plugin'))
            ->set_default_value(true)
            ->set_help_text(__('Bekapcsolja vagy kikapcsolja a címke szűrőt a frontend oldalon', 'bsf_plugin')),
            
            Field::make( 'checkbox', 'bsf_show_past_events_filter', __('Korábbi programok szűrő megjelenítése', 'bsf_plugin'))
            ->set_default_value(true)
            ->set_help_text(__('Bekapcsolja vagy kikapcsolja a korábbi programok szűrőt a frontend oldalon', 'bsf_plugin')),
            
        ) )
      ;
});