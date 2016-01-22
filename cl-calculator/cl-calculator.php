<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * CodeLights Calculator
 * Text Domain: codelights
 */

if ( ! function_exists( 'clc_register_shortcodes' ) ) {
	add_action( 'init', 'clc_register_shortcodes', 20 );
	function clc_register_shortcodes() {
		add_shortcode( 'cl-calculator', 'clc_handle_shortcode' );
	}
}

if ( ! function_exists( 'clc_assets' ) ) {
	add_action( 'wp_enqueue_scripts', 'clc_assets', 12 );
	function clc_assets() {
		wp_enqueue_style( 'cl-calculator', get_stylesheet_directory_uri() . '/cl-calculator/css/cl-calculator.css', array(), FALSE, 'all' );
		wp_register_script( 'cl-calculator', get_stylesheet_directory_uri() . '/cl-calculator/js/cl-calculator.js', array( 'jquery' ), FALSE, TRUE );
	}
}

if ( ! function_exists( 'clc_handle_shortcode' ) ) {
	function clc_handle_shortcode( $atts ) {
		wp_enqueue_style( 'cl-calculator' );
		wp_enqueue_script( 'cl-calculator' );
		$defaults = array(
			'basic_rate' => 0,
			'required_skills' => '',
			'skills' => '',
			'extra_class' => '',
		);
		$atts = array_intersect_key( $atts, $defaults );
		extract( $atts );
		ob_start();
		require get_stylesheet_directory() . '/cl-calculator/templates/elements/cl-calculator.php';

		return ob_get_clean();
	}
}

if ( ! function_exists( 'clc_integrate_with_vc' ) ) {
	add_action( 'vc_before_init', 'clc_integrate_with_vc' );
	function clc_integrate_with_vc() {
		vc_map( array(
			'base' => 'cl-calculator',
			'name' => __( 'Calculator', 'us' ),
			'icon' => 'icon-wpb-pricing-table',
			'category' => 'CodeLights',
			'params' => array(
				array(
					'param_name' => 'basic_rate',
					'heading' => __( 'Basic Rate', 'codelights' ),
					'type' => 'textfield',
				),
				array(
					'param_name' => 'required_skills',
					'heading' => __( 'Required Skills', 'codelights' ),
					'type' => 'param_group',
					'params' => array(
						array(
							'param_name' => 'title',
							'heading' => 'Skill Title',
							'type' => 'textfield',
						),
						array(
							'param_name' => 'help',
							'heading' => 'Help',
							'type' => 'textarea',
						),
					),
				),
				array(
					'heading' => __( 'Additional Skills', 'codelights' ),
					'param_name' => 'skills',
					'type' => 'param_group',
					'params' => array(
						array(
							'param_name' => 'title',
							'heading' => 'Skill Title',
							'type' => 'textfield',
						),
						array(
							'param_name' => 'rate',
							'heading' => 'Pricing Rate',
							'type' => 'textfield',
						),
						array(
							'param_name' => 'help',
							'heading' => 'Help',
							'type' => 'textarea',
						),
					),
				),
				array(
					'param_name' => 'el_class',
					'heading' => __( 'Extra class name', 'codelights' ),
					'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'codelights' ),
					'type' => 'textfield',
				),
			),

		) );
	}
}
