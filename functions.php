<?php

add_action( 'wp_enqueue_scripts', 'clru_theme_enqueue_styles' );
function clru_theme_enqueue_styles() {
	wp_enqueue_style( 'codelights-parent-style', get_template_directory_uri() . '/style.css' );
}

/**
 * include cl_calculator plugin files
 */
require get_stylesheet_directory() . '/cl-calculator/cl-calculator.php';

?>
