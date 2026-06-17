<?php
/**
 * My Account — Dashboard Overview.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tp_customer_id = get_current_user_id();

// Active orders (in progress).
$tp_active_orders = wc_get_orders(
	array(
		'customer' => $tp_customer_id,
		'status'   => array( 'pending', 'processing', 'on-hold' ),
		'limit'    => -1,
		'return'   => 'ids',
	)
);
$tp_active_count = is_array( $tp_active_orders ) ? count( $tp_active_orders ) : 0;

// Total formulations = item quantity across completed orders.
$tp_completed = wc_get_orders(
	array(
		'customer' => $tp_customer_id,
		'status'   => array( 'completed' ),
		'limit'    => -1,
	)
);
$tp_total_items = 0;
foreach ( $tp_completed as $tp_order ) {
	$tp_total_items += $tp_order->get_item_count();
}

$tp_balance = tp_rewards_get_balance( $tp_customer_id );

// Recent orders.
$tp_recent = wc_get_orders(
	array(
		'customer' => $tp_customer_id,
		'limit'    => 3,
		'orderby'  => 'date',
		'order'    => 'DESC',
	)
);
?>

<h2><?php esc_html_e( 'Account Overview', 'truepharm' ); ?></h2>

<div class="stat-grid">
	<div class="stat-card">
		<h4><?php esc_html_e( 'Active Orders', 'truepharm' ); ?></h4>
		<div class="val"><?php echo esc_html( number_format_i18n( $tp_active_count ) ); ?></div>
	</div>
	<div class="stat-card">
		<h4><?php esc_html_e( 'Total Formulations', 'truepharm' ); ?></h4>
		<div class="val"><?php echo esc_html( number_format_i18n( $tp_total_items ) ); ?></div>
	</div>
	<div class="stat-card highlight">
		<h4><?php esc_html_e( 'Available Rewards', 'truepharm' ); ?></h4>
		<div class="val"><?php echo esc_html( number_format_i18n( $tp_balance ) ); ?> <?php esc_html_e( 'Pts', 'truepharm' ); ?></div>
	</div>
</div>

<h3 style="color:var(--navy); font-size:1.3rem; margin-bottom:16px;"><?php esc_html_e( 'Recent Laboratory Orders', 'truepharm' ); ?></h3>

<div class="table-responsive">
	<table class="order-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Order ID', 'truepharm' ); ?></th>
				<th><?php esc_html_e( 'Date', 'truepharm' ); ?></th>
				<th><?php esc_html_e( 'Status', 'truepharm' ); ?></th>
				<th><?php esc_html_e( 'Total', 'truepharm' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'truepharm' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! empty( $tp_recent ) ) : ?>
				<?php foreach ( $tp_recent as $tp_order ) : ?>
					<tr>
						<td><strong>#<?php echo esc_html( $tp_order->get_order_number() ); ?></strong></td>
						<td><?php echo esc_html( wc_format_datetime( $tp_order->get_date_created() ) ); ?></td>
						<td><?php echo truepharm_order_status_tag( $tp_order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?></td>
						<td><?php echo wp_kses_post( $tp_order->get_formatted_order_total() ); ?></td>
						<td><a href="<?php echo esc_url( $tp_order->get_view_order_url() ); ?>" class="btn-view"><?php esc_html_e( 'View details', 'truepharm' ); ?></a></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr><td colspan="5" style="text-align:center; color:var(--slate-soft);"><?php esc_html_e( 'No orders yet — your laboratory orders will appear here.', 'truepharm' ); ?></td></tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>

<p style="font-size:0.85rem; color:var(--slate-soft); margin-top:40px; text-align:center;">
	<?php
	printf(
		/* translators: %s: COA Library link. */
		esc_html__( 'Need to verify your recent batch? Visit the %s and input the Lot Number found on your vial.', 'truepharm' ),
		'<a href="' . esc_url( get_post_type_archive_link( 'coa_library' ) ?: home_url( '/coa-library/' ) ) . '" style="color:var(--navy); font-weight:600;">' . esc_html__( 'COA Library', 'truepharm' ) . '</a>'
	);
	?>
</p>
