<?php
/**
 * Plugin Name: BF Events
 * Description: Teljes körű eseménykezelő rendszer WordPress plugin
 * Version: 1.2.0
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

