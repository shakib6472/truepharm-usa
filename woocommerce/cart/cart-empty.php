<?php
/**
 * Empty cart — TruePharm styled state.
 *
 * @package TruePharm_USA
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_cart_is_empty' );

$tp_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>

<header class="page-header">
	<div class="wrap">
		<h1><?php esc_html_e( 'Your Research Cart', 'truepharm' ); ?></h1>
		<p><?php esc_html_e( 'Review your selected compounds', 'truepharm' ); ?></p>
	</div>
</header>

<section class="wrap">
	<div class="cart-empty-state">
		<svg width="56" height="56" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
		<h2><?php esc_html_e( 'Your cart is empty', 'truepharm' ); ?></h2>
		<p><?php esc_html_e( 'Browse our catalog of verified, clinical-grade research compounds to get started.', 'truepharm' ); ?></p>
		<a href="<?php echo esc_url( $tp_shop_url ); ?>" class="btn btn-cart"><?php esc_html_e( 'Browse Research Catalog', 'truepharm' ); ?></a>
	</div>
</section>
