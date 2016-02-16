<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a user register form.
 *
 * @var $public_key String
 */


$output = '<form method="post" class="g-form align_left for_register">
			<div class="g-form-h">
				<h2 class="g-form-title">Создать аккаунт</h2>
			<div class="g-form-row for_username">
				<div class="g-form-field">
					<input type="text" name="username" id="signup_username">
					<label for="signup_username" class="g-form-field-label">Логин</label>
					<div class="g-form-field-bar"></div>
				</div>
				<div class="g-form-row-state"></div>
			</div>

			<div class="g-form-row for_realname">
				<div class="g-form-field">
					<input type="text" name="realname" id="signup_realname">
					<label for="signup_realname" class="g-form-field-label">Имя и фамилия</label>
					<div class="g-form-field-bar"></div>
				</div>
				<div class="g-form-row-state"></div>
			</div>

			<div class="g-form-row for_email">
				<div class="g-form-field">
					<input type="text" name="email" id="signup_email">
					<label for="signup_email" class="g-form-field-label">Email</label>
					<div class="g-form-field-bar"></div>
				</div>
				<div class="g-form-row-state"></div>
			</div>

			<div class="g-form-row for_password">
				<div class="g-form-field">
					<input type="password" name="password" id="signup_password" value="">
					<label for="signup_password" class="g-form-field-label">Пароль</label>
					<div class="g-form-field-bar"></div>
				</div>
				<div class="g-form-row-state"></div>
			</div>

			<div class="g-form-row for_password">
				<div class="g-form-field">
					<input type="password" name="password2" id="signup_password2" value="">
					<label for="signup_password2" class="g-form-field-label">Подтвердите пароль</label>
					<div class="g-form-field-bar"></div>
				</div>
				<div class="g-form-row-state"></div>
			</div>';

if ( $public_key != '' ) {
	$output .= '<div class="g-form-row for_recaptcha">
					<div class="g-form-field">
						<input type="hidden" name="hidden_recaptcha" id="hidden_recaptcha" value="">
						<div class="g-recaptcha" data-sitekey="' . $public_key . '"></div>
					</div>
					<div class="g-form-row-state"></div>
				</div>';
}

$output .= '<div class="g-form-row for_agree">
				<label for="agree" class="g-checkbox">
					<input type="hidden" value="0" name="agree">
					<input type="checkbox" value="1" id="agree" name="agree" class="not-empty"> Я принимаю правила использования сайта и даю согласие не обработку персональных данных</a>
				</label>
				<div class="g-form-row-state"></div>
			</div>

			<div class="g-form-row for_submit">
				<button class="g-btn color_primary type_raised">
					<span class="g-preloader"></span>
					<span class="g-btn-label">Отправить</span>
					<span class="ripple-container"></span>
				</button>
				<div class="g-form-field-message"></div>
			</div>

			</div>

			<input type="hidden" name="action" value="do_register">';
$output .= wp_nonce_field( 'clru_do_register_nonce', '_ajax_nonce', TRUE, FALSE );
$output .= '</form>';

echo $output;
