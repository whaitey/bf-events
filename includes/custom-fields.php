<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;


add_action('carbon_fields_register_fields', function () {
    // event fields
    Container::make('post_meta', __('Programpont adatai', 'bsf-plugin'))
        ->where('post_type', '=', 'bsf_event')
        ->add_tab( __('Alap adatok', 'bsf-plugin'), array(
            Field::make( 'checkbox', 'bsf_featured', __('Kiemelt', 'bsf-plugin'))->set_width(15),
            Field::make( 'date', 'bsf_date', __('Esemény napja', 'bsf-plugin'))->set_width(85)
            ->set_picker_options(array(
                'dateFormat' => "Y-m-d",
                'altInput' => true,
                'altFormat' => "Y-m-d"
            )),
            Field::make( 'time', 'bsf_starting_time', __('Kezdés', 'bsf-plugin'))->set_width(50)
            ->set_picker_options(array(
                'enableTime' => true,
                'noCalendar' => true,
                'dateFormat' => "H:i",
                'enableSeconds' => false,
                'defaultHour' => 8,
                'time_24hr' => true,
                'altInput' => true,
                'altFormat' => "H:i"
            )),
            Field::make( 'time', 'bsf_ending_time', __('Befejezés', 'bsf-plugin'))
            ->set_width(50)
            ->set_picker_options(array(
                'enableTime' => true,
                'noCalendar' => true,
                'dateFormat' => "H:i",
                'enableSeconds' => false,
                'defaultHour' => 8,
                'time_24hr' => true,
                'altInput' => true,
                'altFormat' => "H:i"
            )),
        ) )
        ->add_tab(__('Leírás', 'bsf-plugin'), array(
            Field::make('rich_text', 'bsf_description', __('Leírás', 'bsf-plugin')),
            Field::make('rich_text', 'bsf_short_description', __('Rövid leírás', 'bsf-plugin')),
        ) )
        ->add_tab(__('Előadók', 'bsf-plugin'), array(
            Field::make('association', 'bsf_speakers', __('Előadók', 'bsf-plugin'))->set_types(array( array( 'type' => 'post', 'post_type' => 'bsf_speaker' ) )),
            Field::make('association', 'bsf_moderators', __('Moderátorok', 'bsf-plugin'))->set_types(array( array( 'type' => 'post', 'post_type' => 'bsf_speaker' ) )),
        ) );
    
    // speaker fields
    Container::make('post_meta', __('Előadó adatai', 'bsf-plugin'))
        ->where('post_type', '=', 'bsf_speaker')
        ->add_tab( __('Alap adatok', 'bsf-plugin'), array(
            Field::make('image', 'bsf_avatar', __('Fotó', 'bsf-plugin'))->set_width(20),
            Field::make('text', 'bsf_last_name', __('Vezetéknév', 'bsf-plugin'))->set_width(40),
            Field::make('text', 'bsf_first_name', __('Keresztnév', 'bsf-plugin'))->set_width(40),
            Field::make('text', 'bsf_company', __('Cég', 'bsf-plugin')),
            Field::make('text', 'bsf_title', __('Tisztség, pozíció', 'bsf-plugin')),
        ) )
        ->add_tab( __('Kapcsolati információ', 'bsf-plugin'), array(
            Field::make( 'complex', 'bsf_contact_info', __('Kapcsolati adatok', 'bsf-plugin'))->set_max(3)
            ->add_fields( array(
                Field::make( 'text', 'label', __('Felirat', 'bsf-plugin') )->set_width(50),
                Field::make( 'text', 'url' )->set_width(50),
            ) )
        ) )
        ->add_tab( __('Leírás', 'bsf-plugin'), array(
            Field::make('rich_text', 'bsf_speaker_description', __('Leírás', 'bsf-plugin')),
        ) );

    // main/sub event name taxonomy fields

    Container::make('term_meta', 'Category Properties')
    ->show_on_taxonomy(array('bsf_main_event_name', 'bsf_stage'))
    ->add_fields(array(
        Field::make('rich_text', 'bsf_event_name_banner_description', __('Leírás', 'bsf-plugin')),
        Field::make('color', 'bsf_event_name_color', __('Szín', 'bsf-plugin')),
        Field::make( 'complex', 'bsf_event_name_logos', __('Logok', 'bsf-plugin'))->add_fields( 'logo', array(
            Field::make( 'image', 'image', __('Logo', 'bsf-plugin') )->set_width(30),
            Field::make( 'text', 'caption', __('Felirat', 'bsf-plugin') )->set_width(70),
        ) )
    ));

    // calendar view page fields

    $calendarPageId = carbon_get_theme_option('bsf_calendar_page');

    Container::make('post_meta', __('Órarend oldal adatai', 'bsf-plugin'))
        ->show_on_page($calendarPageId)
        ->add_fields(array(
            Field::make('rich_text', 'bsf_calendar_page_banner_description', __('Leírás', 'bsf-plugin'))
        ));

    // all speakers page fields

    $speakersPageId = carbon_get_theme_option('bsf_speakers_page');

    Container::make('post_meta', __('Előadók oldal adatai', 'bsf-plugin'))
        ->show_on_page($speakersPageId)
        ->add_fields(array(
            Field::make('rich_text', 'bsf_speakers_page_banner_description', __('Leírás', 'bsf-plugin'))
        ));

});
