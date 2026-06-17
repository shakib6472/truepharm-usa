<?php
/**
 * Cloudflare Turnstile — placeholder hook system + optional live widget.
 *
 * Until a Site Key + Secret Key are entered in the Customizer the theme shows
 * a styled placeholder and verification is a no-op (so forms keep working).
 * Once keys are set, the real widget renders and submissions are verified.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TP_TURNSTILE_VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

function tp_turnstile_site_key(): string {
	return trim( (string) get_theme_mod( 'tp_turnstile_site_key', '' ) );
}
function tp_turnstile_secret_key(): string {
	return trim( (string) get_theme_mod( 'tp_turnstile_secret_key', '' ) );
}
function tp_turnstile_is_configured(): bool {
	return '' !== tp_turnstile_site_key() && '' !== tp_turnstile_secret_key();
}

/* ---------------------------------------------------------------------
 * Widget output — do_action( 'tp_turnstile_widget' ).
 * ------------------------------------------------------------------- */
function tp_turnstile_render_widget(): void {
	if ( tp_turnstile_is_configured() ) {
		printf(
			'<div class="tp-turnstile cf-turnstile" data-sitekey="%s"></div>',
			esc_attr( tp_turnstile_site_key() )
		);
	} else {
		echo '<div class="tp-turnstile-placeholder">' . esc_html__( '[Cloudflare Turnstile widget will appear here]', 'truepharm' ) . '</div>';
	}
}
add_action( 'tp_turnstile_widget', 'tp_turnstile_render_widget' );

/**
 * Load Cloudflare's API script when the widget is live.
 */
function tp_turnstile_enqueue(): void {
	if ( tp_turnstile_is_configured() ) {
		wp_enqueue_script(
			'cloudflare-turnstile',
			'https://challenges.cloudflare.com/turnstile/v0/api.js',
			array(),
			null, // phpcs:ignore -- third-party, no version query.
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'tp_turnstile_enqueue' );

/* ---------------------------------------------------------------------
 * Server-side verification.
 * ------------------------------------------------------------------- */
function tp_turnstile_verify( string $token = '' ): bool {
	// Not configured → pass-through so forms work in placeholder mode.
	if ( ! tp_turnstile_is_configured() ) {
		return true;
	}

	if ( '' === $token ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$token = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : '';
	}
	if ( '' === $token ) {
		return false;
	}

	$response = wp_remote_post(
		TP_TURNSTILE_VERIFY_URL,
		array(
			'timeout' => 10,
			'body'    => array(
				'secret'   => tp_turnstile_secret_key(),
				'response' => $token,
				'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	return ! empty( $body['success'] );
}

/* ---------------------------------------------------------------------
 * Integration — registration, login, checkout, contact.
 * ------------------------------------------------------------------- */
function tp_turnstile_validate_registration( $errors ) {
	if ( tp_turnstile_is_configured() && ! tp_turnstile_verify() ) {
		$errors->add( 'tp_turnstile', __( 'Human verification failed. Please try again.', 'truepharm' ) );
	}
	return $errors;
}
add_filter( 'woocommerce_registration_errors', 'tp_turnstile_validate_registration', 10, 1 );

function tp_turnstile_validate_login( $user, $password = '' ) {
	// Only enforce on the WooCommerce front-end login (avoid wp-admin lockout).
	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( isset( $_POST['woocommerce-login-nonce'] ) && tp_turnstile_is_configured() && ! tp_turnstile_verify() ) {
		return new WP_Error( 'tp_turnstile', __( 'Human verification failed. Please try again.', 'truepharm' ) );
	}
	return $user;
}
add_filter( 'wp_authenticate_user', 'tp_turnstile_validate_login', 10, 2 );

function tp_turnstile_validate_checkout(): void {
	if ( tp_turnstile_is_configured() && ! tp_turnstile_verify() ) {
		wc_add_notice( __( 'Human verification failed. Please complete the security check.', 'truepharm' ), 'error' );
	}
}
add_action( 'woocommerce_checkout_process', 'tp_turnstile_validate_checkout' );

/**
 * Contact form: filter that receives and returns a WP_Error.
 */
function tp_turnstile_validate_contact( $errors ) {
	if ( ! ( $errors instanceof WP_Error ) ) {
		$errors = new WP_Error();
	}
	if ( tp_turnstile_is_configured() && ! tp_turnstile_verify() ) {
		$errors->add( 'tp_turnstile', __( 'Human verification failed. Please try again.', 'truepharm' ) );
	}
	return $errors;
}
add_filter( 'tp_contact_form_validate', 'tp_turnstile_validate_contact', 10, 1 );

/* ---------------------------------------------------------------------
 * Customizer — Site Key + Secret Key.
 * ------------------------------------------------------------------- */
function tp_turnstile_customize( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'tp_turnstile_section',
		array(
			'title'       => __( 'Cloudflare Turnstile', 'truepharm' ),
			'description' => __( 'Enter your Cloudflare Turnstile keys to enable bot protection on the contact, login, register, and checkout forms.', 'truepharm' ),
			'priority'    => 160,
		)
	);

	$wp_customize->add_setting(
		'tp_turnstile_site_key',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'tp_turnstile_site_key',
		array(
			'label'   => __( 'Site Key', 'truepharm' ),
			'section' => 'tp_turnstile_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'tp_turnstile_secret_key',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'tp_turnstile_secret_key',
		array(
			'label'       => __( 'Secret Key', 'truepharm' ),
			'description' => __( 'Kept server-side; used to verify submissions.', 'truepharm' ),
			'section'     => 'tp_turnstile_section',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'tp_turnstile_customize' );
