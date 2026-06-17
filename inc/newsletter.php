<?php
/**
 * Newsletter capture — custom table + AJAX subscribe endpoint.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Bump when the schema changes to trigger a re-install. */
define( 'TP_NEWSLETTER_DB_VERSION', '1.0.0' );

/**
 * Fully-qualified table name (site prefix + tp_newsletter_emails).
 */
function tp_newsletter_table(): string {
	global $wpdb;
	return $wpdb->prefix . 'tp_newsletter_emails';
}

/**
 * Create / upgrade the newsletter table via dbDelta.
 */
function tp_newsletter_install(): void {
	global $wpdb;

	$table           = tp_newsletter_table();
	$charset_collate = $wpdb->get_charset_collate();

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE {$table} (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		email VARCHAR(190) NOT NULL,
		ip_address VARCHAR(45) DEFAULT '' NOT NULL,
		source VARCHAR(100) DEFAULT '' NOT NULL,
		created_at DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY email (email)
	) {$charset_collate};";

	dbDelta( $sql );

	update_option( 'tp_newsletter_db_version', TP_NEWSLETTER_DB_VERSION );
}
add_action( 'after_switch_theme', 'tp_newsletter_install' );

/**
 * Self-heal: create the table if the stored version is missing/outdated
 * (covers themes already active before this phase shipped).
 */
function tp_newsletter_maybe_install(): void {
	if ( get_option( 'tp_newsletter_db_version' ) !== TP_NEWSLETTER_DB_VERSION ) {
		tp_newsletter_install();
	}
}
add_action( 'after_setup_theme', 'tp_newsletter_maybe_install' );

/**
 * AJAX: store a newsletter subscriber.
 */
function tp_newsletter_subscribe(): void {
	check_ajax_referer( 'tp_ajax', 'nonce' );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( '' === $email || ! is_email( $email ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Please enter a valid email address.', 'truepharm' ) ),
			400
		);
	}

	global $wpdb;
	$table = tp_newsletter_table();

	// Already subscribed?
	$exists = $wpdb->get_var(
		$wpdb->prepare( "SELECT id FROM {$table} WHERE email = %s", $email ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	);

	if ( $exists ) {
		wp_send_json_success(
			array( 'message' => __( 'You are already on the list — thank you!', 'truepharm' ) )
		);
	}

	$inserted = $wpdb->insert(
		$table,
		array(
			'email'      => $email,
			'ip_address' => tp_newsletter_get_ip(),
			'source'     => 'homepage',
			'created_at' => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s' )
	);

	if ( false === $inserted ) {
		wp_send_json_error(
			array( 'message' => __( 'Something went wrong. Please try again.', 'truepharm' ) ),
			500
		);
	}

	/** Fires after a subscriber is stored (hook point for email-tool sync). */
	do_action( 'tp_newsletter_subscribed', $email );

	wp_send_json_success(
		array( 'message' => __( 'Success! Your 10% off code is on its way.', 'truepharm' ) )
	);
}
add_action( 'wp_ajax_tp_newsletter', 'tp_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_tp_newsletter', 'tp_newsletter_subscribe' );

/**
 * Best-effort client IP for audit purposes.
 */
function tp_newsletter_get_ip(): string {
	if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
	}
	return '';
}
