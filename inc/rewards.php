<?php
/**
 * TruePharm Rewards — custom points engine (user meta, no plugin).
 *
 * Earn: signup, $1-per-point on completed orders, product reviews, birthdays,
 * and referrals. Redeem: generate single-use WooCommerce coupons.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------
 * Constants
 * ------------------------------------------------------------------- */
if ( ! defined( 'TP_REWARDS_SIGNUP_POINTS' ) ) {
	define( 'TP_REWARDS_SIGNUP_POINTS', 50 );
}
if ( ! defined( 'TP_REWARDS_POINTS_PER_DOLLAR' ) ) {
	define( 'TP_REWARDS_POINTS_PER_DOLLAR', 1 );
}
if ( ! defined( 'TP_REWARDS_REVIEW_POINTS' ) ) {
	define( 'TP_REWARDS_REVIEW_POINTS', 100 );
}
if ( ! defined( 'TP_REWARDS_BIRTHDAY_POINTS' ) ) {
	define( 'TP_REWARDS_BIRTHDAY_POINTS', 200 );
}
if ( ! defined( 'TP_REWARDS_REFERRAL_POINTS' ) ) {
	define( 'TP_REWARDS_REFERRAL_POINTS', 200 );
}
if ( ! defined( 'TP_REWARDS_REFERRAL_DISCOUNT' ) ) {
	define( 'TP_REWARDS_REFERRAL_DISCOUNT', 20 );
}
if ( ! defined( 'TP_REWARDS_POINTS_VALUE' ) ) {
	define( 'TP_REWARDS_POINTS_VALUE', 0.10 ); // 1 point = $0.10.
}

/** Minimum / increment for redemption. */
if ( ! defined( 'TP_REWARDS_REDEEM_STEP' ) ) {
	define( 'TP_REWARDS_REDEEM_STEP', 100 );
}

/** User meta keys. */
const TP_REWARDS_POINTS_KEY    = 'tp_rewards_points';
const TP_REWARDS_LEDGER_KEY    = 'tp_rewards_ledger';
const TP_REWARDS_REFCODE_KEY   = 'tp_rewards_referral_code';
const TP_REWARDS_REFERRED_KEY  = 'tp_rewards_referred_by';
const TP_REWARDS_BIRTHDAY_KEY  = 'tp_rewards_birthday';
const TP_REWARDS_REVIEWED_KEY  = 'tp_rewards_reviewed_products';
const TP_REWARDS_BDAY_YEAR_KEY = 'tp_rewards_birthday_year';
const TP_REWARDS_COOKIE        = 'tp_referral_code';

/* ---------------------------------------------------------------------
 * Core balance + ledger
 * ------------------------------------------------------------------- */
function tp_rewards_get_balance( int $user_id = 0 ): int {
	if ( 0 === $user_id ) {
		$user_id = get_current_user_id();
	}
	if ( 0 === $user_id ) {
		return 0;
	}
	return max( 0, (int) get_user_meta( $user_id, TP_REWARDS_POINTS_KEY, true ) );
}

function tp_rewards_get_ledger( int $user_id = 0 ): array {
	if ( 0 === $user_id ) {
		$user_id = get_current_user_id();
	}
	$ledger = get_user_meta( $user_id, TP_REWARDS_LEDGER_KEY, true );
	return is_array( $ledger ) ? $ledger : array();
}

/**
 * Append a ledger entry: [date, reason, points, balance].
 */
function tp_rewards_log( int $user_id, int $points, string $reason, int $balance ): void {
	$ledger   = tp_rewards_get_ledger( $user_id );
	$ledger[] = array(
		'date'    => current_time( 'mysql' ),
		'reason'  => $reason,
		'points'  => $points,
		'balance' => $balance,
	);
	update_user_meta( $user_id, TP_REWARDS_LEDGER_KEY, $ledger );
}

function tp_rewards_add_points( int $user_id, int $points, string $reason ): bool {
	if ( $user_id <= 0 || $points <= 0 ) {
		return false;
	}
	$balance = tp_rewards_get_balance( $user_id ) + $points;
	update_user_meta( $user_id, TP_REWARDS_POINTS_KEY, $balance );
	tp_rewards_log( $user_id, $points, $reason, $balance );

	/** Fires after points are added. */
	do_action( 'tp_rewards_points_added', $user_id, $points, $reason, $balance );
	return true;
}

function tp_rewards_deduct_points( int $user_id, int $points, string $reason ): bool {
	if ( $user_id <= 0 || $points <= 0 ) {
		return false;
	}
	$current = tp_rewards_get_balance( $user_id );
	if ( $current < $points ) {
		return false;
	}
	$balance = $current - $points;
	update_user_meta( $user_id, TP_REWARDS_POINTS_KEY, $balance );
	tp_rewards_log( $user_id, -$points, $reason, $balance );

	/** Fires after points are deducted. */
	do_action( 'tp_rewards_points_deducted', $user_id, $points, $reason, $balance );
	return true;
}

function tp_rewards_points_to_value( int $points ): float {
	return round( $points * TP_REWARDS_POINTS_VALUE, 2 );
}

/* ---------------------------------------------------------------------
 * Referral codes
 * ------------------------------------------------------------------- */
function tp_rewards_generate_referral_code( int $user_id ): string {
	$user = get_userdata( $user_id );
	$base = $user ? preg_replace( '/[^A-Za-z0-9]/', '', $user->user_login ) : 'USER';
	$base = strtoupper( substr( $base, 0, 5 ) );
	if ( '' === $base ) {
		$base = 'USER';
	}
	return 'TPUSA-' . $base . $user_id;
}

function tp_rewards_get_referral_code( int $user_id = 0 ): string {
	if ( 0 === $user_id ) {
		$user_id = get_current_user_id();
	}
	if ( 0 === $user_id ) {
		return '';
	}
	$code = (string) get_user_meta( $user_id, TP_REWARDS_REFCODE_KEY, true );
	if ( '' === $code ) {
		$code = tp_rewards_generate_referral_code( $user_id );
		update_user_meta( $user_id, TP_REWARDS_REFCODE_KEY, $code );
	}
	return $code;
}

/* ---------------------------------------------------------------------
 * Back-compat accessors (used by the homepage rewards section).
 * ------------------------------------------------------------------- */
function tp_rewards_signup_bonus(): int {
	return (int) apply_filters( 'tp_rewards_signup_bonus', TP_REWARDS_SIGNUP_POINTS );
}
function tp_rewards_points_per_dollar(): int {
	return (int) apply_filters( 'tp_rewards_points_per_dollar', TP_REWARDS_POINTS_PER_DOLLAR );
}
function tp_rewards_redeem_points(): int {
	return (int) apply_filters( 'tp_rewards_redeem_points', TP_REWARDS_REDEEM_STEP );
}
function tp_rewards_redeem_value(): float {
	return tp_rewards_points_to_value( tp_rewards_redeem_points() );
}
function tp_rewards_redeem_value_display(): string {
	if ( function_exists( 'wc_price' ) ) {
		return wp_strip_all_tags( wc_price( tp_rewards_redeem_value(), array( 'decimals' => 0 ) ) );
	}
	return '$' . number_format_i18n( tp_rewards_redeem_value() );
}

/* ---------------------------------------------------------------------
 * Referral cookie — capture ?ref=CODE.
 * ------------------------------------------------------------------- */
function tp_rewards_capture_referral(): void {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( empty( $_GET['ref'] ) || is_admin() ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$code = sanitize_text_field( wp_unslash( $_GET['ref'] ) );
	if ( '' !== $code && ! headers_sent() ) {
		setcookie( TP_REWARDS_COOKIE, $code, time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN );
		$_COOKIE[ TP_REWARDS_COOKIE ] = $code;
	}
}
add_action( 'init', 'tp_rewards_capture_referral' );

/* ---------------------------------------------------------------------
 * Earn — signup + referral resolution.
 * ------------------------------------------------------------------- */
function tp_rewards_on_register( int $user_id ): void {
	// Signup bonus.
	tp_rewards_add_points( $user_id, tp_rewards_signup_bonus(), __( 'Account signup bonus', 'truepharm' ) );

	// Assign a referral code.
	tp_rewards_get_referral_code( $user_id );

	// Resolve referral cookie.
	if ( ! empty( $_COOKIE[ TP_REWARDS_COOKIE ] ) ) {
		$code      = sanitize_text_field( wp_unslash( $_COOKIE[ TP_REWARDS_COOKIE ] ) );
		$referrers = get_users(
			array(
				'meta_key'   => TP_REWARDS_REFCODE_KEY, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value' => $code, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'number'     => 1,
				'fields'     => 'ID',
			)
		);
		$referrer_id = ! empty( $referrers ) ? (int) $referrers[0] : 0;

		if ( $referrer_id && $referrer_id !== $user_id ) {
			tp_rewards_add_points( $referrer_id, TP_REWARDS_REFERRAL_POINTS, __( 'Colleague referral completed', 'truepharm' ) );
			update_user_meta( $user_id, TP_REWARDS_REFERRED_KEY, $referrer_id );
		}

		// Clear the cookie.
		if ( ! headers_sent() ) {
			setcookie( TP_REWARDS_COOKIE, '', time() - HOUR_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN );
		}
		unset( $_COOKIE[ TP_REWARDS_COOKIE ] );
	}
}
add_action( 'user_register', 'tp_rewards_on_register' );

/* ---------------------------------------------------------------------
 * Earn — completed orders.
 * ------------------------------------------------------------------- */
function tp_rewards_on_order_completed( int $order_id ): void {
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}
	if ( $order->get_meta( '_tp_rewards_awarded' ) ) {
		return; // Already awarded.
	}
	$user_id = $order->get_user_id();
	if ( ! $user_id ) {
		return;
	}

	$points = (int) floor( (float) $order->get_subtotal() ) * tp_rewards_points_per_dollar();
	if ( $points > 0 ) {
		tp_rewards_add_points(
			$user_id,
			$points,
			sprintf( /* translators: %s: order number. */ __( 'Purchase (Order #%s)', 'truepharm' ), $order->get_order_number() )
		);
	}
	$order->update_meta_data( '_tp_rewards_awarded', 1 );
	$order->save();
}
add_action( 'woocommerce_order_status_completed', 'tp_rewards_on_order_completed' );

/* ---------------------------------------------------------------------
 * Earn — approved product reviews (once per product).
 * ------------------------------------------------------------------- */
function tp_rewards_award_review( int $comment_id ): void {
	$comment = get_comment( $comment_id );
	if ( ! $comment || (int) $comment->user_id <= 0 ) {
		return;
	}
	$product_id = (int) $comment->comment_post_ID;
	if ( 'product' !== get_post_type( $product_id ) ) {
		return;
	}

	$user_id  = (int) $comment->user_id;
	$reviewed = get_user_meta( $user_id, TP_REWARDS_REVIEWED_KEY, true );
	$reviewed = is_array( $reviewed ) ? $reviewed : array();
	if ( in_array( $product_id, $reviewed, true ) ) {
		return; // Already rewarded for this product.
	}

	tp_rewards_add_points( $user_id, TP_REWARDS_REVIEW_POINTS, __( 'Product review submitted', 'truepharm' ) );
	$reviewed[] = $product_id;
	update_user_meta( $user_id, TP_REWARDS_REVIEWED_KEY, $reviewed );
}

function tp_rewards_review_on_post( int $comment_id, $approved ): void {
	if ( 1 === (int) $approved ) {
		tp_rewards_award_review( $comment_id );
	}
}
add_action( 'comment_post', 'tp_rewards_review_on_post', 10, 2 );

function tp_rewards_review_on_status( string $new_status, string $old_status, WP_Comment $comment ): void {
	if ( 'approved' === $new_status && 'approved' !== $old_status ) {
		tp_rewards_award_review( (int) $comment->comment_ID );
	}
}
add_action( 'transition_comment_status', 'tp_rewards_review_on_status', 10, 3 );

/* ---------------------------------------------------------------------
 * Earn — birthday (daily cron).
 * ------------------------------------------------------------------- */
function tp_rewards_schedule_birthday_cron(): void {
	if ( ! wp_next_scheduled( 'tp_birthday_check' ) ) {
		wp_schedule_event( time(), 'daily', 'tp_birthday_check' );
	}
}
add_action( 'init', 'tp_rewards_schedule_birthday_cron' );

function tp_rewards_birthday_check(): void {
	$today = gmdate( 'm-d' );
	$year  = (int) gmdate( 'Y' );

	$users = get_users(
		array(
			'meta_key' => TP_REWARDS_BIRTHDAY_KEY, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'fields'   => array( 'ID' ),
		)
	);

	foreach ( $users as $user ) {
		$uid = (int) $user->ID;
		$bd  = (string) get_user_meta( $uid, TP_REWARDS_BIRTHDAY_KEY, true );
		if ( $bd !== $today ) {
			continue;
		}
		if ( (int) get_user_meta( $uid, TP_REWARDS_BDAY_YEAR_KEY, true ) === $year ) {
			continue; // Already awarded this year.
		}
		tp_rewards_add_points( $uid, TP_REWARDS_BIRTHDAY_POINTS, __( 'Birthday reward', 'truepharm' ) );
		update_user_meta( $uid, TP_REWARDS_BDAY_YEAR_KEY, $year );
	}
}
add_action( 'tp_birthday_check', 'tp_rewards_birthday_check' );

/* ---------------------------------------------------------------------
 * Redeem — AJAX → single-use WooCommerce coupon.
 * ------------------------------------------------------------------- */
function tp_rewards_redeem(): void {
	check_ajax_referer( 'tp_ajax', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Please log in to redeem points.', 'truepharm' ) ), 403 );
	}

	$user_id = get_current_user_id();
	$points  = isset( $_POST['points_to_redeem'] ) ? absint( wp_unslash( $_POST['points_to_redeem'] ) ) : 0;

	if ( $points < TP_REWARDS_REDEEM_STEP || 0 !== $points % TP_REWARDS_REDEEM_STEP ) {
		wp_send_json_error(
			array(
				/* translators: %d: redemption step. */
				'message' => sprintf( __( 'Points must be a multiple of %d.', 'truepharm' ), TP_REWARDS_REDEEM_STEP ),
			),
			400
		);
	}

	if ( tp_rewards_get_balance( $user_id ) < $points ) {
		wp_send_json_error( array( 'message' => __( 'Insufficient points balance.', 'truepharm' ) ), 400 );
	}

	if ( ! class_exists( 'WC_Coupon' ) ) {
		wp_send_json_error( array( 'message' => __( 'Coupons are unavailable.', 'truepharm' ) ), 500 );
	}

	$amount = tp_rewards_points_to_value( $points );
	$code   = strtoupper( 'TP-REDEEM-' . $user_id . '-' . time() );
	$user   = wp_get_current_user();

	$coupon = new WC_Coupon();
	$coupon->set_code( $code );
	$coupon->set_discount_type( 'fixed_cart' );
	$coupon->set_amount( $amount );
	$coupon->set_individual_use( true );
	$coupon->set_usage_limit( 1 );
	$coupon->set_usage_limit_per_user( 1 );
	$coupon->set_date_expires( time() + ( 30 * DAY_IN_SECONDS ) );
	$coupon->set_description( sprintf( /* translators: 1: points, 2: user. */ __( 'Rewards redemption: %1$d points by user #%2$d', 'truepharm' ), $points, $user_id ) );
	if ( $user && $user->user_email ) {
		$coupon->set_email_restrictions( array( $user->user_email ) );
	}
	$coupon->save();

	if ( ! tp_rewards_deduct_points( $user_id, $points, sprintf( /* translators: %s: coupon code. */ __( 'Redeemed coupon %s', 'truepharm' ), $code ) ) ) {
		// Roll back the coupon if deduction failed.
		wp_delete_post( $coupon->get_id(), true );
		wp_send_json_error( array( 'message' => __( 'Redemption failed. Please try again.', 'truepharm' ) ), 500 );
	}

	wp_send_json_success(
		array(
			'code'    => $code,
			'amount'  => wp_strip_all_tags( wc_price( $amount ) ),
			'balance' => tp_rewards_get_balance( $user_id ),
			/* translators: 1: discount, 2: code. */
			'message' => sprintf( __( 'Success! %1$s coupon "%2$s" created.', 'truepharm' ), wp_strip_all_tags( wc_price( $amount ) ), $code ),
		)
	);
}
add_action( 'wp_ajax_tp_redeem_points', 'tp_rewards_redeem' );
