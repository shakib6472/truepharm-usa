<?php
/**
 * My Account — Laboratory Addresses.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tp_customer_id = get_current_user_id();
$tp_customer    = new WC_Customer( $tp_customer_id );

$tp_billing  = wc_get_account_formatted_address( 'billing', $tp_customer_id );
$tp_shipping = wc_get_account_formatted_address( 'shipping', $tp_customer_id );

$tp_billing_edit  = wc_get_endpoint_url( 'edit-address', 'billing' );
$tp_shipping_edit = wc_get_endpoint_url( 'edit-address', 'shipping' );

$tp_billing_email = $tp_customer->get_billing_email();
$tp_billing_phone = $tp_customer->get_billing_phone();
?>

<h2><?php esc_html_e( 'Laboratory Addresses', 'truepharm' ); ?></h2>
<p><?php esc_html_e( 'Manage your billing information and institutional shipping destinations.', 'truepharm' ); ?></p>

<div class="logistics-notice">
	<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
	<p><strong><?php esc_html_e( 'Logistics Notice:', 'truepharm' ); ?></strong> <?php esc_html_e( 'Due to cold-chain preservation protocols, please ensure your receiving facility or laboratory is equipped to intake packages promptly upon delivery.', 'truepharm' ); ?></p>
</div>

<div class="address-grid">

	<!-- Billing Address -->
	<div class="address-card">
		<div class="address-header">
			<h3><?php esc_html_e( 'Billing Address', 'truepharm' ); ?></h3>
			<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
		</div>
		<div class="address-details">
			<?php if ( $tp_billing ) : ?>
				<?php echo wp_kses_post( $tp_billing ); ?>
				<?php if ( $tp_billing_email || $tp_billing_phone ) : ?>
					<br><br>
					<?php echo $tp_billing_email ? esc_html( $tp_billing_email ) . '<br>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
					<?php echo $tp_billing_phone ? esc_html( $tp_billing_phone ) : ''; ?>
				<?php endif; ?>
			<?php else : ?>
				<em><?php esc_html_e( 'No address saved yet.', 'truepharm' ); ?></em>
			<?php endif; ?>
		</div>
		<a href="<?php echo esc_url( $tp_billing_edit ); ?>" class="btn-edit">
			<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="16 3 21 8 8 21 3 21 3 16 16 3"></polygon></svg>
			<?php echo $tp_billing ? esc_html__( 'Edit Billing', 'truepharm' ) : esc_html__( 'Add Billing Address', 'truepharm' ); ?>
		</a>
	</div>

	<!-- Shipping Address -->
	<div class="address-card">
		<div class="address-header">
			<h3><?php esc_html_e( 'Shipping Address', 'truepharm' ); ?></h3>
			<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
		</div>
		<div class="address-details">
			<?php if ( $tp_shipping ) : ?>
				<?php echo wp_kses_post( $tp_shipping ); ?>
			<?php else : ?>
				<em><?php esc_html_e( 'No address saved yet.', 'truepharm' ); ?></em>
			<?php endif; ?>
		</div>
		<a href="<?php echo esc_url( $tp_shipping_edit ); ?>" class="btn-edit">
			<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="16 3 21 8 8 21 3 21 3 16 16 3"></polygon></svg>
			<?php echo $tp_shipping ? esc_html__( 'Edit Shipping', 'truepharm' ) : esc_html__( 'Add Shipping Address', 'truepharm' ); ?>
		</a>
	</div>

</div>
