<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$message = 'Привет, ' . $name_in_letter . '!' . "\r\n\r\n";
$message .= 'Кто-то запросил восстановление твоего пароля на сайте CodeLights.ru. Если это не ты, просто проигнорируй и удали это письмо.' . "\r\n\r\n";
$message .= __( 'To reset your password, visit the following address:' ) . "\r\n";
$message .= '<' . network_site_url( "reset_password?key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n\r\n";
$message .= 'С наилучшими пожеланиями,' . "\r\n";
$message .= 'Команда UpSolution' . "\r\n";

echo $message;
