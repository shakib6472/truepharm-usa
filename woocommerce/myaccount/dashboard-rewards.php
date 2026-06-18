<?php
/**
 * My Account — TruePharm Rewards tab.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tp_user_id = get_current_user_id();
$tp_balance = tp_rewards_get_balance( $tp_user_id );
$tp_value   = tp_rewards_points_to_value( $tp_balance );
$tp_code    = tp_rewards_get_referral_code( $tp_user_id );
$tp_ledger  = array_reverse( tp_rewards_get_ledger( $tp_user_id ) );
$tp_step    = TP_REWARDS_REDEEM_STEP;
?>

<h2><?php esc_html_e( 'TruePharm Rewards', 'truepharm' ); ?></h2>
<p><?php esc_html_e( 'Manage your points balance, redeem discounts, and access your unique referral link.', 'truepharm' ); ?></p>

<!-- Wallet -->
<div class="rewards-wallet">
	<div class="wallet-balance">
		<h3><?php esc_html_e( 'Current Balance', 'truepharm' ); ?></h3>
		<div class="points" id="tp-balance"><?php echo esc_html( number_format_i18n( $tp_balance ) ); ?> <?php esc_html_e( 'Pts', 'truepharm' ); ?></div>
		<div class="value">
			<?php
			/* translators: %s: dollar value. */
			printf( esc_html__( 'Estimated Value: %s', 'truepharm' ), wp_kses_post( wc_price( $tp_value ) ) );
			?>
		</div>
	</div>
	<div class="wallet-action">
		<div class="redeem-controls">
			<input type="number" id="tp-redeem-points" min="<?php echo esc_attr( (string) $tp_step ); ?>" step="<?php echo esc_attr( (string) $tp_step ); ?>" value="<?php echo esc_attr( (string) $tp_step ); ?>" max="<?php echo esc_attr( (string) $tp_balance ); ?>" aria-label="<?php esc_attr_e( 'Points to redeem', 'truepharm' ); ?>">
			<button type="button" class="btn-rosegold" id="tp-redeem-btn"><?php esc_html_e( 'Redeem for Discount', 'truepharm' ); ?></button>
		</div>
		<p style="font-size:0.8rem; color:var(--slate-soft); margin-top:8px;">
			<?php
			/* translators: 1: points step, 2: dollar value. */
			printf( esc_html__( '%1$d points = %2$s off your next order.', 'truepharm' ), (int) $tp_step, esc_html( tp_rewards_redeem_value_display() ) );
			?>
		</p>
		<div class="redeem-result" id="tp-redeem-result" role="status" aria-live="polite" hidden></div>
	</div>
</div>

<!-- Referral -->
<div class="referral-box">
	<div class="referral-text">
		<h4>
		<?php
		/* translators: %d: referral discount. */
		printf( esc_html__( 'Refer a Colleague, Earn $%d', 'truepharm' ), (int) TP_REWARDS_REFERRAL_DISCOUNT );
		?>
		</h4>
		<p>
		<?php
		/* translators: 1: referral discount, 2: referral points. */
		printf( esc_html__( 'Give $%1$d off their first order. You get %2$d points when they buy.', 'truepharm' ), (int) TP_REWARDS_REFERRAL_DISCOUNT, (int) TP_REWARDS_REFERRAL_POINTS );
		?>
		</p>
	</div>
	<div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
		<div class="referral-code" id="tp-referral-code"><?php echo esc_html( $tp_code ); ?></div>
		<button type="button" class="btn-view tp-copy-btn" id="tp-copy-referral" data-code="<?php echo esc_attr( $tp_code ); ?>"><?php esc_html_e( 'Copy', 'truepharm' ); ?></button>
	</div>
</div>

<!-- Ledger -->
<h3 class="history-header"><?php esc_html_e( 'Points Ledger', 'truepharm' ); ?></h3>
<div style="overflow-x:auto;">
	<table class="history-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Date', 'truepharm' ); ?></th>
				<th><?php esc_html_e( 'Action / Event', 'truepharm' ); ?></th>
				<th><?php esc_html_e( 'Points', 'truepharm' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! empty( $tp_ledger ) ) : ?>
				<?php foreach ( $tp_ledger as $tp_entry ) : ?>
					<?php
					$tp_pts   = (int) ( $tp_entry['points'] ?? 0 );
					$tp_class = $tp_pts >= 0 ? 'pts-earned' : 'pts-spent';
					$tp_date  = ! empty( $tp_entry['date'] ) ? date_i18n( get_option( 'date_format' ), strtotime( $tp_entry['date'] ) ) : '';
					?>
					<tr>
						<td><?php echo esc_html( $tp_date ); ?></td>
						<td><?php echo esc_html( $tp_entry['reason'] ?? '' ); ?></td>
						<td class="<?php echo esc_attr( $tp_class ); ?>"><?php echo esc_html( ( $tp_pts >= 0 ? '+' : '' ) . number_format_i18n( $tp_pts ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr><td colspan="3" style="text-align:center; color:var(--slate-soft);"><?php esc_html_e( 'No points activity yet. Start earning by placing an order or referring a colleague.', 'truepharm' ); ?></td></tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
