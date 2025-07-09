<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {
    if (isset($_GET['download_ical'])) {
        $event_id = intval($_GET['download_ical']);
        $event = get_post($event_id);

        // Get event meta
        $date = carbon_get_post_meta($event_id, 'bsf_date');
        $start_time = carbon_get_post_meta($event_id, 'bsf_starting_time') ?: '00:00:00';
        $end_time = carbon_get_post_meta($event_id, 'bsf_ending_time') ?: '23:59:59';
        $description = carbon_get_post_meta($event_id, 'bsf_description');

        $stageTerms = wp_get_post_terms(
            $event_id,
            'bsf_stage',
            array(
                'fields' => 'names'
            )
        );

        $locationTerms = wp_get_post_terms(
            $event_id,
            'bsf_event_location',
            array(
                'fields' => 'names'
            )
        );

        $stageInfo = '';
        if (isset($stageTerms[0])) {
            $stageInfo = $stageTerms[0];
        }
        if ($stageInfo && isset($locationTerms[0])) {
            $stageInfo .= ' (' . $locationTerms[0] . ')';
        }

        $speakerIds = bsf_get_relevant_speakers($event_id);
        $speakerNames = [];
        if ($speakerIds) {
            foreach ($speakerIds as $sid) {
                $speakerNames[] = carbon_get_post_meta($sid, 'bsf_last_name') . ' ' . carbon_get_post_meta($sid, 'bsf_first_name');
            }
        }

        if ($stageInfo) {
            $description .= "\n" . sprintf(__('Sz\xEDnpad: %s', 'bsf-plugin'), $stageInfo);
        }
        if ($speakerNames) {
            $description .= "\n" . sprintf(__('El\x0151ad\xF3: %s', 'bsf-plugin'), implode(', ', $speakerNames));
        }

        $eventTerms = wp_get_post_terms(
            $event_id,
            'bsf_main_event_name',
            array(
                'parent' => 0,
                'fields' => 'names'
            )
        );

        $locationTerms = wp_get_post_terms(
            $event_id,
            'bsf_event_location',
            array(
                'parent' => 0,
                'fields' => 'names'
            )
        );

        $location = '';
        if (isset($eventTerms[0]) && $eventTerms[0] != '') {
            $location = $eventTerms[0];
        }

        if (isset($locationTerms[0]) && $locationTerms[0] != '') {
            $location .= ' - ' . $locationTerms[0];
        }

        // Convert to Budapest time
        $timezone = new DateTimeZone('Europe/Budapest');
        $dt_start = new DateTime("$date $start_time", $timezone);
        $dt_end = new DateTime("$date $end_time", $timezone);

        // Convert to UTC clones for .ics
        $dt_start_utc = clone $dt_start;
        $dt_start_utc->setTimezone(new DateTimeZone('UTC'));
        $dt_end_utc = clone $dt_end;
        $dt_end_utc->setTimezone(new DateTimeZone('UTC'));

        // iCal output
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="event-' . $event_id . '.ics"');

        echo "BEGIN:VCALENDAR\r\n";
        echo "VERSION:2.0\r\n";
        echo "PRODID:-//Your Site//Events//EN\r\n";
        echo "CALSCALE:GREGORIAN\r\n";
        echo "BEGIN:VEVENT\r\n";
        echo "UID:" . uniqid() . "@yourdomain.com\r\n";
        echo "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        echo "DTSTART:" . $dt_start_utc->format('Ymd\THis\Z') . "\r\n";
        echo "DTEND:" . $dt_end_utc->format('Ymd\THis\Z') . "\r\n";
        echo "SUMMARY:" . ical_escape($event->post_title) . "\r\n";
        echo "DESCRIPTION:" . ical_escape($description) . "\r\n";
        echo "LOCATION:" . ical_escape($location) . "\r\n";
        echo "END:VEVENT\r\n";
        echo "END:VCALENDAR\r\n";
        exit;
    }
});

function get_calendar_links($event_id)
{
    $event = get_post($event_id);
    $date = carbon_get_post_meta($event_id, 'bsf_date');
    $start_time = carbon_get_post_meta($event_id, 'bsf_starting_time');
    $end_time = carbon_get_post_meta($event_id, 'bsf_ending_time');
    $description = carbon_get_post_meta($event_id, 'bsf_description');

    $stageTerms = wp_get_post_terms(
        $event_id,
        'bsf_stage',
        array('fields' => 'names')
    );
    $stageInfo = '';
    if (isset($stageTerms[0])) {
        $stageInfo = $stageTerms[0];
    }

    $stageLocation = wp_get_post_terms(
        $event_id,
        'bsf_event_location',
        array('fields' => 'names')
    );
    if ($stageInfo && isset($stageLocation[0])) {
        $stageInfo .= ' (' . $stageLocation[0] . ')';
    }

    $speakerIds = bsf_get_relevant_speakers($event_id);
    $speakerNames = [];
    if ($speakerIds) {
        foreach ($speakerIds as $sid) {
            $speakerNames[] = carbon_get_post_meta($sid, 'bsf_last_name') . ' ' . carbon_get_post_meta($sid, 'bsf_first_name');
        }
    }

    if ($stageInfo) {
        $description .= "\n" . sprintf(__('Sz\xEDnpad: %s', 'bsf-plugin'), $stageInfo);
    }
    if ($speakerNames) {
        $description .= "\n" . sprintf(__('El\x0151ad\xF3: %s', 'bsf-plugin'), implode(', ', $speakerNames));
    }

    $eventTerms = wp_get_post_terms(
        $event_id,
        'bsf_main_event_name',
        array(
            'parent' => 0,
            'fields' => 'names'
        )
    );

    $locationTerms = wp_get_post_terms(
        $event_id,
        'bsf_event_location',
        array(
            'parent' => 0,
            'fields' => 'names'
        )
    );

    $location = '';
    if (isset($eventTerms[0]) && $eventTerms[0] != '') {
        $location = $eventTerms[0];
    }

    if (isset($locationTerms[0]) && $locationTerms[0] != '') {
        $location .= ' - ' . $locationTerms[0];
    }

    // Convert to Budapest timezone
    $timezone = new DateTimeZone('Europe/Budapest');
    $dt_start = new DateTime("$date $start_time", $timezone);
    $dt_end = new DateTime("$date $end_time", $timezone);

    // Google Calendar (local time for both start and end)
    $google_url = "https://www.google.com/calendar/render?action=TEMPLATE" .
        "&text=" . urlencode($event->post_title) .
        "&dates=" . $dt_start->format('Ymd\THis') .
        "/" . $dt_end->format('Ymd\THis') .
        "&details=" . urlencode(strip_tags($description)) .
        "&location=" . urlencode($location) .
        "&ctz=Europe/Budapest";

    // Outlook Web (UTC timestamps, still works fine)
    $outlook_url = "https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose" .
        "&rru=addevent" .
        "&subject=" . urlencode($event->post_title) .
        "&startdt=" . $dt_start->format('Y-m-d\TH:i:s') .
        "&enddt=" . $dt_end->format('Y-m-d\TH:i:s') .
        "&location=" . urlencode($location) .
        "&body=" . urlencode(strip_tags($description));

    // iCal download link
    $ical_url = add_query_arg('download_ical', $event_id, home_url('/'));

    return [
        'google' => $google_url,
        'outlook' => $outlook_url,
        'apple' => $ical_url
    ];
}

function ical_escape($string)
{
    $string = strip_tags($string);
    $string = str_replace('\\', '\\\\', $string);
    $string = str_replace(';', '\;', $string);
    $string = str_replace(',', '\,', $string);
    $string = str_replace("\r\n", '\n', $string);
    $string = str_replace("\n", '\n', $string);
    $string = str_replace("\r", '\n', $string);

    return implode("\r\n ", str_split($string, 73));
}
