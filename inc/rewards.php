<?php
/**
 * TruePharm Rewards — program constants & accessors.
 *
 * These constants define the economics of the custom points engine and are
 * the single source of truth used across the site (homepage rewards section,
 * account dashboard, and the earn/redeem logic added in a later phase).
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Points awarded once when a customer creates an account. */
if ( ! defined( 'TP_REWARDS_SIGNUP_BONUS' ) ) {
	define( 'TP_REWARDS_SIGNUP_BONUS', 100 );
}

/** Points earned per $1 spent on a completed order. */
if ( ! defined( 'TP_REWARDS_POINTS_PER_DOLLAR' ) ) {
	define( 'TP_REWARDS_POINTS_PER_DOLLAR', 1 );
}

/** Points required to redeem one discount block. */
if ( ! defined( 'TP_REWARDS_REDEEM_POINTS' ) ) {
	define( 'TP_REWARDS_REDEEM_POINTS', 100 );
}

/** Dollar value of one redeemed discount block. */
if ( ! defined( 'TP_REWARDS_REDEEM_VALUE' ) ) {
	define( 'TP_REWARDS_REDEEM_VALUE', 5 );
}

/** User meta key holding a member's current points balance. */
if ( ! defined( 'TP_REWARDS_META_KEY' ) ) {
	define( 'TP_REWARDS_META_KEY', '_tp_rewards_points' );
}

/**
 * Signup bonus points (filterable).
 */
function tp_rewards_signup_bonus(): int {
	return (int) apply_filters( 'tp_rewards_signup_bonus', TP_REWARDS_SIGNUP_BONUS );
}

/**
 * Points earned per dollar spent (filterable).
 */
function tp_rewards_points_per_dollar(): int {
	return (int) apply_filters( 'tp_rewards_points_per_dollar', TP_REWARDS_POINTS_PER_DOLLAR );
}

/**
 * Points required per redemption block (filterable).
 */
function tp_rewards_redeem_points(): int {
	return (int) apply_filters( 'tp_rewards_redeem_points', TP_REWARDS_REDEEM_POINTS );
}

/**
 * Dollar value of one redemption block (filterable).
 */
function tp_rewards_redeem_value(): int {
	return (int) apply_filters( 'tp_rewards_redeem_value', TP_REWARDS_REDEEM_VALUE );
}

/**
 * Formatted dollar value of one redemption block (e.g. "$5").
 */
function tp_rewards_redeem_value_display(): string {
	if ( function_exists( 'wc_price' ) ) {
		return wp_strip_all_tags( wc_price( tp_rewards_redeem_value(), array( 'decimals' => 0 ) ) );
	}
	return '$' . tp_rewards_redeem_value();
}

/**
 * Current points balance for a user (0 when none / logged out).
 */
function tp_rewards_get_balance( int $user_id = 0 ): int {
	if ( 0 === $user_id ) {
		$user_id = get_current_user_id();
	}
	if ( 0 === $user_id ) {
		return 0;
	}
	return (int) get_user_meta( $user_id, TP_REWARDS_META_KEY, true );
}
