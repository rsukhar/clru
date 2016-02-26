<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Grab all attributes for a given shortcode in a text
 *
 * @uses get_shortcode_regex()
 * @uses shortcode_parse_atts()
 *
 * @param  string $tag Shortcode tag
 * @param  string $text Text containing shortcodes
 *
 * @return array  $out   Array of attributes
 */

function clru_get_all_shortcode_attributes( $tag, $text ) {
	preg_match_all( '/' . get_shortcode_regex() . '/s', $text, $matches );
	$out = array();
	if ( isset( $matches[2] ) ) {
		foreach ( (array) $matches[2] as $key => $value ) {
			if ( $tag === $value ) {
				$out[] = shortcode_parse_atts( $matches[3][ $key ] );
			}
		}
	}

	return $out;
}

