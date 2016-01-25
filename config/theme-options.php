<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Child Theme's Theme Options config
 *
 * @var $config array Framework-based theme options config
 *
 * @return array Changed config
 */

$config['generalsettings']['fields'] = us_array_merge_insert( $config['generalsettings']['fields'], array(
	'google_recaptcha_public_key' => array(
		'title' => __( 'reCaptcha public key', 'us' ),
		'description' => __( 'This option sets Google reCaptcha public key for Register page', 'codelights' ),
		'std' => '',
		'type' => 'text',
	),
), 'after', 'custom_html' );

$config['generalsettings']['fields'] = us_array_merge_insert( $config['generalsettings']['fields'], array(
	'google_recaptcha_secret_key' => array(
		'title' => __( 'reCaptcha secret key', 'us' ),
		'description' => __( 'This option sets Google reCaptcha secret key for Register page', 'codelights' ),
		'std' => '',
		'type' => 'text',
	),
), 'after', 'google_recaptcha_secret_key' );

// Blog sharing button styles
unset( $config['blogoptions']['fields']['post_sharing_type']['options']['outlined'] );

unset( $config['styling']['fields']['color_menu_active_bg'] );

unset( $config['footeroptions']['fields']['footer_layout'] );

return $config;
