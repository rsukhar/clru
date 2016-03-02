<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Register shortcodes
 */
add_action( 'init', 'clru_register_shortcodes', 20 );
function clru_register_shortcodes() {
	// calculator
	add_shortcode( 'cl-calculator', 'clru_shortcode_calcularor' );

	// registration form
	add_shortcode( 'cl-register', 'clru_shortcode_register' );

	// request password reset form
	add_shortcode( 'cl-request-password-reset', 'clru_shortcode_request_password_reset' );

	// password reset form
	add_shortcode( 'cl-reset-password', 'clru_shortcode_reset_password' );

	// progress marker
	add_shortcode( 'cl-learnmarker', 'clru_shortcode_progress_marker' );

	// learn navigation
	add_shortcode( 'cl-learnnav', 'clru_shortcode_learn_navigation' );
}

/**
 * Shortcode for calculator
 *
 * @param $atts
 *
 * @return string
 */
function clru_shortcode_calcularor( $atts ) {
	global $us_stylesheet_directory;

	$defaults = array(
		'basic_rate' => 0,
		'required_skills' => '',
		'skills' => '',
		'extra_class' => '',
	);
	$atts = array_intersect_key( $atts, $defaults );
	extract( $atts );
	ob_start();
	require $us_stylesheet_directory . '/templates/elements/clru-calculator.php';

	return ob_get_clean();
}

/**
 * Shortcode for registration form
 *
 * @param $atts
 *
 * @return string
 */
function clru_shortcode_register( $atts ) {
	global $us_stylesheet_directory;

	if ( ! is_user_logged_in() ) {

		$public_key = us_get_option( 'google_recaptcha_public_key' );

		ob_start();
		require $us_stylesheet_directory . '/templates/elements/clru-register-form.php';

		return ob_get_clean();
	} else {
		return '<meta id="redirect" http-equiv="refresh" content="0; url=' . esc_url( home_url( '/' ) ) . '">';
	}
}

/**
 * Shortcode for request password reset (get message with a link to a setting new password form) form
 *
 * @param $atts
 *
 * @return string
 */
function clru_shortcode_request_password_reset( $atts ) {
	global $us_stylesheet_directory;
	if ( ! is_user_logged_in() ) {

		ob_start();
		require $us_stylesheet_directory . '/templates/elements/clru-password-reset-form.php';

		return ob_get_clean();
	} else {
		return '<meta id="redirect" http-equiv="refresh" content="0; url=' . esc_url( home_url( '/' ) ) . '">';
	}
}

/**
 * Shortcode for password reset (set new password) form
 *
 * @param $atts
 *
 * @return string
 */
function clru_shortcode_reset_password( $atts ) {
	global $us_stylesheet_directory;
	if ( ! is_user_logged_in() ) {
		if ( $_GET['login'] ) {
			$user_login = $_GET['login'];
		} else if ( $_POST['user_login'] ) {
			$user_login = $_POST['user_login'];
		}

		if ( $_GET['key'] ) {
			$activation_key = $_GET['key'];
		} else if ( $_POST['user_activation_key'] ) {
			$activation_key = $_POST['user_activation_key'];
		}

		ob_start();
		require $us_stylesheet_directory . '/templates/elements/clru-new-password-form.php';

		return ob_get_clean();
	} else {
		return '<meta id="redirect" http-equiv="refresh" content="0; url=' . esc_url( home_url( '/' ) ) . '">';
	}
}

/**
 * Shortcode for progress marker
 *
 * @param atts
 *
 * @return string
 */
function clru_shortcode_progress_marker( $atts ) {
	global $us_stylesheet_directory;

	$defaults = array(
		'id' => 0,
		'title' => '',
	);
	$atts = array_intersect_key( $atts, $defaults );
	extract( $atts );

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

	if ( is_user_logged_in() ) {
		ob_start();
		require $us_stylesheet_directory . '/templates/elements/clru-progress-marker.php';

		return ob_get_clean();
	} else {
		return;
	}
}

function clru_shortcode_learn_navigation( $atts ) {
	global $us_stylesheet_directory, $wpdb;

	$defaults = array(
		'id' => 0,
	);
	$atts = array_intersect_key( $atts, $defaults );
	extract( $atts );

	if ( $atts['id'] ) {
		$parent_id = $atts['id'];
	} else {
		$parent_id = get_the_ID();
	}

	$user_logged = FALSE;
	$user = wp_get_current_user();
	if ( $user->exists() ) {
		$user_logged = TRUE;
		$user_id = $user->ID;

		$user_progress = get_user_meta( $user_id, 'clru_progress', TRUE );
	}

	$user_progress_pages = $user_progress['pages'];

	$result = $wpdb->get_results( 'SELECT `ID`, `post_title`, `post_parent` FROM `wp_posts` WHERE `post_type` = "page" AND `post_parent` != 0 ORDER BY `menu_order` ASC' );

	$learn_pages = [ ];
	$pages_markers = [ ];
	foreach ( $result as $page ) {
		$learn_pages[ $page->post_parent ][ $page->ID ] = $page->post_title;

		$meta_markers = get_post_meta( $page->ID, 'clru_markers', TRUE );
		if ( $meta_markers ) {
			$current_page_markers = unserialize( $meta_markers );
			$total_markers = count( $current_page_markers );
		} else {
			$total_markers = 0;
		}

		$current_page = $user_progress_pages[ $page->ID ];
		$user_completed = count( $current_page['markers'] );

		$pages_markers[ $page->ID ] = array(
			'parent_id' => $page->post_parent,
			'total_markers' => $total_markers,
			'completed_markers' => $user_completed,
		);
	}

	function child_progress( $pages_markers, $parent_id ) {
		$total_markers = 0;
		$completed_markers = 0;
		foreach ( $pages_markers as $page_id => $marker ) {
			if ( $parent_id == $marker['parent_id'] ) {
				$child_progress = child_progress( $pages_markers, $page_id );
				$total_markers += $marker['total_markers'] + $child_progress['total_markers'];
				$completed_markers += $marker['completed_markers'] + $child_progress['completed_markers'];
			}
		}

		return array(
			'total_markers' => $total_markers,
			'completed_markers' => $completed_markers,
		);
	}


	ob_start();
	require $us_stylesheet_directory . '/templates/elements/clru-learn-navigation.php';

	return ob_get_clean();
}
