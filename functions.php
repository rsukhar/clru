<?php
/* required for register form validation */
$validate = '';

add_action( 'wp_enqueue_scripts', 'clru_theme_enqueue_styles' );
function clru_theme_enqueue_styles() {
	wp_enqueue_style( 'codelights-parent-style', get_template_directory_uri() . '/style.css' );
}

add_action( 'wp_enqueue_scripts', 'clru_assets' );
function clru_assets() {
	wp_register_script( 'child-main', get_stylesheet_directory_uri() . '/assets/js/main.js', array(), FALSE, TRUE );
	wp_localize_script( 'child-main', 'clruAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script( 'child-main' );

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
	if ( $_POST['clru_register_validation_required'] != '' ) {
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
			if ( username_exists( trim( $_POST['username'] ) ) ) {
				$validate['username_exists_state'] .= 'Пользователь с таким логином уже существует. Выберите, пожалуйста, другой логин или <a href="' . esc_url( home_url( '/' ) ) . 'request_password_reset/">восстановите ваш пароль</a>, если забыли его. ';
				$validate['username_state'] = 'check_wrong';
				$register_error = 'error';
			}
			if ( filter_var( trim( $_POST['username'] ), FILTER_VALIDATE_EMAIL ) ) {
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
			if ( ! filter_var( trim( $_POST['email'] ), FILTER_VALIDATE_EMAIL ) ) {
				$validate['email_valid_state'] .= 'Вы ввели неправильный email. ';
				$validate['email_state'] = 'check_wrong';
				$register_error = 'error';
			}
			if ( email_exists( trim( $_POST['email'] ) ) ) {
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
			$validate['password1_valid_state'] = 'Необходимо ввести пароль.';
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
									<input type="hidden" name="clru_register_validation_required" value="TRUE">
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

/*
 * Based on core function retrieve_password(), located in wp-login.php
 * Changed a message text and a link to password reset form.
 */
function clru_retrieve_password() {
	global $wpdb, $wp_hasher;

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

	$message = 'Привет, ' . $name_in_letter . '!' . "\r\n\r\n";
	$message .= 'Кто-то запросил восстановление твоего пароля на сайте CodeLights.ru. Если это не ты, просто проигнорируй и удали это письмо.' . "\r\n\r\n";
	$message .= __( 'To reset your password, visit the following address:' ) . "\r\n";
	$message .= '<' . network_site_url( "reset_password?key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n\r\n";
	$message .= 'С наилучшими пожеланиями,' . "\r\n";
	$message .= 'Команда UpSolution' . "\r\n";

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

add_shortcode( 'cl-request-password-reset', 'clru_password_reset_form' );
function clru_password_reset_form( $atts ) {

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

	$output = '<form action="' . get_the_permalink() . '" method="post" class="g-form for_resetpass">';
	$output .= '<div class="g-form-h">';
	$output .= '<h2 class="g-form-title">Восстановление пароля</h2>';

	$output .= '<div class="g-form-row for_text">Введите email, который вы указали при регистрации.</div>';

	$output .= '<div class="g-form-row for_email ' . $validate['email_state'] . '">
									<div class="g-form-field">
										<input type="text" value="" name="user_login" id="resetpass_email">
										<label for="resetpass_email" class="g-form-field-label">Email</label>
										<div class="g-form-field-bar"></div>
									</div>
									<div class="g-form-row-state">' . $validate['email_valid_state'] . '</div>
								</div>';

	$output .= '<div class="g-form-row for_submit">
									<input type="hidden" name="clru_request_password_reset" value="TRUE">
									<button class="g-btn color_primary type_raised">
										<span class="g-preloader"></span>
										<span class="g-btn-label">Восстановить пароль</span>
									<span class="ripple-container"></span></button>
									<div class="g-form-field-message"></div>
								</div>

							</div>
						</form>';

	return $output;
}

/**
 * Ajax for login
 */
add_action( 'wp_ajax_do_login', 'clru_do_login' );
add_action( 'wp_ajax_nopriv_do_login', 'clru_do_login' );
function clru_do_login() {

	check_ajax_referer( 'clru_do_login_nonce', '_ajax_nonce' );

	if ( $_POST['username'] != '' AND $_POST['password'] != '' ) {
		if ( ! filter_var( trim( $_POST['username'] ), FILTER_VALIDATE_EMAIL ) ) {
			$user = get_user_by( 'login', trim( $_POST['username'] ) );
		} else if ( filter_var( trim( $_POST['username'] ), FILTER_VALIDATE_EMAIL ) ) {
			$user = get_user_by( 'email', trim( $_POST['username'] ) );
		} else {
			$errors['username'] = 'Неправильное имя пользователя.';
		}
		if ( $user ) {
			if ( ! wp_check_password( trim( $_POST['password'] ), $user->user_pass, $user->ID ) ) {
				$errors['password'] = 'Неправильные имя пользователя или пароль.';
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
			$errors['username'] = 'Неправильное имя пользователя.';
		}
	} else {
		$errors['username'] = 'Нужно ввести имя пользователя и пароль';
	}

	$response = array(
		'success' => $success,
		'errors' => $errors,
		'post_username' => $_POST['username'],
		'post_password' => $_POST['password'],
	);
	echo json_encode( $response );
	die();
}

add_shortcode( 'cl-reset-password', 'clru_new_password_form' );
function clru_new_password_form( $atts ) {

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

	$output = '<form action="' . get_the_permalink() . '" method="post" class="g-form for_newpass">';
	$output .= '<div class="g-form-h">';
	$output .= '<h2 class="g-form-title">Новый пароль</h2>';

	$output .= '<div class="g-form-row for_text">Введите новый пароль.</div>';

	$output .= '<div class="g-form-row for_password ' . $validate['password1_state'] . '">
									<div class="g-form-field">
										<input type="text" value="" name="password" id="newpass_password1">
										<label for="newpass_password1" class="g-form-field-label">Новый пароль</label>
										<div class="g-form-field-bar"></div>
									</div>
									<div class="g-form-row-state">' . $validate['password1_valid_state'] . '</div>
								</div>';

	$output .= '<div class="g-form-row for_password2 ' . $validate['password2_state'] . '">
									<div class="g-form-field">
										<input type="text" value="" name="password2" id="newpass_password2">
										<label for="newpass_password2" class="g-form-field-label">Повторите пароль</label>
										<div class="g-form-field-bar"></div>
									</div>
									<div class="g-form-row-state">' . $validate['password2_valid_state'] . '</div>
								</div>';

	$output .= '<div class="g-form-row for_submit">
									<button class="g-btn color_primary type_raised">
										<span class="g-preloader"></span>
										<span class="g-btn-label">Задать новый пароль</span>
									<span class="ripple-container"></span></button>
									<div class="g-form-field-message"></div>
								</div>

							</div>
							<input type="hidden" name="clru_new_password_request" value="TRUE">
							<input type="hidden" name="user_login" value="' . esc_attr( $user_login ) . '">
							<input type="hidden" name="user_activation_key" value="' . esc_attr( $activation_key ) . '">
						</form>';

	return $output;
}

?>
