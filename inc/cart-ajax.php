<?php
/**
 * Cart — shared render helpers + AJAX (quantity, remove, coupon).
 *
 * WooCommerce 10.8 has no `wc-ajax=update_cart` endpoint, so quantity changes,
 * removals, and coupons are routed through one theme handler that re-renders
 * the cart rows + summary from the same helpers the template uses.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Make sure the cart is available (it can be null during admin-ajax).
 */
function tp_cart_ready(): bool {
	if ( ! function_exists( 'WC' ) ) {
		return false;
	}
	if ( null === WC()->cart && function_exists( 'wc_load_cart' ) ) {
		wc_load_cart();
	}
	return null !== WC()->cart;
}

/**
 * Render the cart line-item rows (shared by cart.php and the AJAX handler).
 */
function tp_cart_item_rows(): string {
	if ( ! tp_cart_ready() ) {
		return '';
	}

	ob_start();
	foreach ( WC()->cart->get_cart() as $key => $cart_item ) {
		$product = $cart_item['data'];
		if ( ! $product instanceof WC_Product || ! $product->exists() || $cart_item['quantity'] <= 0 ) {
			continue;
		}
		$permalink = $product->is_visible() ? $product->get_permalink( $cart_item ) : '';
		$name      = $product->get_name();
		$meta      = wc_get_formatted_cart_item_data( $cart_item );
		$remove    = wc_get_cart_remove_url( $key );
		?>
		<tr class="tp-cart-row" data-cart_key="<?php echo esc_attr( $key ); ?>">
			<td class="col-remove">
				<a href="<?php echo esc_url( $remove ); ?>" class="tp-remove" data-cart_key="<?php echo esc_attr( $key ); ?>" aria-label="<?php esc_attr_e( 'Remove item', 'truepharm' ); ?>">&times;</a>
			</td>
			<td class="col-thumb">
				<?php if ( $permalink ) : ?><a href="<?php echo esc_url( $permalink ); ?>"><?php endif; ?>
					<?php echo $product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( $permalink ) : ?></a><?php endif; ?>
			</td>
			<td class="col-name" data-title="<?php esc_attr_e( 'Product', 'truepharm' ); ?>">
				<?php if ( $permalink ) : ?>
					<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $name ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $name ); ?>
				<?php endif; ?>
				<?php if ( $meta ) : ?><div class="item-meta"><?php echo wp_kses_post( $meta ); ?></div><?php endif; ?>
			</td>
			<td class="col-price" data-title="<?php esc_attr_e( 'Price', 'truepharm' ); ?>">
				<?php echo wp_kses_post( WC()->cart->get_product_price( $product ) ); ?>
			</td>
			<td class="col-qty" data-title="<?php esc_attr_e( 'Quantity', 'truepharm' ); ?>">
				<div class="qty-input">
					<button type="button" class="qty-btn" data-step="down" aria-label="<?php esc_attr_e( 'Decrease quantity', 'truepharm' ); ?>">&minus;</button>
					<input type="number" class="tp-qty" name="cart[<?php echo esc_attr( $key ); ?>][qty]" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" min="0" max="<?php echo esc_attr( $product->get_max_purchase_quantity() ); ?>" data-cart_key="<?php echo esc_attr( $key ); ?>" aria-label="<?php esc_attr_e( 'Quantity', 'truepharm' ); ?>">
					<button type="button" class="qty-btn" data-step="up" aria-label="<?php esc_attr_e( 'Increase quantity', 'truepharm' ); ?>">+</button>
				</div>
			</td>
			<td class="col-total" data-title="<?php esc_attr_e( 'Total', 'truepharm' ); ?>">
				<?php echo wp_kses_post( WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] ) ); ?>
			</td>
		</tr>
		<?php
	}
	return ob_get_clean();
}

/**
 * Whether any single product hits a bundle-discount tier (3/5/10+).
 */
function tp_cart_has_bundle(): bool {
	if ( ! tp_cart_ready() || ! function_exists( 'tp_bundle_discount_rate' ) ) {
		return false;
	}
	$qty = array();
	foreach ( WC()->cart->get_cart() as $item ) {
		$pid         = (int) $item['product_id'];
		$qty[ $pid ] = ( $qty[ $pid ] ?? 0 ) + (int) $item['quantity'];
	}
	foreach ( $qty as $q ) {
		if ( tp_bundle_discount_rate( (int) $q ) > 0 ) {
			return true;
		}
	}
	return false;
}

/**
 * Render the order-summary inner block (shared by cart.php and AJAX).
 */
function tp_cart_summary(): string {
	if ( ! tp_cart_ready() ) {
		return '';
	}
	$cart = WC()->cart;
	ob_start();
	?>
	<div class="summary-row">
		<span><?php esc_html_e( 'Subtotal', 'truepharm' ); ?></span>
		<span><?php echo wp_kses_post( $cart->get_cart_subtotal() ); ?></span>
	</div>

	<?php foreach ( $cart->get_coupons() as $code => $coupon ) : ?>
		<div class="summary-row summary-discount">
			<span><?php echo esc_html( wc_cart_totals_coupon_label( $coupon, false ) ); ?></span>
			<span>&minus;<?php echo wp_kses_post( wc_price( $cart->get_coupon_discount_amount( $code, $cart->display_cart_ex_tax ) ) ); ?></span>
		</div>
	<?php endforeach; ?>

	<div class="summary-row">
		<span><?php esc_html_e( 'Shipping', 'truepharm' ); ?></span>
		<span><?php esc_html_e( 'Calculated at checkout', 'truepharm' ); ?></span>
	</div>

	<div class="summary-row summary-total">
		<span><?php esc_html_e( 'Total', 'truepharm' ); ?></span>
		<span><?php echo wp_kses_post( $cart->get_total() ); ?></span>
	</div>

	<?php if ( tp_cart_has_bundle() ) : ?>
		<div class="bundle-info">
			<?php esc_html_e( 'Bundle discount applied — quantity savings are reflected in your subtotal.', 'truepharm' ); ?>
		</div>
	<?php endif; ?>

	<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn-rosegold cart-checkout-btn"><?php esc_html_e( 'Proceed to Checkout', 'truepharm' ); ?></a>

	<div class="cart-payments">
		<span><?php esc_html_e( 'We accept', 'truepharm' ); ?></span>
		<span class="pay-pill">VISA</span><span class="pay-pill">MC</span><span class="pay-pill">AMEX</span><span class="pay-pill">&#63743; Pay</span><span class="pay-pill">G Pay</span>
	</div>
	<div class="cart-secure">
		<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
		<?php esc_html_e( '256-bit SSL secured checkout', 'truepharm' ); ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * AJAX: quantity update / remove / coupon (single endpoint).
 */
function tp_cart_update(): void {
	check_ajax_referer( 'tp_ajax', 'nonce' );
	if ( ! tp_cart_ready() ) {
		wp_send_json_error( array( 'message' => __( 'Cart unavailable.', 'truepharm' ) ), 500 );
	}

	$action = isset( $_POST['do'] ) ? sanitize_text_field( wp_unslash( $_POST['do'] ) ) : '';
	$key    = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';

	switch ( $action ) {
		case 'qty':
			$qty = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 1;
			if ( $key && WC()->cart->find_product_in_cart( $key ) === $key || isset( WC()->cart->get_cart()[ $key ] ) ) {
				WC()->cart->set_quantity( $key, max( 0, (int) $qty ), true );
			}
			break;

		case 'remove':
			if ( $key ) {
				WC()->cart->remove_cart_item( $key );
			}
			break;

		case 'coupon':
			$code = isset( $_POST['coupon'] ) ? wc_format_coupon_code( wp_unslash( $_POST['coupon'] ) ) : '';
			if ( '' === $code ) {
				wc_add_notice( __( 'Please enter a coupon code.', 'truepharm' ), 'error' );
			} else {
				WC()->cart->apply_coupon( $code );
			}
			break;
	}

	WC()->cart->calculate_totals();

	wp_send_json_success(
		array(
			'items'   => tp_cart_item_rows(),
			'summary' => tp_cart_summary(),
			'count'   => WC()->cart->get_cart_contents_count(),
			'empty'   => WC()->cart->is_empty(),
			'notices' => function_exists( 'wc_print_notices' ) ? wc_print_notices( true ) : '',
		)
	);
}
add_action( 'wp_ajax_tp_cart_update', 'tp_cart_update' );
add_action( 'wp_ajax_nopriv_tp_cart_update', 'tp_cart_update' );
