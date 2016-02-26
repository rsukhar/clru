<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a progress marker checkbox
 *
 * @var $atts Array
 */

$marker_id = esc_attr( $atts['id'] );
$marker_name = 'marker_' . esc_attr( $atts['id'] );

$ajax_nonce = wp_create_nonce( 'clru_do_progress_nonce' );
$user_id = get_current_user_id();
$page_id = get_the_ID();

$user_progress = get_user_meta( $user_id, 'clru_progress', TRUE );
$checked = '';
if ( $user_progress ) {
	$markers = $user_progress['pages'][ $page_id ]['markers'];
	if ( count( $markers ) > 0 ) {
		if ( in_array( $marker_id, $markers ) ) {
			$checked = 'checked="checked"';
		}
	}
}

$output = '<label for="' . $marker_id . '" class="g-checkbox">
				<input type="checkbox" value="1" name="' . $marker_name . '" id="' . $marker_id . '" class="not-empty clru-progress-marker" data-nonce="' . $ajax_nonce . '" data-page="' . $page_id . '" data-user="' . $user_id . '" ' . $checked . '>&nbsp;' . esc_attr( $atts['title'] ) . '
			</label>';

echo $output;
