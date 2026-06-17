<?php
/**
 * Site header: compliance top bar, sticky nav with utility icons,
 * and the slide-in navigation panel.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$truepharm_account_url = function_exists( 'wc_get_account_endpoint_url' )
	? wc_get_account_endpoint_url( 'dashboard' )
	: wp_login_url();

$truepharm_cart_url = function_exists( 'wc_get_cart_url' )
	? wc_get_cart_url()
	: home_url( '/cart/' );

$truepharm_shop_url = function_exists( 'wc_get_page_permalink' )
	? wc_get_page_permalink( 'shop' )
	: home_url( '/shop/' );

// Compliance top-bar copy — overridable via the Customizer (registered in a later phase).
$truepharm_topbar_left   = get_theme_mod( 'truepharm_topbar_left', __( 'Secure Checkout', 'truepharm' ) );
$truepharm_topbar_center = get_theme_mod( 'truepharm_topbar_center', __( 'Authorized Clinical Research Formulas', 'truepharm' ) );
$truepharm_topbar_right  = get_theme_mod( 'truepharm_topbar_right', __( 'Free Priority Logistics on Orders $200+', 'truepharm' ) );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="color-scheme" content="light only">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#primary"><?php esc_html_e( 'Skip to content', 'truepharm' ); ?></a>

<?php
/**
 * Entrance gate (age / research-compliance modal).
 * The markup + cookie logic is wired in inc/entrance-gate.php (later phase).
 */
do_action( 'truepharm_entrance_gate' );
?>

<!-- Compliance Top Bar -->
<div class="top-bar">
	<?php if ( $truepharm_topbar_left ) : ?>
		<span><?php echo esc_html( $truepharm_topbar_left ); ?></span>
	<?php endif; ?>
	<?php echo $truepharm_topbar_left && $truepharm_topbar_center ? ' | ' : ''; ?>
	<?php echo esc_html( $truepharm_topbar_center ); ?>
	<?php echo $truepharm_topbar_center && $truepharm_topbar_right ? ' | ' : ''; ?>
	<?php if ( $truepharm_topbar_right ) : ?>
		<span><?php echo esc_html( $truepharm_topbar_right ); ?></span>
	<?php endif; ?>
</div>

<!-- Sticky Navigation -->
<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'truepharm' ); ?>">
	<div class="wrap nav-inner">

		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a class="logo-text" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php
				$truepharm_name = get_bloginfo( 'name' );
				$truepharm_parts = explode( ' ', $truepharm_name, 2 );
				echo esc_html( $truepharm_parts[0] );
				if ( isset( $truepharm_parts[1] ) ) {
					echo ' <span>' . esc_html( $truepharm_parts[1] ) . '</span>';
				}
				?>
			</a>
		<?php endif; ?>

		<div class="nav-right">
			<div class="nav-utils">
				<!-- Search -->
				<a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="nav-icon" aria-label="<?php esc_attr_e( 'Search', 'truepharm' ); ?>">
					<svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
				</a>
				<!-- Account -->
				<a href="<?php echo esc_url( $truepharm_account_url ); ?>" class="nav-icon" aria-label="<?php esc_attr_e( 'My Account', 'truepharm' ); ?>">
					<svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
				</a>
				<!-- Cart -->
				<a href="<?php echo esc_url( $truepharm_cart_url ); ?>" class="nav-icon nav-cart" aria-label="<?php esc_attr_e( 'Cart', 'truepharm' ); ?>">
					<svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 01-8 0"></path></svg>
					<?php echo truepharm_cart_badge_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup escaped in helper. ?>
				</a>
			</div>

			<!-- Hamburger -->
			<button class="hamburger" id="hamburger" aria-label="<?php esc_attr_e( 'Open menu', 'truepharm' ); ?>" aria-controls="menuPanel" aria-expanded="false">
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>
</nav>

<!-- Slide-in Menu -->
<div class="menu-overlay" id="menuOverlay"></div>
<aside class="menu-panel" id="menuPanel" aria-hidden="true">
	<button class="menu-close" id="menuClose" aria-label="<?php esc_attr_e( 'Close menu', 'truepharm' ); ?>">&times;</button>

	<?php
	wp_nav_menu(
		array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'menu-links',
			'menu_id'        => 'primary-menu',
			'fallback_cb'    => 'truepharm_primary_menu_fallback',
			'depth'          => 1,
		)
	);
	?>

	<div class="menu-foot">
		<a href="<?php echo esc_url( $truepharm_shop_url ); ?>" class="btn btn-cart" style="width:100%;text-align:center">
			<?php esc_html_e( 'Shop All Research Compounds', 'truepharm' ); ?>
		</a>
	</div>
</aside>
