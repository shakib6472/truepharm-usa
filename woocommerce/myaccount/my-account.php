<?php
/**
 * My Account page wrapper — Researcher Dashboard layout.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tp_user      = wp_get_current_user();
$tp_first     = $tp_user->first_name ? $tp_user->first_name : $tp_user->display_name;
$tp_logout    = wc_logout_url( wc_get_page_permalink( 'shop' ) );
?>

<header class="page-header">
	<div class="wrap">
		<h1><?php esc_html_e( 'Researcher Dashboard', 'truepharm' ); ?></h1>
		<p>
			<?php
			printf(
				/* translators: %s: customer display name. */
				esc_html__( 'Welcome back, %s', 'truepharm' ),
				'<strong>' . esc_html( $tp_user->display_name ) . '</strong>'
			);
			?>
			(<?php printf( /* translators: %s: first name. */ esc_html__( 'Not %s?', 'truepharm' ), esc_html( $tp_first ) ); ?>
			<a href="<?php echo esc_url( $tp_logout ); ?>" style="color:var(--warm); font-weight:600;"><?php esc_html_e( 'Log out', 'truepharm' ); ?></a>)
		</p>
	</div>
</header>

<section class="dashboard-section wrap">
	<div class="dashboard-layout">
		<div class="dash-nav-wrap">
			<?php
			/**
			 * Account navigation (themed .dash-nav via navigation.php override).
			 *
			 * @hooked woocommerce_account_navigation - 10
			 */
			do_action( 'woocommerce_account_navigation' );
			?>
		</div>

		<div class="dash-content">
			<?php
			/**
			 * Account content.
			 *
			 * @hooked woocommerce_account_content - 10
			 */
			do_action( 'woocommerce_account_content' );
			?>
		</div>
	</div>
</section>
