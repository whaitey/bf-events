<?php
/**
 * Plugin Name: BF Events
 * Description: Teljes körű eseménykezelő rendszer WordPress plugin
 * Version: 1.3.6
 * Author: ZeusWeb
 * Plugin URI: https://github.com/whaitey/bf-events
 * GitHub Plugin URI: https://github.com/whaitey/bf-events
 * GitHub Branch: main-2
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin Update Checker
require_once __DIR__ . '/load-v5p6.php';
use YahnisElsts\PluginUpdateChecker\v5p6\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/whaitey/bf-events',
    __FILE__,
    'bf-events'
);

// Include Carbon Fields(and other vendors if needed)

define('BSF_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once BSF_PLUGIN_DIR . 'vendor/autoload.php';

// Load Carbon Fields
add_action('after_setup_theme', function () {
    	\Carbon_Fields\Carbon_Fields::boot();
});

// Load scripts and styles
function bsf_events_enqueue_assets() {
    wp_enqueue_style('bsf-events-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('bsf-events-htmx', plugin_dir_url(__FILE__) . 'assets/vendor/htmx.org@2.0.3/htmx.min.js', [], null, true);
    wp_enqueue_script('bsf-events-scripts', plugin_dir_url(__FILE__) . 'assets/js/scripts.js', ['bsf-events-htmx'], null, true);

    wp_localize_script('bsf-events-scripts', 'bsfEventsAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('filter_events'),
    ]);
}
add_action('wp_enqueue_scripts', 'bsf_events_enqueue_assets');

// Generate and enqueue dynamic CSS with custom colors
function bsf_events_enqueue_dynamic_css() {
    $main_color = carbon_get_theme_option('bsf_main_color');
    
    // If no custom color is set, use the default
    if (empty($main_color)) {
        $main_color = '#2F24A1';
    }
    
    // Generate darker and lighter variants of the main color
    $main_color_rgb = sscanf($main_color, "#%02x%02x%02x");
    $darker_color = sprintf("#%02x%02x%02x", 
        max(0, $main_color_rgb[0] - 30), 
        max(0, $main_color_rgb[1] - 30), 
        max(0, $main_color_rgb[2] - 30)
    );
    
    $lighter_color = sprintf("#%02x%02x%02x", 
        min(255, $main_color_rgb[0] + 30), 
        min(255, $main_color_rgb[1] + 30), 
        min(255, $main_color_rgb[2] + 30)
    );
    
    // Generate CSS with custom color variables
    $custom_css = "
        :root {
            --c-indigo: {$main_color};
            --c-purple-3: {$darker_color};
            --c-purple-2: {$lighter_color};
        }
    ";
    
    // Enqueue the dynamic CSS
    wp_add_inline_style('bsf-events-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'bsf_events_enqueue_dynamic_css', 20);


// Include functions, filters, menus, etc
require_once BSF_PLUGIN_DIR . 'includes/admin-page.php';
require_once BSF_PLUGIN_DIR . 'includes/custom-post-types.php';
require_once BSF_PLUGIN_DIR . 'includes/custom-taxonomies.php';
require_once BSF_PLUGIN_DIR . 'includes/custom-fields.php';
require_once BSF_PLUGIN_DIR . 'includes/shortcodes.php';
require_once BSF_PLUGIN_DIR . 'includes/filters.php';
require_once BSF_PLUGIN_DIR . 'includes/helper-functions.php';
require_once BSF_PLUGIN_DIR . 'includes/ajax-functions.php';
require_once BSF_PLUGIN_DIR . 'includes/calendar-functions.php';
require_once BSF_PLUGIN_DIR . 'includes/add-to-calendar.php';

// thumbnail sizes

add_image_size( 'bsf_speaker_avatar_small', 58, 58, true );
add_image_size( 'bsf_events_banner_logo', 150, 70, true );
add_image_size( 'bsf_speakers_list_avatar', 272, 272, true );
add_image_size( 'bsf_speakers_single_avatar', 320, 320, true );

