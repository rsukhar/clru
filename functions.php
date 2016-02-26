<?php
add_action( 'wp_enqueue_scripts', 'clru_assets' );
function clru_assets() {
	global $us_stylesheet_directory_uri, $us_template_directory;

	wp_enqueue_style( 'clru-parent-style', $us_template_directory . '/style.css' );

	wp_register_script( 'clru-child-main', $us_stylesheet_directory_uri . '/js/main.js', array(), FALSE, TRUE );
	wp_localize_script( 'clru-child-main', 'clruAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script( 'clru-child-main' );

	if ( is_page( 'register' ) ) {
		wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js', array(), FALSE, TRUE );
	}
}

add_action( 'after_setup_theme', 'clru_disable_admin_bar' );
function clru_disable_admin_bar() {
	if ( current_user_can( 'subscriber' ) ) {
		add_filter( 'show_admin_bar', '__return_false' );
	}
}

/**
 * Include helpers functions
 */
require $us_stylesheet_directory . '/functions/helpers.php';

/**
 * Include activation functions
 */
require $us_stylesheet_directory . '/functions/activation.php';

/**
 * Include Visual Composer map
 */
require $us_stylesheet_directory . '/functions/vc_map.php';

/**
 * Include shortcodes
 */
require $us_stylesheet_directory . '/functions/shortcodes.php';

/**
 * Include authorization functions
 */
require $us_stylesheet_directory . '/functions/auth.php';

/**
 * Include learning functions
 */
require $us_stylesheet_directory . '/functions/learn.php';
