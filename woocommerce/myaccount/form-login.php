<?php
/**
 * Login / Register — Laboratory Account Portal.
 *
 * Restyled to the brief while preserving WooCommerce's native field names,
 * nonces, and hooks so authentication and registration work unchanged.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_customer_login_form' );

$tp_registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
?>

<header class="page-header">
	<div class="wrap">
		<h1><?php esc_html_e( 'Laboratory Account Portal', 'truepharm' ); ?></h1>
		<p><?php esc_html_e( 'Access your institutional history, download previous COAs, and track active logistics.', 'truepharm' ); ?></p>
	</div>
</header>

<section class="account-section">
	<div class="wrap account-grid" id="customer_login">

		<!-- Returning Login -->
		<div class="auth-card">
			<h2><?php esc_html_e( 'Returning Researchers', 'truepharm' ); ?></h2>
			<p><?php esc_html_e( 'Log in to access your dashboard, rewards, and order history.', 'truepharm' ); ?></p>

			<form class="woocommerce-form woocommerce-form-login login" method="post">

				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<div class="form-group">
					<label for="username"><?php esc_html_e( 'Email Address', 'truepharm' ); ?> *</label>
					<input type="text" class="form-control" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>" required>
				</div>

				<div class="form-group">
					<label for="password"><?php esc_html_e( 'Password', 'truepharm' ); ?> *</label>
					<input class="form-control" type="password" name="password" id="password" autocomplete="current-password" required>
				</div>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<div class="form-options">
					<div class="checkbox-group">
						<input class="woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever">
						<label for="rememberme"><?php esc_html_e( 'Remember me', 'truepharm' ); ?></label>
					</div>
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="lost-password"><?php esc_html_e( 'Lost your password?', 'truepharm' ); ?></a>
				</div>

				<?php do_action( 'tp_turnstile_widget' ); ?>

				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="btn-navy woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Access Account', 'truepharm' ); ?>"><?php esc_html_e( 'Access Account', 'truepharm' ); ?></button>

				<?php do_action( 'woocommerce_login_form_end' ); ?>

			</form>
		</div>

		<?php if ( $tp_registration_enabled ) : ?>
			<!-- New Registration -->
			<div class="auth-card register-card">
				<h2><?php esc_html_e( 'Register New Account', 'truepharm' ); ?></h2>
				<p><?php esc_html_e( 'Create a secure account to track orders and earn TruePharm Rewards.', 'truepharm' ); ?></p>

				<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

					<?php do_action( 'woocommerce_register_form_start' ); ?>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
						<div class="form-group">
							<label for="reg_username"><?php esc_html_e( 'Username', 'truepharm' ); ?> *</label>
							<input type="text" class="form-control" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>" required>
						</div>
					<?php endif; ?>

					<div class="form-group">
						<label for="reg_email"><?php esc_html_e( 'Email Address', 'truepharm' ); ?> *</label>
						<input type="email" class="form-control" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>" required>
					</div>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
						<div class="form-group">
							<label for="reg_password"><?php esc_html_e( 'Create Password', 'truepharm' ); ?> *</label>
							<input type="password" class="form-control" name="password" id="reg_password" autocomplete="new-password" required>
						</div>
					<?php else : ?>
						<p><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'truepharm' ); ?></p>
					<?php endif; ?>

					<?php
					/**
					 * Compliance checkbox is injected here via the
					 * woocommerce_register_form hook (see functions.php).
					 */
					do_action( 'woocommerce_register_form' );
					?>

					<?php do_action( 'tp_turnstile_widget' ); ?>

					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="btn-rosegold woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register Account', 'truepharm' ); ?>"><?php esc_html_e( 'Register Account', 'truepharm' ); ?></button>

					<?php do_action( 'woocommerce_register_form_end' ); ?>

				</form>
			</div>
		<?php endif; ?>

	</div>
</section>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
