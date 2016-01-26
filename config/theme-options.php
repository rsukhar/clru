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
		'title' => __( 'reCaptcha public key', 'codelights' ),
		'description' => __( 'This option sets Google reCaptcha public key for Register page', 'codelights' ),
		'std' => '',
		'type' => 'text',
	),
), 'after', 'custom_html' );

$config['generalsettings']['fields'] = us_array_merge_insert( $config['generalsettings']['fields'], array(
	'google_recaptcha_secret_key' => array(
		'title' => __( 'reCaptcha secret key', 'codelights' ),
		'description' => __( 'This option sets Google reCaptcha secret key for Register page', 'codelights' ),
		'std' => '',
		'type' => 'text',
	),
), 'after', 'google_recaptcha_secret_key' );

$config['headeroptions']['fields'] = us_array_merge_insert( $config['headeroptions']['fields'], array(
	'header_userblock_show' => array(
		'text' => __( 'Show <strong>User Bar</strong> in the Header', 'codelights' ),
		'std' => 0,
		'type' => 'switch',
		'show_if' => array( 'header_layout', 'in', array( 'extended' ) ),
		'classes' => 'title_top',
	),
), 'after', 'header_language_show' );

return $config;
