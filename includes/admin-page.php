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

// Add image regeneration submenu
add_action('admin_menu', 'bsf_add_image_regeneration_menu');

function bsf_add_image_regeneration_menu() {
    add_submenu_page(
        'bsf-events-main-page',
        __('Kép minőség javítása', 'bsf_plugin'),
        __('Kép minőség javítása', 'bsf_plugin'),
        'manage_options',
        'bsf-image-regeneration',
        'bsf_image_regeneration_page'
    );
}

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
      ->add_tab( __('Megjelenés', 'bsf_plugin'), array(
            Field::make( 'color', 'bsf_main_color', __('Fő szín', 'bsf_plugin'))
            ->set_default_value('#2F24A1')
            ->set_help_text(__('Ez a szín lesz használva a gombok, kiemelések és egyéb elemek színezésére', 'bsf_plugin')),
            
            Field::make( 'image', 'bsf_banner_background', __('Banner háttérkép', 'bsf_plugin'))
            ->set_help_text(__('Ez a kép lesz megjelenítve a banner háttérében', 'bsf_plugin')),
            
        ) )
      ;
});

// Image regeneration page callback
function bsf_image_regeneration_page() {
    // Handle AJAX requests
    if (isset($_POST['action']) && $_POST['action'] === 'bsf_regenerate_single_image') {
        bsf_handle_single_image_regeneration();
        return;
    }
    
    // Get all speakers with images
    $speakers = get_posts(array(
        'post_type' => 'bsf_speaker',
        'numberposts' => -1,
        'post_status' => 'publish'
    ));
    
    $speakers_with_images = array();
    foreach ($speakers as $speaker) {
        $avatar_id = carbon_get_post_meta($speaker->ID, 'bsf_avatar');
        if ($avatar_id) {
            $speakers_with_images[] = array(
                'id' => $speaker->ID,
                'name' => $speaker->post_title,
                'avatar_id' => $avatar_id,
                'current_size' => bsf_get_image_size_info($avatar_id, 'bsf_speakers_list_avatar')
            );
        }
    }
    
    ?>
    <div class="wrap">
        <h1><?php _e('Kép minőség javítása', 'bsf_plugin'); ?></h1>
        
        <div class="notice notice-info">
            <p><?php _e('Ez az eszköz segít javítani az előadói képek minőségét. A képek egyenként, biztonságosan lesznek újragenerálva.', 'bsf_plugin'); ?></p>
        </div>
        
        <?php if (empty($speakers_with_images)): ?>
            <div class="notice notice-warning">
                <p><?php _e('Nem található előadó kép.', 'bsf_plugin'); ?></p>
            </div>
        <?php else: ?>
            <div class="bsf-image-regeneration-container">
                <h2><?php _e('Előadói képek', 'bsf_plugin'); ?> (<?php echo count($speakers_with_images); ?>)</h2>
                
                <div class="bsf-speakers-grid">
                    <?php foreach ($speakers_with_images as $speaker): ?>
                        <div class="bsf-speaker-item" data-speaker-id="<?php echo $speaker['id']; ?>" data-avatar-id="<?php echo $speaker['avatar_id']; ?>">
                            <div class="bsf-speaker-image">
                                <?php echo wp_get_attachment_image($speaker['avatar_id'], 'thumbnail'); ?>
                            </div>
                            <div class="bsf-speaker-info">
                                <h3><?php echo esc_html($speaker['name']); ?></h3>
                                <p class="bsf-current-size">
                                    <?php if ($speaker['current_size']): ?>
                                        <?php echo esc_html($speaker['current_size']); ?>
                                    <?php else: ?>
                                        <?php _e('Kép méret: Ismeretlen', 'bsf_plugin'); ?>
                                    <?php endif; ?>
                                </p>
                                <button class="button button-primary bsf-regenerate-btn" data-speaker-id="<?php echo $speaker['id']; ?>" data-avatar-id="<?php echo $speaker['avatar_id']; ?>">
                                    <?php _e('Újragenerálás', 'bsf_plugin'); ?>
                                </button>
                                <div class="bsf-regeneration-status" style="display: none;">
                                    <span class="spinner is-active"></span>
                                    <span class="status-text"><?php _e('Újragenerálás...', 'bsf_plugin'); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <style>
                .bsf-speakers-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                    gap: 20px;
                    margin-top: 20px;
                }
                .bsf-speaker-item {
                    border: 1px solid #ddd;
                    padding: 15px;
                    border-radius: 5px;
                    background: #fff;
                }
                .bsf-speaker-image {
                    text-align: center;
                    margin-bottom: 10px;
                }
                .bsf-speaker-image img {
                    border-radius: 5px;
                    max-width: 100px;
                    height: auto;
                }
                .bsf-speaker-info h3 {
                    margin: 0 0 10px 0;
                    font-size: 16px;
                }
                .bsf-current-size {
                    color: #666;
                    font-size: 12px;
                    margin: 5px 0 15px 0;
                }
                .bsf-regeneration-status {
                    margin-top: 10px;
                    color: #0073aa;
                }
                .bsf-regeneration-status.success {
                    color: #46b450;
                }
                .bsf-regeneration-status.error {
                    color: #dc3232;
                }
            </style>
            
            <script>
            jQuery(document).ready(function($) {
                $('.bsf-regenerate-btn').on('click', function() {
                    var $btn = $(this);
                    var $item = $btn.closest('.bsf-speaker-item');
                    var $status = $item.find('.bsf-regeneration-status');
                    var speakerId = $btn.data('speaker-id');
                    var avatarId = $btn.data('avatar-id');
                    
                    // Disable button and show status
                    $btn.prop('disabled', true);
                    $status.show().removeClass('success error');
                    
                    // Send AJAX request
                    $.post(ajaxurl, {
                        action: 'bsf_regenerate_single_image',
                        speaker_id: speakerId,
                        avatar_id: avatarId,
                        nonce: '<?php echo wp_create_nonce('bsf_regenerate_image'); ?>'
                    }, function(response) {
                        if (response.success) {
                            $status.addClass('success').find('.status-text').text('Sikeres újragenerálás!');
                            // Update image
                            $item.find('.bsf-speaker-image img').attr('src', response.data.new_image_url);
                            // Update size info
                            $item.find('.bsf-current-size').text(response.data.new_size);
                        } else {
                            $status.addClass('error').find('.status-text').text('Hiba: ' + response.data);
                        }
                    }).fail(function() {
                        $status.addClass('error').find('.status-text').text('Hálózati hiba történt.');
                    }).always(function() {
                        $btn.prop('disabled', false);
                    });
                });
            });
            </script>
        <?php endif; ?>
    </div>
    <?php
}

// Helper function to get image size information
function bsf_get_image_size_info($attachment_id, $size_name) {
    $image_data = wp_get_attachment_image_src($attachment_id, $size_name);
    if ($image_data) {
        return sprintf(__('Kép méret: %dx%d px', 'bsf_plugin'), $image_data[1], $image_data[2]);
    }
    return false;
}

// Handle single image regeneration via AJAX
function bsf_handle_single_image_regeneration() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'bsf_regenerate_image')) {
        wp_send_json_error('Invalid nonce');
        return;
    }
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }
    
    $speaker_id = intval($_POST['speaker_id']);
    $avatar_id = intval($_POST['avatar_id']);
    
    // Verify the avatar belongs to the speaker
    $actual_avatar_id = carbon_get_post_meta($speaker_id, 'bsf_avatar');
    if ($actual_avatar_id != $avatar_id) {
        wp_send_json_error('Avatar ID mismatch');
        return;
    }
    
    // Regenerate the image
    if (function_exists('wp_generate_attachment_metadata')) {
        $attachment_data = wp_get_attachment_metadata($avatar_id);
        if ($attachment_data) {
            $new_metadata = wp_generate_attachment_metadata($avatar_id, get_attached_file($avatar_id));
            wp_update_attachment_metadata($avatar_id, $new_metadata);
            
            // Get new image URL and size info
            $new_image_url = wp_get_attachment_image_url($avatar_id, 'bsf_speakers_list_avatar');
            $new_size_info = bsf_get_image_size_info($avatar_id, 'bsf_speakers_list_avatar');
            
            wp_send_json_success(array(
                'new_image_url' => $new_image_url,
                'new_size' => $new_size_info
            ));
        } else {
            wp_send_json_error('Could not get attachment metadata');
        }
    } else {
        wp_send_json_error('WordPress image functions not available');
    }
}