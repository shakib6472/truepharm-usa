<?php
/**
 * Quantity-based bundle pricing.
 *
 * Buying multiple units of the SAME product (variations counted toward their
 * parent) earns an automatic per-unit discount:
 *   3+  → 5% off
 *   5+  → 10% off
 *   10+ → 20% off
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Discount rate for a given total quantity of one product.
 */
function tp_bundle_discount_rate( int $qty ): float {
	if ( $qty >= 10 ) {
		return 0.20;
	}
	if ( $qty >= 5 ) {
		return 0.10;
	}
	if ( $qty >= 3 ) {
		return 0.05;
	}
	return 0.0;
}

/**
 * Apply bundle pricing to cart line items.
 *
 * @param WC_Cart $cart Cart instance.
 */
function tp_apply_bundle_pricing( $cart ): void {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}
	if ( ! $cart instanceof WC_Cart || empty( $cart->get_cart() ) ) {
		return;
	}

	// Total quantity per parent product (variations grouped under their parent).
	$qty_by_product = array();
	foreach ( $cart->get_cart() as $item ) {
		$pid                    = (int) $item['product_id'];
		$qty_by_product[ $pid ] = ( $qty_by_product[ $pid ] ?? 0 ) + (int) $item['quantity'];
	}

	foreach ( $cart->get_cart() as $item ) {
		$pid  = (int) $item['product_id'];
		$rate = tp_bundle_discount_rate( (int) ( $qty_by_product[ $pid ] ?? 0 ) );

		if ( $rate <= 0 ) {
			continue;
		}

		/** @var WC_Product $product */
		$product = $item['data'];

		// Base on the stored catalog price (_price) so repeated recalcs never compound.
		$base = (float) get_post_meta( $product->get_id(), '_price', true );
		if ( $base <= 0 ) {
			$base = (float) $product->get_price();
		}
		if ( $base <= 0 ) {
			continue;
		}

		$product->set_price( round( $base * ( 1 - $rate ), wc_get_price_decimals() ) );
	}
}
add_action( 'woocommerce_before_calculate_totals', 'tp_apply_bundle_pricing', 20 );

/**
 * Show a notice on the cart page summarising any active bundle discounts.
 */
function tp_bundle_cart_notice(): void {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	$qty_by_product = array();
	$names          = array();
	foreach ( WC()->cart->get_cart() as $item ) {
		$pid                    = (int) $item['product_id'];
		$qty_by_product[ $pid ] = ( $qty_by_product[ $pid ] ?? 0 ) + (int) $item['quantity'];
		$names[ $pid ]          = $item['data'] ? $item['data']->get_title() : '';
	}

	foreach ( $qty_by_product as $pid => $qty ) {
		$rate = tp_bundle_discount_rate( (int) $qty );
		if ( $rate <= 0 ) {
			continue;
		}
		wc_add_notice(
			sprintf(
				/* translators: 1: discount %, 2: product name, 3: quantity. */
				__( 'Bundle discount applied: %1$d%% off %2$s (%3$d in cart).', 'truepharm' ),
				(int) round( $rate * 100 ),
				esc_html( $names[ $pid ] ),
				(int) $qty
			),
			'notice'
		);
	}
}
add_action( 'woocommerce_before_cart', 'tp_bundle_cart_notice' );
