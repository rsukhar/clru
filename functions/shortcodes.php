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

	$public_key = us_get_option( 'google_recaptcha_public_key', $default_value = NULL );

	ob_start();
	require $us_stylesheet_directory . '/templates/elements/clru-register-form.php';

	return ob_get_clean();
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

	ob_start();
	require $us_stylesheet_directory . '/templates/elements/clru-password-reset-form.php';

	return ob_get_clean();
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
}

