<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ajax for login
 */
add_action( 'wp_ajax_do_login', 'clru_do_login' );
add_action( 'wp_ajax_nopriv_do_login', 'clru_do_login' );
function clru_do_login() {

	$success = '';

	check_ajax_referer( 'clru_do_login_nonce', '_ajax_nonce' );

	if ( $_POST['username'] != '' AND $_POST['password'] != '' ) {
		if ( ! filter_var( trim( $_POST['username'] ), FILTER_VALIDATE_EMAIL ) ) {
			$user = get_user_by( 'login', trim( $_POST['username'] ) );
		} else if ( filter_var( trim( $_POST['username'] ), FILTER_VALIDATE_EMAIL ) ) {
			$user = get_user_by( 'email', trim( $_POST['username'] ) );
		} else {
			$message['username'] = 'Неправильное имя пользователя.';
		}
		if ( $user ) {
			if ( ! wp_check_password( trim( $_POST['password'] ), $user->user_pass, $user->ID ) ) {
				$message['password'] = 'Неправильные имя пользователя или пароль.';
			} else {
				if ( $_POST['remember'] == '1' ) {
					$remember = TRUE;
				} else {
					$remember = FALSE;
				}
				wp_set_current_user( $user->ID, $_POST['username'] );
				wp_set_auth_cookie( $user->ID, $remember );
				do_action( 'wp_login', $_POST['username'] );
				$success = 'OK';
			}
		} else {
			$message['username'] = 'Неправильное имя пользователя.';
		}
	} else {
		$message['username'] = 'Нужно ввести имя пользователя и пароль';
	}

	if ( $success == 'OK' ) {
		wp_send_json_success( $message );
	} else {
		wp_send_json_error( $message );
	}
}

