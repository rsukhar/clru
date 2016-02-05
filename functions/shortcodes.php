<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Register shortcodes
 */
add_action( 'init', 'clru_register_shortcodes', 20 );
function clru_register_shortcodes() {
	// calculator
	add_shortcode( 'cl-calculator', 'clru_shortcode_calcularor' );

	// registration form
	add_shortcode( 'cl-register', 'clru_shortcode_register' );

	// request password reset form
	add_shortcode( 'cl-request-password-reset', 'clru_shortcode_request_password_reset' );

	// password reset form
	add_shortcode( 'cl-reset-password', 'clru_shortcode_reset_password' );
}

/**
 * Shortcode for calculator
 *
 * @param $atts
 *
 * @return string
 */
function clru_shortcode_calcularor( $atts ) {
	global $us_stylesheet_directory;

	$defaults = array(
		'basic_rate' => 0,
		'required_skills' => '',
		'skills' => '',
		'extra_class' => '',
	);
	$atts = array_intersect_key( $atts, $defaults );
	extract( $atts );
	ob_start();
	require $us_stylesheet_directory . '/templates/elements/cl-calculator.php';

	return ob_get_clean();
}

/**
 * Shortcode for registration form
 *
 * @param $atts
 *
 * @return string
 */
function clru_shortcode_register( $atts ) {
	global $clru_validate, $us_stylesheet_directory;

	$username = $_POST['username'];
	$realname = $_POST['realname'];
	$email = $_POST['email'];
	$public_key = us_get_option( 'google_recaptcha_public_key', $default_value = NULL );

	ob_start();
	require $us_stylesheet_directory . '/templates/elements/clru-register-form.php';

	return ob_get_clean();
}

/**
 * Shortcode for request password reset form
 *
 * @param $atts
 *
 * @return string
 */
function clru_shortcode_request_password_reset( $atts ) {
	global $us_stylesheet_directory;

	if ( $_POST['clru_request_password_reset'] != '' ) {
		if ( ! email_exists( trim( $_POST['user_login'] ) ) ) {
			$validate['email_valid_state'] = 'Пользователь с таким email не существует. Введите, пожалуйста, другой email или <a href="' . esc_url( home_url( '/register/' ) ) . 'request_password_reset/">зарегистрируйтесь</a>, если не регистрировались ранее. ';
			$validate['email_state'] = 'check_wrong';
		} else {
			$errors = clru_retrieve_password();
			if ( $errors !== TRUE ) {
				$validate['email_valid_state'] = $errors;
				$validate['email_state'] = 'check_wrong';
			} else {
				$validate['email_valid_state'] = 'Письмо для восстановления пароля отправлено на ваш email';
				$validate['email_state'] = 'check_success';
			}
		}
	}

	ob_start();
	require $us_stylesheet_directory . '/templates/elements/clru-password-reset-form.php';

	return ob_get_clean();
}

/**
 * Shortcode for password reset form
 *
 * @param $atts
 *
 * @return string
 */
function clru_shortcode_reset_password( $atts ) {
	global $us_stylesheet_directory;

	if ( $_GET['login'] ) {
		$user_login = $_GET['login'];
	} else if ( $_POST['user_login'] ) {
		$user_login = $_POST['user_login'];
	}

	if ( $_GET['key'] ) {
		$activation_key = $_GET['key'];
	} else if ( $_POST['user_activation_key'] ) {
		$activation_key = $_POST['user_activation_key'];
	}

	if ( $_POST['clru_new_password_request'] != '' ) {
		if ( $_POST['user_activation_key'] != '' ) {
			if ( $_POST['user_login'] != '' ) {
				if ( $_POST['password'] != '' AND $_POST['password2'] != '' ) {
					if ( trim( $_POST['password'] ) != trim( $_POST['password2'] ) ) {
						$validate['password2_valid_state'] = 'Пароли не совпадают.';
						$validate['password1_state'] = 'check_wrong';
						$validate['password2_state'] = 'check_wrong';
					} else {
						$user = get_user_by( 'login', trim( $_POST['user_login'] ) );
						if ( $user ) {
							wp_set_password( trim( $_POST['password'] ), $user->ID );
							$validate['password2_valid_state'] = 'Пароль успешно изменен!';
							$validate['password2_state'] = 'check_success';
						} else {
							$validate['password2_valid_state'] = 'Неизвестный пользователь. Перейдите на данную страницу по ссылке из письма, в ней содержится имя пользователя.';
							$validate['password2_state'] = 'check_wrong';
						}
					}
				} else {
					$validate['password2_valid_state'] = 'Необходимо ввести новый пароль и его подтверждение.';
					$validate['password1_state'] = 'check_wrong';
					$validate['password2_state'] = 'check_wrong';
				}
			} else {
				$validate['password2_valid_state'] = 'Неизвестный пользователь. Перейдите на данную страницу по ссылке из письма, в ней содержится имя пользователя.';
				$validate['password2_state'] = 'check_wrong';
			}
		} else {
			$validate['password2_valid_state'] = 'Отсутствует ключ активации. Перейдите на данную страницу по ссылке из письма, в ней содержится ключ активации.';
			$validate['password2_state'] = 'check_wrong';
		}
	}

	ob_start();
	require $us_stylesheet_directory . '/templates/elements/clru-new-password-form.php';

	return ob_get_clean();
}

