<?php
/**
 * My Account — Order History.
 *
 * @package TruePharm_USA
 *
 * @var bool      $has_orders
 * @var stdClass  $customer_orders
 * @var int       $current_page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tp_orders     = ( isset( $customer_orders ) && ! empty( $customer_orders->orders ) ) ? $customer_orders->orders : array();
$tp_max_pages  = ( isset( $customer_orders ) && ! empty( $customer_orders->max_num_pages ) ) ? (int) $customer_orders->max_num_pages : 1;
$tp_cur_page   = isset( $current_page ) ? (int) $current_page : 1;
$tp_has_orders = isset( $has_orders ) ? (bool) $has_orders : ! empty( $tp_orders );
?>

<h2><?php esc_html_e( 'Order History', 'truepharm' ); ?></h2>

<?php if ( $tp_has_orders ) : ?>

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
				<?php
				foreach ( $tp_orders as $tp_order ) :
					$tp_status   = $tp_order->get_status();
					$tp_view_url = $tp_order->get_view_order_url();
					?>
					<tr>
						<td><strong>#<?php echo esc_html( $tp_order->get_order_number() ); ?></strong></td>
						<td><?php echo esc_html( wc_format_datetime( $tp_order->get_date_created() ) ); ?></td>
						<td><?php echo truepharm_order_status_tag( $tp_order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?></td>
						<td>
							<?php
							printf(
								/* translators: 1: order total, 2: item count. */
								wp_kses_post( __( '%1$s for %2$d item(s)', 'truepharm' ) ),
								wp_kses_post( $tp_order->get_formatted_order_total() ),
								(int) $tp_order->get_item_count()
							);
							?>
						</td>
						<td>
							<a href="<?php echo esc_url( $tp_view_url ); ?>" class="btn-view"><?php esc_html_e( 'View', 'truepharm' ); ?></a>
							<?php if ( in_array( $tp_status, array( 'pending', 'processing', 'on-hold', 'shipped' ), true ) ) : ?>
								<a href="<?php echo esc_url( $tp_view_url ); ?>" class="btn-view"><?php esc_html_e( 'Track', 'truepharm' ); ?></a>
							<?php endif; ?>
							<?php if ( 'completed' === $tp_status ) : ?>
								<a href="<?php echo esc_url( $tp_view_url ); ?>" class="btn-view"><?php esc_html_e( 'Invoice', 'truepharm' ); ?></a>
							<?php endif; ?>
							<?php if ( in_array( $tp_status, array( 'completed', 'cancelled', 'refunded', 'failed' ), true ) ) : ?>
								<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'order_again', $tp_order->get_id(), wc_get_cart_url() ), 'woocommerce-order_again' ) ); ?>" class="btn-view"><?php esc_html_e( 'Order Again', 'truepharm' ); ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<?php if ( $tp_max_pages > 1 ) : ?>
		<div class="woocommerce-pagination tp-account-pagination">
			<?php if ( 1 !== $tp_cur_page ) : ?>
				<a class="btn-view" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $tp_cur_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'truepharm' ); ?></a>
			<?php endif; ?>
			<span class="tp-page-of">
				<?php
				printf(
					/* translators: 1: current page, 2: total pages. */
					esc_html__( 'Page %1$d of %2$d', 'truepharm' ),
					(int) $tp_cur_page,
					(int) $tp_max_pages
				);
				?>
			</span>
			<?php if ( $tp_cur_page < $tp_max_pages ) : ?>
				<a class="btn-view" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $tp_cur_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'truepharm' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>
	<p style="color:var(--slate-soft);"><?php esc_html_e( 'You have no orders yet. Once you place a research requisition, it will appear here.', 'truepharm' ); ?></p>
	<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn-view"><?php esc_html_e( 'Browse Formulations', 'truepharm' ); ?></a>
<?php endif; ?>
