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
		'title' => 'Публичный ключ reCaptcha',
		'std' => '',
		'type' => 'text',
	),
	'google_recaptcha_secret_key' => array(
		'title' => 'Секретный ключ reCaptcha',
		'std' => '',
		'type' => 'text',
	),
), 'after', 'custom_html' );

$config['headeroptions']['fields'] = us_array_merge_insert( $config['headeroptions']['fields'], array(
	'header_userblock_show' => array(
		'text' => __( 'Показать пользовательский блок', 'codelights' ),
		'std' => 1,
		'type' => 'switch',
		'show_if' => array( 'header_layout', 'in', array( 'extended' ) ),
		'classes' => 'title_top',
	),
), 'after', 'header_language_show' );

$config['headeroptions']['fields']['header_layout']['std'] = 'extended';

return $config;
