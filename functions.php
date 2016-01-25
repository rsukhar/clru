<?php
/* required for register form validation */
$validate = '';

add_action( 'wp_enqueue_scripts', 'clru_theme_enqueue_styles' );
function clru_theme_enqueue_styles() {
	wp_enqueue_style( 'codelights-parent-style', get_template_directory_uri() . '/style.css' );
}

add_action( 'wp_enqueue_scripts', 'clru_assets' );
function clru_assets() {
	if ( is_page( 'register' ) ) {
		wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js', array(), FALSE, TRUE );
	}
}

add_action( 'after_setup_theme', 'clru_disable_admin_bar' );
function clru_disable_admin_bar() {
	if ( current_user_can( 'subscriber' ) ) {
		add_filter( 'show_admin_bar', '__return_false' );
	}
}

/**
 * include cl_calculator plugin files
 */
require get_stylesheet_directory() . '/cl-calculator/cl-calculator.php';

add_action( 'init', 'new_user_redirect' );
function new_user_redirect() {
	global $validate;
	if ( $_POST['register_validation_required'] != '' ) {
		$register_error = '';

		$secret_key = us_get_option( 'google_recaptcha_secret_key', $default_value = NULL );

		if ( $secret_key ) {
			$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['captcha_response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'];
			$response = wp_remote_get( $url );

			if ( $response['success'] === FALSE ) {
				$validate['recaptcha_validate_state'] .= 'Очень жаль, но вы - робот! А мы не работаем с роботами.';
				$validate['recaptcha_state'] = 'check_wrong';
				$register_error = 'error';
			}
		}

		if ( $_POST['username'] != '' ) {
			$validate['username_exists_state'] = '';
			if ( username_exists( $_POST['username'] ) ) {
				$validate['username_exists_state'] .= 'Пользователь с таким логином уже существует. Выберите, пожалуйста, другой логин или <a href="' . esc_url( home_url( '/' ) ) . 'request_password_reset/">восстановите ваш пароль</a>, если забыли его. ';
				$validate['username_state'] = 'check_wrong';
				$register_error = 'error';
			}
			if ( filter_var( $_POST['username'], FILTER_VALIDATE_EMAIL ) ) {
				$validate['username_exists_state'] .= 'Ваш логин так похож на адрес email. Рекомендуем выбрать иной логин.';
				$validate['username_state'] = 'check_wrong';
				$register_error = 'error';
			}
		} else {
			$validate['username_exists_state'] = 'Вы не ввели логин.';
			$validate['username_state'] = 'check_wrong';
			$register_error = 'error';
		}

		if ( $_POST['realname'] != '' ) {
			$validate['realname_valid_state'] = '';
			if ( mb_strlen( $_POST['realname'], 'UTF-8' ) < 4 ) {
				$validate['realname_valid_state'] .= 'У вас такие короткие имя и фамилия? Уверены? ';
				$validate['realname_state'] = 'check_wrong';
				$register_error = 'error';
			}

			if ( strstr( $_POST['realname'], ' ' ) === FALSE ) {
				$validate['realname_valid_state'] .= 'В вашем имени и фамилии должен быть как минимум один пробел. ';
				$validate['realname_state'] = 'check_wrong';
				$register_error = 'error';
			}
		} else {
			$validate['realname_valid_state'] = 'Вы не ввели имя и фамилию.';
			$validate['realname_state'] = 'check_wrong';
			$register_error = 'error';
		}

		if ( $_POST['email'] != '' ) {
			$validate['email_valid_state'] = '';
			if ( ! filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) ) {
				$validate['email_valid_state'] .= 'Вы ввели неправильный email. ';
				$validate['email_state'] = 'check_wrong';
				$register_error = 'error';
			}
			if ( email_exists( $_POST['email'] ) ) {
				$validate['email_valid_state'] .= 'Пользователь с таким email уже существует. Введите, пожалуйста, другой email или <a href="' . esc_url( home_url( '/' ) ) . 'request_password_reset/">восстановите ваш пароль</a>, если забыли его. ';
				$validate['email_state'] = 'check_wrong';
				$register_error = 'error';
			}
		} else {
			$validate['email_valid_state'] = 'Вы не ввели адрес email.';
			$validate['email_state'] = 'check_wrong';
			$register_error = 'error';
		}

		if ( $_POST['password'] != '' AND $_POST['password2'] != '' ) {
			$validate['password1_valid_state'] = '';
			if ( trim( $_POST['password'] ) != trim( $_POST['password2'] ) ) {
				$validate['password1_valid_state'] .= 'Пароль и подтверждение пароля должны совпадать. ';
				$validate['password1_state'] = 'check_wrong';
				$validate['password2_state'] = 'check_wrong';
				$register_error = 'error';
			}
			if ( mb_strlen( trim( $_POST['password'] ), 'UTF-8' ) < 8 AND mb_strlen( trim( $_POST['password2'] ), 'UTF-8' ) < 8 ) {
				$validate['password1_valid_state'] .= 'Длина пароля должна быть не менее 8 символов.';
				$validate['password1_state'] = 'check_wrong';
				$validate['password2_state'] = 'check_wrong';
				$register_error = 'error';
			}
		} else if ( $_POST['password'] == '' AND $_POST['password2'] != '' ) {
			$validate['password1_valid_state'] = 'Необходимо ввести сам пароль.';
			$validate['password1_state'] = 'check_wrong';
			$validate['register_error'] = 'error';
		} else if ( $_POST['password'] != '' AND $_POST['password2'] == '' ) {
			$validate['password2_valid_state'] = 'Необходимо ввести также подтверждение пароля.';
			$validate['password2_state'] = 'check_wrong';
			$register_error = 'error';
		} else {
			$validate['password1_valid_state'] = 'Необходимо ввести пароль и подтверждение пароля.';
			$validate['password1_state'] = 'check_wrong';
			$validate['password2_state'] = 'check_wrong';
			$register_error = 'error';
		}

		if ( $_POST['agree'] != '1' ) {
			$validate['agree_valid_state'] = 'Мы не можем вас зарегистрировать без вашего согласия с правилами сайта и передачей персональных данных.';
			$validate['agree_state'] = 'check_wrong';
			$register_error = 'error';
		}

		if ( $register_error != 'error' ) {

			$user_id = wp_create_user( $_POST['username'], $_POST['password'], $_POST['email'] );
			if ( ! is_wp_error( $user_id ) ) {
				$user = get_user_by( 'id', $user_id );
				$user->set_role( 'subscriber' );
				$userdata = array(
					'ID' => $user_id,
					'display_name' => trim( $_POST['realname'] ),
				);
				wp_update_user( $userdata );

				$realname = explode( ' ', $_POST['realname'] );
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

add_shortcode( 'cl-register', 'clru_register_form' );
function clru_register_form( $atts ) {
	global $validate;

	$output = '<form action="' . get_the_permalink() . '" method="post" class="g-form align_left for_register">';
	$output .= '<div class="g-form-h">';
	$output .= '<h2 class="g-form-title">Создать аккаунт</h2>';
	$output .= '<div class="g-form-row for_username ' . $validate['username_state'] . '">
									<div class="g-form-field">
										<input type="text" name="username" id="signup_username" value="' . esc_attr( $_POST['username'] ) . '">
										<label for="signup_username" class="g-form-field-label">Логин</label>
										<div class="g-form-field-bar"></div>
									</div>';
	$output .= '<div class="g-form-row-state">' . $validate['username_exists_state'] . '</div>';
	$output .= '</div>';

	$output .= '<div class="g-form-row for_realname ' . $validate['realname_state'] . '">
									<div class="g-form-field">
										<input type="text" name="realname" id="signup_realname" value="' . esc_attr( $_POST['realname'] ) . '">
										<label for="signup_realname" class="g-form-field-label">Имя и фамилия</label>
										<div class="g-form-field-bar"></div>
									</div>';
	$output .= '<div class="g-form-row-state">' . $validate['realname_valid_state'] . '</div>';
	$output .= '</div>';

	$output .= '<div class="g-form-row for_email ' . $validate['email_state'] . '">
									<div class="g-form-field">
										<input type="text" name="email" id="signup_email" value="' . esc_attr( $_POST['email'] ) . '">
										<label for="signup_email" class="g-form-field-label">Email</label>
										<div class="g-form-field-bar"></div>
									</div>';
	$output .= '<div class="g-form-row-state">' . $validate['email_valid_state'] . '</div>';
	$output .= '</div>';

	$output .= '<div class="g-form-row for_password ' . $validate['password1_state'] . '">
									<div class="g-form-field">
										<input type="password" name="password" id="signup_password" value="">
										<label for="signup_password" class="g-form-field-label">Пароль</label>
										<div class="g-form-field-bar"></div>
									</div>
									<div class="g-form-row-state">' . $validate['password1_valid_state'] . '</div>
								</div>';

	$output .= '<div class="g-form-row for_password ' . $validate['password2_state'] . '">
									<div class="g-form-field">
										<input type="password" name="password2" id="signup_password2" value="">
										<label for="signup_password2" class="g-form-field-label">Подтвердите пароль</label>
										<div class="g-form-field-bar"></div>
									</div>
									<div class="g-form-row-state">' . $validate['password2_valid_state'] . '</div>
								</div>';

	$public_key = us_get_option( 'google_recaptcha_public_key', $default_value = NULL );

	if ( $public_key != '' ) {
		$output .= '<div class="g-form-row for_recaptcha ' . $validate['recaptcha_state'] . '">
									<div class="g-form-field">
										<div class="g-recaptcha" data-sitekey="' . $public_key . '"></div>
									</div>
									<div class="g-form-row-state">' . $validate['recaptcha_valid_state'] . '</div>
								</div>';
	}

	$output .= '<div class="g-form-row for_agree ' . $validate['agree_state'] . '">
									<label for="agree" class="g-checkbox">
										<input type="hidden" value="0" name="agree">
										<input type="checkbox" value="1" id="agree" name="agree" class="not-empty"> Я принимаю правила использования сайта и даю согласие не обработку персональных данных</a>
									</label>
									<div class="g-form-row-state">' . $validate['agree_valid_state'] . '</div>
								</div>';

	$output .= '<div class="g-form-row for_submit">
									<input type="hidden" name="register_validation_required" value="TRUE">
									<button class="g-btn color_primary type_raised">
										<span class="g-preloader"></span>
										<span class="g-btn-label">Отправить</span>
									<span class="ripple-container"></span></button>
									<div class="g-form-field-message"></div>
								</div>

							</div>
						</form>';

	return $output;
}

?>
