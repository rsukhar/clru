<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a new password reset form
 *
 * @var $user_login String
 * @var $activation_key String
 */

$output = '<form method="post" class="g-form for_newpass">';
$output .= '<div class="g-form-h">';
$output .= '<h2 class="g-form-title">Новый пароль</h2>';

$output .= '<div class="g-form-row for_text">Введите новый пароль.</div>';

$output .= '<div class="g-form-row for_password">
				<div class="g-form-field">
					<input type="text" value="" name="password" id="newpass_password1">
					<label for="newpass_password1" class="g-form-field-label">Новый пароль</label>
					<div class="g-form-field-bar"></div>
				</div>
				<div class="g-form-row-state"></div>
			</div>';

$output .= '<div class="g-form-row for_password2">
				<div class="g-form-field">
					<input type="text" value="" name="password2" id="newpass_password2">
					<label for="newpass_password2" class="g-form-field-label">Повторите пароль</label>
					<div class="g-form-field-bar"></div>
				</div>
				<div class="g-form-row-state"></div>
			</div>';

$output .= '<div class="g-form-row for_submit">
				<button class="g-btn color_primary type_raised">
					<span class="g-preloader"></span>
					<span class="g-btn-label">Задать новый пароль</span>
					<span class="ripple-container"></span>
				</button>
				<div class="g-form-field-message"></div>
				</div>

			</div>
			<input type="hidden" name="action" value="do_new_password">
			<input type="hidden" name="user_login" value="' . esc_attr( $user_login ) . '">
			<input type="hidden" name="user_activation_key" value="' . esc_attr( $activation_key ) . '">';
$output .= wp_nonce_field( 'clru_do_new_password_nonce', '_ajax_nonce', TRUE, FALSE );
$output .= '</form>';

echo $output;
