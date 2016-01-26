<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$us_layout = US_Layout::instance();
?>
</div>
<!-- /CANVAS -->

<?php if ( $us_layout->footer_show_top OR $us_layout->footer_show_bottom ) { ?>

	<?php do_action( 'us_before_footer' ) ?>

	<?php
	$footer_classes = '';
	$footer_layout = us_get_option( 'footer_layout' );
	if ( $footer_layout != NULL ) {
		$footer_classes .= ' layout_' . $footer_layout;
	}
	?>
	<!-- FOOTER -->
	<div class="l-footer<?php echo $footer_classes; ?>">

		<?php if ( $us_layout->footer_show_top ): ?>
			<!-- subfooter: top -->
			<div class="l-subfooter at_top">
				<div class="l-subfooter-h i-cf">

					<?php do_action( 'us_top_subfooter_start' ) ?>

					<div class="g-cols offset_medium">
						<?php
						$columns_number = (int) us_get_option( 'footer_columns', 3 );
						if ( $columns_number < 1 OR $columns_number > 4 ) {
							$columns_number = 3;
						}
						$columns_classes = array(
							1 => 'full-width',
							2 => 'one-half',
							3 => 'one-third',
							4 => 'one-quarter',
						);
						$columns_class = $columns_classes[ $columns_number ];
						$widget_names = array(
							1 => 'footer_first',
							2 => 'footer_second',
							3 => 'footer_third',
							4 => 'footer_fourth',
						);
						for ( $i = 1; $i <= $columns_number; $i ++ ) {
							?>
							<div class="<?php echo $columns_class ?>">
								<?php dynamic_sidebar( $widget_names[ $i ] ) ?>
							</div>
							<?php
						}
						?>
					</div>

					<?php do_action( 'us_top_subfooter_end' ) ?>

				</div>
			</div>
		<?php endif/*( $us_layout->footer_show_top )*/
		; ?>

		<?php if ( $us_layout->footer_show_bottom ): ?>
			<!-- subfooter: bottom -->
			<div class="l-subfooter at_bottom">
				<div class="l-subfooter-h i-cf">

					<?php do_action( 'us_bottom_subfooter_start' ) ?>

					<?php us_load_template( 'templates/widgets/nav-footer' ) ?>

					<div class="w-copyright"><?php echo us_get_option( 'footer_copyright', '' ) ?></div>

					<?php do_action( 'us_bottom_subfooter_end' ) ?>

				</div>
			</div>
		<?php endif/*( $us_layout->footer_show_bottom )*/
		; ?>

	</div>
	<!-- /FOOTER -->

	<?php do_action( 'us_after_footer' ) ?>

<?php }/*( $us_layout->footer_show_top OR $us_layout->footer_show_bottom )*/; ?>

<a class="w-toplink" href="#" title="<?php _e( 'Back to top', 'us' ); ?>"></a>
<script type="text/javascript">
	if (window.$us === undefined) window.$us = {};
	$us.canvasOptions = ($us.canvasOptions || {});
	$us.canvasOptions.disableStickyHeaderWidth = <?php echo intval( us_get_option( 'header_sticky_disable_width', 900 ) ) ?>;
	$us.canvasOptions.disableEffectsWidth = <?php echo intval( us_get_option( 'disable_effects_width', 900 ) ) ?>;
	$us.canvasOptions.headerScrollBreakpoint = <?php echo intval( us_get_option( 'header_scroll_breakpoint', 100 ) ) ?>;
	$us.canvasOptions.responsive = <?php echo us_get_option( 'responsive_layout', TRUE ) ? 'true' : 'false' ?>;

	$us.langOptions = ($us.langOptions || {});
	$us.langOptions.magnificPopup = ($us.langOptions.magnificPopup || {});
	$us.langOptions.magnificPopup.tPrev = '<?php _e( 'Previous (Left arrow key)', 'us' ); ?>' // Alt text on left arrow
	$us.langOptions.magnificPopup.tNext = '<?php _e( 'Next (Right arrow key)', 'us' ); ?>' // Alt text on right arrow
	$us.langOptions.magnificPopup.tCounter = '<?php _ex( '%curr% of %total%', 'Example: 3 of 12' , 'us' ); ?>' // Markup for "1 of 7" counter

	$us.navOptions = ($us.navOptions || {});
	$us.navOptions.mobileWidth = <?php echo intval( us_get_option( 'menu_mobile_width', 900 ) ) ?>;
	$us.navOptions.togglable = <?php echo us_get_option( 'menu_togglable_type', TRUE ) ? 'true' : 'false' ?>;
</script>
<?php echo us_get_option( 'custom_html', '' ) ?>

<form method="post" class="g-form for_login align_left fixed">
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
			<a href="<?php echo esc_attr( home_url( '/request_password_reset/' ) ); ?>" class="g-form-field-link for_forgot">Забыли
				пароль?</a>
			<a href="<?php echo esc_attr( home_url( '/register/' ) ); ?>" class="g-form-field-link for_signup">Зарегистрироваться</a>
		</div>
	</div>
	<div href="javascript:void(0)" class="g-closer">&times;</div>
	<input type="hidden" name="action" value="do_login">
	<?php wp_nonce_field( 'clru_do_login_nonce', '_ajax_nonce' ); ?>
</form>

<?php wp_footer(); ?>

</body>
</html>
