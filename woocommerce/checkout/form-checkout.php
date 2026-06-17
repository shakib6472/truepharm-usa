<?php
/**
 * Checkout Form — Secure Checkout.
 *
 * Restyled to the brief while preserving every native WooCommerce checkout
 * hook (billing, shipping, order review, payment). The Turnstile widget is
 * injected above the Place Order button via woocommerce_review_order_before_submit.
 *
 * @package TruePharm_USA
 * @var WC_Checkout $checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<section class="checkout-section wrap">

	<div class="checkout-header">
		<h1><?php esc_html_e( 'Secure Checkout', 'truepharm' ); ?></h1>
		<p><?php esc_html_e( 'Complete your institutional details to finalize your research requisition.', 'truepharm' ); ?></p>
	</div>

	<div class="compliance-check" style="max-width:none; margin-bottom:30px;">
		<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
		<label style="cursor:default;"><strong><?php esc_html_e( 'Research Compliance:', 'truepharm' ); ?></strong> <?php esc_html_e( 'All items in your cart are intended strictly for in-vitro laboratory research and are NOT for human or veterinary consumption. By placing this order you confirm you are a qualified researcher aged 21+.', 'truepharm' ); ?></label>
	</div>

	<form name="checkout" method="post" class="checkout woocommerce-checkout checkout-grid" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

		<div class="checkout-left">
			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<div class="col2-set" id="customer_details">
					<div class="col-1">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
					</div>
					<div class="col-2">
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
					</div>
				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>
		</div>

		<div class="order-summary-wrap">
			<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

			<div class="order-summary">
				<h2 id="order_review_heading"><?php esc_html_e( 'Order Summary', 'truepharm' ); ?></h2>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

				<div class="ssl-notice">
					<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
					<?php esc_html_e( 'Payments are securely encrypted.', 'truepharm' ); ?>
				</div>
			</div>
		</div>

	</form>

</section>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
