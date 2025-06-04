<?php
/** This template is used to display a stage and its events */

get_header();

$curTerm =  $wp_query->queried_object;

echo do_shortcode('[bsf_eventsbanner name="' . $curTerm->slug . '"]');
echo do_shortcode('[bsf_eventscontent]');

get_footer();