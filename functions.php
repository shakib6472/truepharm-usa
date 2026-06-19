<?php
/**
 * TruePharm USA — theme bootstrap.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------
 * Constants
 * ------------------------------------------------------------------- */
define( 'TRUEPHARM_VERSION', '1.0.0' );
define( 'TRUEPHARM_DIR', get_template_directory() );
define( 'TRUEPHARM_URI', get_template_directory_uri() );

/* ---------------------------------------------------------------------
 * Theme setup
 * ------------------------------------------------------------------- */
function truepharm_setup(): void {
	// Make the theme available for translation.
	load_theme_textdomain( 'truepharm', TRUEPHARM_DIR . '/languages' );

	// Core supports.
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 400,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array( 'site-title', 'site-description' ),
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	// WooCommerce.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Navigation menus.
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu (slide-in panel)', 'truepharm' ),
			'footer'  => __( 'Footer Legal Links', 'truepharm' ),
		)
	);

	// Editor content width.
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'truepharm_setup' );

/* ---------------------------------------------------------------------
 * Includes — files for later phases are loaded only when present so the
 * theme never fatals while it is being built out phase by phase.
 * ------------------------------------------------------------------- */
$truepharm_includes = array(
	'inc/enqueue.php',          // Phase 1 — scripts & styles.
	'inc/customizer.php',       // Customizer (top bar text, favicon, etc.).
	'inc/cpt-coa.php',          // COA Library custom post type + meta.
	'inc/woo-product-fields.php', // Molecular Class, Form, vial size variants.
	'inc/bundle-pricing.php',   // Quantity-based per-product bundle discounts.
	'inc/rewards.php',          // Custom rewards points engine.
	'inc/newsletter.php',       // Newsletter capture (custom table + AJAX).
	'inc/contact-form.php',     // Contact form (custom table + AJAX + email).
	'inc/cart-ajax.php',        // Cart render helpers + AJAX (qty/remove/coupon).
	'inc/turnstile.php',        // Cloudflare Turnstile placeholder hooks.
	'inc/entrance-gate.php',    // Age / compliance entrance gate.
	'inc/theme-activation.php', // One-time activation setup.
);

foreach ( $truepharm_includes as $truepharm_include ) {
	$truepharm_path = TRUEPHARM_DIR . '/' . $truepharm_include;
	if ( file_exists( $truepharm_path ) ) {
		require_once $truepharm_path;
	}
}
unset( $truepharm_include, $truepharm_path );

/* ---------------------------------------------------------------------
 * Give the custom logo the brief's .logo-img sizing class.
 * ------------------------------------------------------------------- */
function truepharm_custom_logo_class( string $html ): string {
	return str_replace( 'class="custom-logo"', 'class="custom-logo logo-img"', $html );
}
add_filter( 'get_custom_logo', 'truepharm_custom_logo_class' );

/* ---------------------------------------------------------------------
 * Cart count helpers (used by the header badge and the Woo fragment).
 * ------------------------------------------------------------------- */
function truepharm_cart_count(): int {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return (int) WC()->cart->get_cart_contents_count();
	}
	return 0;
}

function truepharm_cart_badge_html(): string {
	$count  = truepharm_cart_count();
	$hidden = $count > 0 ? '' : ' hidden';
	return '<span class="tp-cart-count"' . $hidden . '>' . esc_html( (string) $count ) . '</span>';
}

/**
 * Register the cart badge as a WooCommerce fragment so the standard
 * (jQuery-driven, WC-owned) cart-fragments script swaps it on add-to-cart
 * without our own theme JS needing jQuery.
 */
function truepharm_cart_fragment( array $fragments ): array {
	$fragments['span.tp-cart-count'] = truepharm_cart_badge_html();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'truepharm_cart_fragment' );

/* ---------------------------------------------------------------------
 * WooCommerce — replace default content wrappers with theme markup.
 * ------------------------------------------------------------------- */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

function truepharm_wc_wrapper_start(): void {
	echo '<main id="primary" class="site-main wc-content"><div class="wrap">';
}
add_action( 'woocommerce_before_main_content', 'truepharm_wc_wrapper_start', 10 );

function truepharm_wc_wrapper_end(): void {
	echo '</div></main>';
}
add_action( 'woocommerce_after_main_content', 'truepharm_wc_wrapper_end', 10 );

// The slide-in panel + utility icons replace the default Woo header cart.
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// Render the Turnstile widget directly above the Place Order button.
add_action(
	'woocommerce_review_order_before_submit',
	static function () {
		do_action( 'tp_turnstile_widget' );
	}
);

/* ---------------------------------------------------------------------
 * My Account — custom "Rewards" endpoint.
 * ------------------------------------------------------------------- */
function truepharm_add_rewards_endpoint(): void {
	add_rewrite_endpoint( 'rewards', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'truepharm_add_rewards_endpoint' );

/**
 * Insert "TruePharm Rewards" into the account menu before Logout.
 */
function truepharm_account_menu_items( array $items ): array {
	$new = array();
	foreach ( $items as $key => $label ) {
		if ( 'customer-logout' === $key ) {
			$new['rewards'] = __( 'TruePharm Rewards', 'truepharm' );
		}
		$new[ $key ] = $label;
	}
	if ( ! isset( $new['rewards'] ) ) {
		$new['rewards'] = __( 'TruePharm Rewards', 'truepharm' );
	}
	return $new;
}
add_filter( 'woocommerce_account_menu_items', 'truepharm_account_menu_items' );

/**
 * Render the Rewards endpoint content.
 */
function truepharm_rewards_endpoint_content(): void {
	if ( function_exists( 'wc_get_template' ) ) {
		wc_get_template( 'myaccount/dashboard-rewards.php' );
	}
}
add_action( 'woocommerce_account_rewards_endpoint', 'truepharm_rewards_endpoint_content' );

/**
 * Checkout order review: prepend a product thumbnail to each line item.
 * Scoped to the checkout page so the cart, mini-cart, and emails are untouched.
 */
function truepharm_checkout_review_thumbnail( $name, $cart_item, $cart_item_key ) {
	if ( ! is_checkout() || is_cart() ) {
		return $name;
	}
	$product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
	if ( ! $product instanceof WC_Product ) {
		return $name;
	}
	$thumb = $product->get_image( 'woocommerce_gallery_thumbnail', array( 'class' => 'co-thumb' ) );
	return '<span class="co-item">' . $thumb . '<span class="co-name">' . $name . '</span></span>';
}
add_filter( 'woocommerce_cart_item_name', 'truepharm_checkout_review_thumbnail', 20, 3 );

/**
 * Order History: 10 orders per page.
 */
function truepharm_orders_per_page( array $args ): array {
	$args['limit'] = 10;
	return $args;
}
add_filter( 'woocommerce_my_account_my_orders_query', 'truepharm_orders_per_page' );

/**
 * Map a WooCommerce order status to a display label + status-tag CSS class.
 *
 * @return array{class:string,label:string}
 */
function truepharm_order_status_meta( string $status ): array {
	$map = array(
		'pending'    => array( 'status-processing', __( 'Processing', 'truepharm' ) ),
		'processing' => array( 'status-processing', __( 'Processing', 'truepharm' ) ),
		'on-hold'    => array( 'status-shipped', __( 'Shipped', 'truepharm' ) ),
		'shipped'    => array( 'status-shipped', __( 'Shipped', 'truepharm' ) ),
		'completed'  => array( 'status-completed', __( 'Completed', 'truepharm' ) ),
		'cancelled'  => array( 'status-cancelled', __( 'Cancelled', 'truepharm' ) ),
		'refunded'   => array( 'status-cancelled', __( 'Cancelled', 'truepharm' ) ),
		'failed'     => array( 'status-cancelled', __( 'Cancelled', 'truepharm' ) ),
	);
	$entry = $map[ $status ] ?? array( 'status-processing', ucfirst( $status ) );
	return array(
		'class' => $entry[0],
		'label' => $entry[1],
	);
}

/**
 * Render an order status tag.
 */
function truepharm_order_status_tag( WC_Order $order ): string {
	$meta = truepharm_order_status_meta( $order->get_status() );
	return sprintf(
		'<span class="status-tag %s">%s</span>',
		esc_attr( $meta['class'] ),
		esc_html( $meta['label'] )
	);
}

/**
 * Registration: compliance acknowledgment checkbox (added via hook).
 */
function truepharm_register_compliance_field(): void {
	?>
	<div class="compliance-box">
		<div class="checkbox-group">
			<input type="checkbox" id="tp_compliance" name="tp_compliance" value="1" <?php checked( ! empty( $_POST['tp_compliance'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing ?> required>
			<label for="tp_compliance" style="font-weight:700; color:var(--slate);"><?php esc_html_e( 'I confirm I am a qualified researcher.', 'truepharm' ); ?></label>
		</div>
		<p><?php esc_html_e( 'By registering, I acknowledge that all formulations provided by TruePharm USA are strictly for in-vitro laboratory research and are not for human or veterinary use.', 'truepharm' ); ?></p>
	</div>
	<?php
}
add_action( 'woocommerce_register_form', 'truepharm_register_compliance_field' );

/**
 * Registration: require the compliance checkbox.
 */
function truepharm_validate_compliance( $errors ) {
	if ( empty( $_POST['tp_compliance'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$errors->add( 'tp_compliance_error', __( 'You must confirm you are a qualified researcher to register.', 'truepharm' ) );
	}
	return $errors;
}
add_filter( 'woocommerce_registration_errors', 'truepharm_validate_compliance', 10, 1 );

/**
 * Flush rewrite rules once so the rewards endpoint resolves.
 */
function truepharm_maybe_flush_rewards_endpoint(): void {
	if ( '1' !== get_option( 'tp_rewards_endpoint_flushed' ) ) {
		truepharm_add_rewards_endpoint();
		flush_rewrite_rules();
		update_option( 'tp_rewards_endpoint_flushed', '1' );
	}
}
add_action( 'init', 'truepharm_maybe_flush_rewards_endpoint', 11 );

// Re-flush on theme activation.
add_action(
	'after_switch_theme',
	static function () {
		truepharm_add_rewards_endpoint();
		delete_option( 'tp_rewards_endpoint_flushed' );
		flush_rewrite_rules();
	}
);

/* ---------------------------------------------------------------------
 * Fallback menu for the slide-in panel (used until a Primary menu is
 * assigned in Appearance → Menus). Mirrors the navigation in the brief.
 * ------------------------------------------------------------------- */
function truepharm_primary_menu_fallback(): void {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

	$links = array(
		$shop_url             => __( 'Formulas', 'truepharm' ),
		home_url( '/track-my-order/' ) => __( 'Track My Order', 'truepharm' ),
		home_url( '/why-us/' )         => __( 'Why Us', 'truepharm' ),
		home_url( '/coa-library/' )    => __( 'COA Library', 'truepharm' ),
		home_url( '/faq/' )            => __( 'FAQ', 'truepharm' ),
		home_url( '/rewards-program/' ) => __( 'Rewards Program', 'truepharm' ),
		home_url( '/contact-us/' )     => __( 'Contact Us', 'truepharm' ),
	);

	echo '<ul class="menu-links">';
	foreach ( $links as $url => $label ) {
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( $url ),
			esc_html( $label )
		);
	}
	echo '</ul>';
}
