<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

add_action( 'vc_before_init', 'clru_integrate_with_vc' );
function clru_integrate_with_vc() {
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

?>
