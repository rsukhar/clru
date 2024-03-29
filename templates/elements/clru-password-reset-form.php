<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a password reset request form
 */

$output = '<form method="post" class="g-form for_resetpass">';
$output .= '<div class="g-form-h">';
$output .= '<h2 class="g-form-title">Восстановление пароля</h2>';

$output .= '<div class="g-form-row for_text">Введите email, который вы указали при регистрации.</div>';

$output .= '<div class="g-form-row for_email">
				<div class="g-form-field">
					<input type="text" value="" name="user_login" id="resetpass_email">
					<label for="resetpass_email" class="g-form-field-label">Email</label>
					<div class="g-form-field-bar"></div>
				</div>
				<div class="g-form-row-state"></div>
			</div>';

$output .= '<div class="g-form-row for_submit">
				<button class="g-btn color_primary type_raised">
					<span class="g-preloader"></span>
					<span class="g-btn-label">Восстановить пароль</span>
					<span class="ripple-container"></span>
				</button>
				<div class="g-form-field-message"></div>
			</div>

			</div>
			<input type="hidden" name="action" value="do_password_request">';
$output .= wp_nonce_field( 'clru_do_password_request_nonce', '_ajax_nonce', TRUE, FALSE );
$output .= '</form>';

echo $output;
