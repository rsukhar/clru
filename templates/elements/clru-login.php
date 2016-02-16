<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a user login form.
 *
 */


$output = '<form method="post" class="g-form for_login align_left fixed">
	<div class="g-form-h">
		<h2 class="g-form-title">Авторизация</h2>

		<div class="g-form-row for_username">
			<div class="g-form-field">
				<input type="text" id="login_username" tabindex="1" name="username">
				<label for="login_username" class="g-form-field-label">Имя пользователя или email</label>

				<div class="g-form-field-bar"></div>
			</div>
			<div class="g-form-row-state"></div>
		</div>
		<div class="g-form-row for_password">
			<div class="g-form-field">
				<input type="password" id="login_password" tabindex="2" name="password">
				<label for="login_password" class="g-form-field-label">Пароль</label>

				<div class="g-form-field-bar"></div>
			</div>
			<div class="g-form-row-state"></div>
		</div>
		<div class="g-form-row for_submit">
			<button tabindex="4" class="g-btn color_primary type_raised">
				<span class="g-preloader"></span>
				<span class="g-btn-label">Войти</span>
			</button>
			<label for="auth_remember" class="g-checkbox">
				<input type="hidden" value="0" name="remember">
				<input type="checkbox" checked="" tabindex="3" value="1" id="auth_remember" name="remember" class="not-empty">Запомнить
				меня</label>

			<div class="g-form-field-message"></div>
		</div>
		<div class="g-form-row for_links">
			<a href="' . esc_attr( home_url( '/request_password_reset/' ) ) . '" class="g-form-field-link for_forgot">Забыли
				пароль?</a>
			<a href="' . esc_attr( home_url( '/register/' ) ) . '" class="g-form-field-link for_signup">Зарегистрироваться</a>
		</div>
	</div>
	<div href="javascript:void(0)" class="g-closer">&times;</div>
	<input type="hidden" name="action" value="do_login">';
$output .= wp_nonce_field( 'clru_do_login_nonce', '_ajax_nonce', TRUE, FALSE );
$output .= '</form>';

echo $output;
