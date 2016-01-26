<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Header user panel block
 *
 * @action Before the template: 'us_before_template:templates/widgets/userpanel'
 * @action After the template: 'us_after_template:templates/widgets/userpanel'
 */

$output = '<div id="user-block" class="w-user-block">';
if ( ! is_user_logged_in() ) {
	$output .= '<div class="w-user-block-item"><a href="' . esc_attr( home_url( '/register/' ) ) . '" class="w-user-block-link for_signup">Зарегистрироваться</a></div>';
	$output .= '<div class="w-user-block-item"><a href="javascript:void(0)" class="w-user-block-link for_login i-login">Войти</a></div>';
} else {
	$output .= '<div class="w-user-block-item"><a href="' . wp_logout_url( home_url() ) . '" class="w-user-block-link for_logout i-logoutwp">Выйти</a></div>';
}
$output .= '</div>';

echo $output;
