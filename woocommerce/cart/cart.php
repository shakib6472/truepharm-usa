<?php
/**
 * Cart page — TruePharm "Your Research Cart".
 *
 * Two-column layout (cart table + order summary). Line rows and the summary
 * are rendered via shared helpers in inc/cart-ajax.php so AJAX updates stay
 * in sync with the initial markup. Native form POST still works without JS.
 *
 * @package TruePharm_USA
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );

$tp_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>

<header class="page-header">
	<div class="wrap">
		<h1><?php esc_html_e( 'Your Research Cart', 'truepharm' ); ?></h1>
		<p><?php esc_html_e( 'Review your selected compounds', 'truepharm' ); ?></p>
	</div>
</header>

<section class="wrap cart-section">
	<div class="cart-layout">

		<!-- Cart table -->
		<div class="cart-main">
			<form class="woocommerce-cart-form tp-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
				<?php do_action( 'woocommerce_before_cart_table' ); ?>

				<table class="tp-cart-table" cellspacing="0">
					<thead>
						<tr>
							<th class="col-remove"><span class="screen-reader-text"><?php esc_html_e( 'Remove', 'truepharm' ); ?></span></th>
							<th class="col-thumb"><span class="screen-reader-text"><?php esc_html_e( 'Image', 'truepharm' ); ?></span></th>
							<th class="col-name"><?php esc_html_e( 'Product', 'truepharm' ); ?></th>
							<th class="col-price"><?php esc_html_e( 'Price', 'truepharm' ); ?></th>
							<th class="col-qty"><?php esc_html_e( 'Quantity', 'truepharm' ); ?></th>
							<th class="col-total"><?php esc_html_e( 'Total', 'truepharm' ); ?></th>
						</tr>
					</thead>
					<tbody id="tp-cart-items">
						<?php do_action( 'woocommerce_before_cart_contents' ); ?>
						<?php echo tp_cart_item_rows(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
						<?php do_action( 'woocommerce_cart_contents' ); ?>
						<?php do_action( 'woocommerce_after_cart_contents' ); ?>
					</tbody>
				</table>

				<div class="cart-actions">
					<a href="<?php echo esc_url( $tp_shop_url ); ?>" class="btn btn-ghost"><?php esc_html_e( 'Continue Shopping', 'truepharm' ); ?></a>

					<?php if ( wc_coupons_enabled() ) : ?>
						<div class="coupon tp-coupon">
							<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'truepharm' ); ?></label>
							<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'truepharm' ); ?>">
							<button type="submit" class="btn btn-cart" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'truepharm' ); ?>"><?php esc_html_e( 'Apply Coupon', 'truepharm' ); ?></button>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php endif; ?>

					<button type="submit" class="btn btn-ghost tp-update-cart" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'truepharm' ); ?>"><?php esc_html_e( 'Update cart', 'truepharm' ); ?></button>
					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</div>

				<?php do_action( 'woocommerce_after_cart_table' ); ?>
			</form>
		</div>

		<!-- Order summary -->
		<aside class="cart-summary">
			<h2><?php esc_html_e( 'Order Summary', 'truepharm' ); ?></h2>
			<div id="tp-cart-summary-inner">
				<?php echo tp_cart_summary(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
			</div>
		</aside>

	</div>
</section>

<?php do_action( 'woocommerce_after_cart' ); ?>
