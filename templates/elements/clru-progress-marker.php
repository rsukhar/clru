<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a progress marker checkbox
 *
 * @var $marker_id string
 * @var $marker_name string
 * @var $ajax_nonce string
 * @var $page_id int
 * @var $user_id int
 * @var $checked string
 * @var $atts array
 */

$output = '<label for="' . $marker_id . '" class="g-checkbox">
				<input type="checkbox" value="1" name="' . $marker_name . '" id="' . $marker_id . '" class="not-empty clru-progress-marker" data-nonce="' . $ajax_nonce . '" data-page="' . $page_id . '" data-user="' . $user_id . '" ' . $checked . '>&nbsp;' . esc_attr( $atts['title'] ) . '
			</label>';

echo $output;
