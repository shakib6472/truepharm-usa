<?php
/**
 * Contact form — custom table + AJAX handler + email notification.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TP_CONTACT_DB_VERSION', '1.0.0' );

function tp_contact_table(): string {
	global $wpdb;
	return $wpdb->prefix . 'tp_contact_submissions';
}

/**
 * Create / upgrade the submissions table.
 */
function tp_contact_install(): void {
	global $wpdb;
	$table           = tp_contact_table();
	$charset_collate = $wpdb->get_charset_collate();

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE {$table} (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		name VARCHAR(190) NOT NULL,
		email VARCHAR(190) NOT NULL,
		order_number VARCHAR(100) DEFAULT '' NOT NULL,
		subject VARCHAR(190) DEFAULT '' NOT NULL,
		message TEXT NOT NULL,
		submitted_at DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
		PRIMARY KEY  (id),
		KEY email (email)
	) {$charset_collate};";

	dbDelta( $sql );
	update_option( 'tp_contact_db_version', TP_CONTACT_DB_VERSION );
}
add_action( 'after_switch_theme', 'tp_contact_install' );

function tp_contact_maybe_install(): void {
	if ( get_option( 'tp_contact_db_version' ) !== TP_CONTACT_DB_VERSION ) {
		tp_contact_install();
	}
}
add_action( 'after_setup_theme', 'tp_contact_maybe_install' );

/**
 * AJAX: process a contact submission.
 */
function tp_submit_contact(): void {
	check_ajax_referer( 'tp_ajax', 'nonce' );

	// Turnstile + extensible validation (filter returns a WP_Error).
	$errors = apply_filters( 'tp_contact_form_validate', new WP_Error() );
	if ( is_wp_error( $errors ) && $errors->has_errors() ) {
		wp_send_json_error( array( 'message' => $errors->get_error_message() ), 400 );
	}

	$first   = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last    = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$order   = isset( $_POST['order_number'] ) ? sanitize_text_field( wp_unslash( $_POST['order_number'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	$name = trim( $first . ' ' . $last );

	if ( '' === $name || '' === $email || ! is_email( $email ) || '' === $subject || '' === $message ) {
		wp_send_json_error( array( 'message' => __( 'Please complete all required fields with a valid email address.', 'truepharm' ) ), 400 );
	}

	global $wpdb;
	$inserted = $wpdb->insert(
		tp_contact_table(),
		array(
			'name'         => $name,
			'email'        => $email,
			'order_number' => $order,
			'subject'      => $subject,
			'message'      => $message,
			'submitted_at' => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s' )
	);

	if ( false === $inserted ) {
		wp_send_json_error( array( 'message' => __( 'Something went wrong saving your message. Please try again.', 'truepharm' ) ), 500 );
	}

	// Notify the admin.
	$admin   = get_option( 'admin_email' );
	$mail_subject = sprintf( /* translators: %s: subject. */ __( '[TruePharm Contact] %s', 'truepharm' ), $subject );
	$body    = sprintf(
		"Name: %s\nEmail: %s\nOrder #: %s\nSubject: %s\n\nMessage:\n%s\n",
		$name,
		$email,
		$order ? $order : '—',
		$subject,
		$message
	);
	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );
	wp_mail( $admin, $mail_subject, $body, $headers );

	/** Fires after a contact submission is stored + emailed. */
	do_action( 'tp_contact_submitted', $name, $email, $subject, $message );

	wp_send_json_success( array( 'message' => __( 'Thank you — your inquiry has been received. Our team will respond within 24–48 hours.', 'truepharm' ) ) );
}
add_action( 'wp_ajax_tp_submit_contact', 'tp_submit_contact' );
add_action( 'wp_ajax_nopriv_tp_submit_contact', 'tp_submit_contact' );
