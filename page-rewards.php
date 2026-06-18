<?php
/**
 * Template: Rewards Program (marketing).
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$tp_account_url = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'dashboard' ) : wp_login_url();
$tp_rewards_url = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'rewards' ) : $tp_account_url;

$tp_signup     = tp_rewards_signup_bonus();
$tp_per_dollar = tp_rewards_points_per_dollar();
$tp_redeem_pts = tp_rewards_redeem_points();
$tp_redeem_val = tp_rewards_redeem_value_display();
$tp_ref_disc   = (int) TP_REWARDS_REFERRAL_DISCOUNT;
$tp_ref_pts    = (int) TP_REWARDS_REFERRAL_POINTS;
$tp_ref_val    = tp_rewards_points_to_value( $tp_ref_pts );
?>

<header class="page-header">
	<div class="wrap reveal">
		<h1><?php esc_html_e( 'TruePharm Rewards', 'truepharm' ); ?></h1>
		<p><?php esc_html_e( 'Earn points on every research batch. Redeem for exclusive discounts and institutional perks.', 'truepharm' ); ?></p>
		<a href="<?php echo esc_url( $tp_account_url ); ?>" class="btn-rosegold"><?php esc_html_e( 'Join Now / Log In', 'truepharm' ); ?></a>
	</div>
</header>

<!-- How it Works -->
<section class="how-it-works wrap reveal">
	<div class="hiw-grid">
		<div class="hiw-step">
			<div class="hiw-icon"><svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></div>
			<h3><?php esc_html_e( '1. Create an Account', 'truepharm' ); ?></h3>
			<p>
			<?php
			/* translators: %d: signup points. */
			printf( esc_html__( 'Sign up for free. You will instantly earn %d points just for joining our network.', 'truepharm' ), (int) $tp_signup );
			?>
			</p>
		</div>
		<div class="hiw-step">
			<div class="hiw-icon"><svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
			<h3><?php esc_html_e( '2. Earn Points', 'truepharm' ); ?></h3>
			<p>
			<?php
			/* translators: %d: points per dollar. */
			printf( esc_html( _n( 'Earn %d point for every $1 spent on formulations, plus bonus points for reviews and referrals.', 'Earn %d points for every $1 spent on formulations, plus bonus points for reviews and referrals.', $tp_per_dollar, 'truepharm' ) ), (int) $tp_per_dollar );
			?>
			</p>
		</div>
		<div class="hiw-step">
			<div class="hiw-icon"><svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg></div>
			<h3><?php esc_html_e( '3. Redeem for Discounts', 'truepharm' ); ?></h3>
			<p>
			<?php
			/* translators: 1: points, 2: dollar value. */
			printf( esc_html__( 'Every %1$d points equals %2$s off your next order. Apply them directly at checkout.', 'truepharm' ), (int) $tp_redeem_pts, esc_html( $tp_redeem_val ) );
			?>
			</p>
		</div>
	</div>
</section>

<!-- Ways to Earn -->
<section class="ways-to-earn reveal">
	<div class="wrap">
		<div class="wte-header">
			<span class="eyebrow"><?php esc_html_e( 'Maximize Your Value', 'truepharm' ); ?></span>
			<h2><?php esc_html_e( 'Ways to Earn Points', 'truepharm' ); ?></h2>
		</div>
		<div class="wte-grid">
			<div class="wte-card">
				<div class="wte-pts"><?php printf( /* translators: %d: pts per dollar. */ esc_html__( '%d Pt / $1', 'truepharm' ), (int) $tp_per_dollar ); ?></div>
				<h4><?php esc_html_e( 'Place an Order', 'truepharm' ); ?></h4>
				<p><?php esc_html_e( 'Earn points automatically on every dollar spent.', 'truepharm' ); ?></p>
			</div>
			<div class="wte-card">
				<div class="wte-pts"><?php printf( /* translators: %d: signup pts. */ esc_html__( '%d Pts', 'truepharm' ), (int) $tp_signup ); ?></div>
				<h4><?php esc_html_e( 'Sign Up', 'truepharm' ); ?></h4>
				<p><?php esc_html_e( 'Create your free TruePharm USA laboratory account.', 'truepharm' ); ?></p>
			</div>
			<div class="wte-card">
				<div class="wte-pts"><?php printf( /* translators: %d: review pts. */ esc_html__( '%d Pts', 'truepharm' ), (int) TP_REWARDS_REVIEW_POINTS ); ?></div>
				<h4><?php esc_html_e( 'Leave a Review', 'truepharm' ); ?></h4>
				<p><?php esc_html_e( 'Share your empirical feedback after your order arrives.', 'truepharm' ); ?></p>
			</div>
			<div class="wte-card">
				<div class="wte-pts"><?php printf( /* translators: %d: birthday pts. */ esc_html__( '%d Pts', 'truepharm' ), (int) TP_REWARDS_BIRTHDAY_POINTS ); ?></div>
				<h4><?php esc_html_e( 'Happy Birthday', 'truepharm' ); ?></h4>
				<p><?php esc_html_e( 'Add your birthday to your profile for an annual bonus.', 'truepharm' ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- Refer a Colleague -->
<section class="referral-section reveal">
	<div class="wrap">
		<div class="referral-box">
			<span class="eyebrow"><?php esc_html_e( 'Institutional Growth', 'truepharm' ); ?></span>
			<h2><?php esc_html_e( 'Refer a Colleague', 'truepharm' ); ?></h2>
			<p><?php esc_html_e( 'Give your fellow researchers a discount on their first clinical batch, and earn a massive reward for your own lab when they complete their purchase.', 'truepharm' ); ?></p>

			<div class="referral-highlight">
				<span><?php printf( /* translators: %d: discount. */ esc_html__( 'They Get $%d Off', 'truepharm' ), $tp_ref_disc ); ?></span>
				&rarr;
				<span><?php printf( /* translators: 1: points, 2: value. */ esc_html__( 'You Get %1$d Points (%2$s)', 'truepharm' ), $tp_ref_pts, esc_html( wp_strip_all_tags( wc_price( $tp_ref_val, array( 'decimals' => 0 ) ) ) ) ); ?></span>
			</div>

			<?php if ( is_user_logged_in() ) : ?>
				<?php
				$tp_code = tp_rewards_get_referral_code( get_current_user_id() );
				$tp_link = add_query_arg( 'ref', rawurlencode( $tp_code ), home_url( '/' ) );
				?>
				<div class="referral-code" style="display:inline-block; margin-bottom:16px;"><?php echo esc_html( $tp_link ); ?></div>
				<div>
					<button type="button" class="btn-cart tp-copy-btn" data-code="<?php echo esc_attr( $tp_link ); ?>"><?php esc_html_e( 'Copy Referral Link', 'truepharm' ); ?></button>
				</div>
			<?php else : ?>
				<a href="<?php echo esc_url( $tp_rewards_url ); ?>" class="btn-cart"><?php esc_html_e( 'Get Your Referral Link', 'truepharm' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
get_footer();
