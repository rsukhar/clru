<?php

$output = '<form action="' . get_the_permalink() . '" method="post" class="g-form align_left for_register">';
$output .= '<div class="g-form-h">';
$output .= '<h2 class="g-form-title">Создать аккаунт</h2>';
$output .= '<div class="g-form-row for_username ' . $validate['username_state'] . '">
				<div class="g-form-field">
					<input type="text" name="username" id="signup_username" value="' . esc_attr( $username ) . '">
					<label for="signup_username" class="g-form-field-label">Логин</label>
					<div class="g-form-field-bar"></div>
				</div>';
$output .= '<div class="g-form-row-state">' . $validate['username_exists_state'] . '</div>';
$output .= '</div>';

$output .= '<div class="g-form-row for_realname ' . $validate['realname_state'] . '">
				<div class="g-form-field">
					<input type="text" name="realname" id="signup_realname" value="' . esc_attr( $realname ) . '">
					<label for="signup_realname" class="g-form-field-label">Имя и фамилия</label>
					<div class="g-form-field-bar"></div>
				</div>';
$output .= '<div class="g-form-row-state">' . $validate['realname_valid_state'] . '</div>';
$output .= '</div>';

$output .= '<div class="g-form-row for_email ' . $validate['email_state'] . '">
				<div class="g-form-field">
					<input type="text" name="email" id="signup_email" value="' . esc_attr( $email ) . '">
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
					<span class="ripple-container"></span>
				</button>
				<div class="g-form-field-message"></div>
			</div>

			</div>
			</form>';

echo $output;
