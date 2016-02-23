<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

add_action( 'after_switch_theme', 'clru_add_pages_after_theme_activation' );
function clru_add_pages_after_theme_activation() {

	/* page Register */
	if ( ! get_page_by_path( 'register', OBJECT, 'page' ) ) {

		$post = array(
			'post_content' => '[cl-register]',
			'post_title' => 'Регистрация',
			'post_status' => 'publish',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_name' => 'register',
			'post_type' => 'page',
		);

		wp_insert_post( $post );
	}

	/* page Request password reset */
	if ( ! get_page_by_path( 'request_password_reset', OBJECT, 'page' ) ) {

		$post = array(
			'post_content' => '[cl-request-password-reset]',
			'post_title' => 'Восстановление пароля',
			'post_status' => 'publish',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_name' => 'request_password_reset',
			'post_type' => 'page',
		);

		wp_insert_post( $post );
	}

	/* page Reset password */
	if ( ! get_page_by_path( 'reset_password', OBJECT, 'page' ) ) {

		$post = array(
			'post_content' => '[cl-reset-password]',
			'post_title' => 'Новый пароль',
			'post_status' => 'publish',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_name' => 'reset_password',
			'post_type' => 'page',
		);

		wp_insert_post( $post );
	}

}
