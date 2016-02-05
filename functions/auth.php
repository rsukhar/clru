<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ajax for login
 */
add_action( 'wp_ajax_do_login', 'clru_do_login' );
add_action( 'wp_ajax_nopriv_do_login', 'clru_do_login' );
function clru_do_login() {

	check_ajax_referer( 'clru_do_login_nonce', '_ajax_nonce' );

	if ( $_POST['username'] == '' OR $_POST['password'] == '' ) {
		wp_send_json_error( array(
			'username' => 'Нужно ввести имя пользователя и пароль',
		) );
	}

	if ( filter_var( trim( $_POST['username'] ), FILTER_VALIDATE_EMAIL ) ) {
		$user = get_user_by( 'email', trim( $_POST['username'] ) );
	} else {
		$user = get_user_by( 'login', trim( $_POST['username'] ) );
	}

	if ( ! $user ) {
		wp_send_json_error( array(
			'username' => 'Неправильное имя пользователя.',
		) );
	}

	if ( ! wp_check_password( trim( $_POST['password'] ), $user->user_pass, $user->ID ) ) {
		wp_send_json_error( array(
			'password' => 'Неправильные имя пользователя или пароль.',
		) );
	}

	wp_set_current_user( $user->ID, $_POST['username'] );
	wp_set_auth_cookie( $user->ID, ! ! $_POST['remember'] );
	do_action( 'wp_login', $_POST['username'] );

	wp_send_json_success();
}

/* required for register form validation */
$clru_validate = '';
add_action( 'init', 'new_user_redirect' );
function new_user_redirect() {
	global $clru_validate;
	if ( $_POST['clru_register_validation_required'] != '' ) {
		$register_error = '';

		$secret_key = us_get_option( 'google_recaptcha_secret_key' );

		if ( $secret_key ) {
			$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['captcha_response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'];
			$response = wp_remote_get( $url );

			if ( $response['success'] === FALSE ) {
				$clru_validate['recaptcha_validate_state'] .= 'Очень жаль, но вы - робот! А мы не работаем с роботами.';
				$clru_validate['recaptcha_state'] = 'check_wrong';
				$register_error = 'error';
			}
		}

		if ( $_POST['username'] != '' ) {
			$clru_validate['username_exists_state'] = '';
			if ( username_exists( trim( $_POST['username'] ) ) ) {
				$clru_validate['username_exists_state'] .= 'Пользователь с таким логином уже существует. Выберите, пожалуйста, другой логин или <a href="' . esc_url( home_url( '/' ) ) . 'request_password_reset/">восстановите ваш пароль</a>, если забыли его. ';
				$clru_validate['username_state'] = 'check_wrong';
				$register_error = 'error';
			}
			if ( filter_var( trim( $_POST['username'] ), FILTER_VALIDATE_EMAIL ) ) {
				$clru_validate['username_exists_state'] .= 'Ваш логин так похож на адрес email. Рекомендуем выбрать иной логин.';
				$clru_validate['username_state'] = 'check_wrong';
				$register_error = 'error';
			}
		} else {
			$clru_validate['username_exists_state'] = 'Вы не ввели логин.';
			$clru_validate['username_state'] = 'check_wrong';
			$register_error = 'error';
		}

		if ( $_POST['realname'] != '' ) {
			$clru_validate['realname_valid_state'] = '';
			if ( mb_strlen( $_POST['realname'], 'UTF-8' ) < 4 ) {
				$clru_validate['realname_valid_state'] .= 'У вас такие короткие имя и фамилия? Уверены? ';
				$clru_validate['realname_state'] = 'check_wrong';
				$register_error = 'error';
			}

			if ( strstr( $_POST['realname'], ' ' ) === FALSE ) {
				$clru_validate['realname_valid_state'] .= 'В вашем имени и фамилии должен быть как минимум один пробел. ';
				$clru_validate['realname_state'] = 'check_wrong';
				$register_error = 'error';
			}
		} else {
			$clru_validate['realname_valid_state'] = 'Вы не ввели имя и фамилию.';
			$clru_validate['realname_state'] = 'check_wrong';
			$register_error = 'error';
		}

		if ( $_POST['email'] != '' ) {
			$clru_validate['email_valid_state'] = '';
			if ( ! filter_var( trim( $_POST['email'] ), FILTER_VALIDATE_EMAIL ) ) {
				$clru_validate['email_valid_state'] .= 'Вы ввели неправильный email. ';
				$clru_validate['email_state'] = 'check_wrong';
				$register_error = 'error';
			}
			if ( email_exists( trim( $_POST['email'] ) ) ) {
				$clru_validate['email_valid_state'] .= 'Пользователь с таким email уже существует. Введите, пожалуйста, другой email или <a href="' . esc_url( home_url( '/' ) ) . 'request_password_reset/">восстановите ваш пароль</a>, если забыли его. ';
				$clru_validate['email_state'] = 'check_wrong';
				$register_error = 'error';
			}
		} else {
			$clru_validate['email_valid_state'] = 'Вы не ввели адрес email.';
			$clru_validate['email_state'] = 'check_wrong';
			$register_error = 'error';
		}

		if ( $_POST['password'] != '' AND $_POST['password2'] != '' ) {
			$clru_validate['password1_valid_state'] = '';
			if ( trim( $_POST['password'] ) != trim( $_POST['password2'] ) ) {
				$clru_validate['password1_valid_state'] .= 'Пароль и подтверждение пароля должны совпадать. ';
				$clru_validate['password1_state'] = 'check_wrong';
				$clru_validate['password2_state'] = 'check_wrong';
				$register_error = 'error';
			}
			if ( mb_strlen( trim( $_POST['password'] ), 'UTF-8' ) < 8 AND mb_strlen( trim( $_POST['password2'] ), 'UTF-8' ) < 8 ) {
				$clru_validate['password1_valid_state'] .= 'Длина пароля должна быть не менее 8 символов.';
				$clru_validate['password1_state'] = 'check_wrong';
				$clru_validate['password2_state'] = 'check_wrong';
				$register_error = 'error';
			}
		} else if ( $_POST['password'] == '' AND $_POST['password2'] != '' ) {
			$clru_validate['password1_valid_state'] = 'Необходимо ввести пароль.';
			$clru_validate['password1_state'] = 'check_wrong';
			$clru_validate['register_error'] = 'error';
		} else if ( $_POST['password'] != '' AND $_POST['password2'] == '' ) {
			$clru_validate['password2_valid_state'] = 'Необходимо ввести также подтверждение пароля.';
			$clru_validate['password2_state'] = 'check_wrong';
			$register_error = 'error';
		} else {
			$clru_validate['password1_valid_state'] = 'Необходимо ввести пароль и подтверждение пароля.';
			$clru_validate['password1_state'] = 'check_wrong';
			$clru_validate['password2_state'] = 'check_wrong';
			$register_error = 'error';
		}

		if ( $_POST['agree'] != '1' ) {
			$clru_validate['agree_valid_state'] = 'Мы не можем вас зарегистрировать без вашего согласия с правилами сайта и передачей персональных данных.';
			$clru_validate['agree_state'] = 'check_wrong';
			$register_error = 'error';
		}

		if ( $register_error != 'error' ) {

			$user_id = wp_create_user( trim( $_POST['username'] ), trim( $_POST['password'] ), trim( $_POST['email'] ) );
			if ( ! is_wp_error( $user_id ) ) {
				$user = get_user_by( 'id', $user_id );
				$user->set_role( 'subscriber' );
				$userdata = array(
					'ID' => $user_id,
					'display_name' => trim( $_POST['realname'] ),
				);
				wp_update_user( $userdata );

				$realname = explode( ' ', trim( $_POST['realname'] ) );
				update_user_meta( $user_id, 'first_name', $realname[0] );
				update_user_meta( $user_id, 'last_name', $realname[1] );
			}

			if ( ! is_wp_error( $user_id ) ) {
				if ( $user ) {
					wp_set_current_user( $user_id, $user->user_login );
					wp_set_auth_cookie( $user_id );
					do_action( 'wp_login', $user->user_login );
					wp_redirect( home_url( '/' ) );
					exit;
				}
			}
		}
	}

	if ( is_admin() AND current_user_can( 'subscriber' ) ) {
		wp_redirect( home_url( '/' ) );
		exit;
	}
}

/*
 * Based on core function retrieve_password(), located in wp-login.php
 * Changed a message text and a link to password reset form.
 *
 * @return bool
 */
function clru_retrieve_password() {
	global $wpdb, $wp_hasher, $us_stylesheet_directory;

	$errors = new WP_Error();

	if ( empty( $_POST['user_login'] ) ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Enter a username or email address.' ) );
	} elseif ( strpos( $_POST['user_login'], '@' ) ) {
		$user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
		if ( empty( $user_data ) ) {
			$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: There is no user registered with that email address.' ) );
		}
	} else {
		$login = trim( $_POST['user_login'] );
		$user_data = get_user_by( 'login', $login );
	}

	/**
	 * Fires before errors are returned from a password reset request.
	 *
	 * @since 2.1.0
	 * @since 4.4.0 Added the `$errors` parameter.
	 *
	 * @param WP_Error $errors A WP_Error object containing any errors generated
	 *                         by using invalid credentials.
	 */
	do_action( 'lostpassword_post', $errors );

	if ( $errors->get_error_code() ) {
		return $errors;
	}

	if ( ! $user_data ) {
		$errors->add( 'invalidcombo', __( '<strong>ERROR</strong>: Invalid username or email.' ) );

		return $errors;
	}

	// Redefining user_login ensures we return the right case in the email.
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$user_display_name = $user_data->display_name;
	$key = get_password_reset_key( $user_data );

	if ( is_wp_error( $key ) ) {
		return $key;
	}

	if ( $user_display_name != '' ) {
		$name_in_letter = $user_display_name;
	} else {
		$name_in_letter = $user_login;
	}

	ob_start();
	require $us_stylesheet_directory . '/templates/elements/clru-retrieve-password-message.php';
	ob_get_clean();

	if ( is_multisite() ) {
		$blogname = $GLOBALS['current_site']->site_name;
	} else /*
		 * The blogname option is escaped with esc_html on the way into the database
		 * in sanitize_option we want to reverse this for the plain text arena of emails.
		 */ {
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	$title = sprintf( __( '[%s] Password Reset' ), $blogname );

	/**
	 * Filter the subject of the password reset email.
	 *
	 * @since 2.8.0
	 * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
	 *
	 * @param string $title Default email title.
	 * @param string $user_login The username for the user.
	 * @param WP_User $user_data WP_User object.
	 */
	$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

	/**
	 * Filter the message body of the password reset mail.
	 *
	 * @since 2.8.0
	 * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
	 *
	 * @param string $message Default mail message.
	 * @param string $key The activation key.
	 * @param string $user_login The username for the user.
	 * @param WP_User $user_data WP_User object.
	 */
	$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

	if ( $message && ! wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
		wp_die( __( 'The email could not be sent.' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.' ) );
	}

	return TRUE;
}

add_action( 'wp_footer', 'clru_login_form', 10 );
function clru_login_form() {
	global $us_stylesheet_directory;

	require $us_stylesheet_directory . '/templates/elements/cl-login.php';
}

add_action( 'us_top_subheader_end', 'clru_load_user_block', 10 );
function clru_load_user_block() {
	if ( us_get_option( 'header_userblock_show' ) ) {
		us_load_template( 'templates/widgets/user-block' );
	}
}
