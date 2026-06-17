<?php
/**
 * Front-end styles & scripts.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve an asset version from its file mtime (cache-busts on every edit),
 * falling back to the theme version.
 */
function truepharm_asset_version( string $relative_path ): string {
	$file = TRUEPHARM_DIR . '/' . ltrim( $relative_path, '/' );
	if ( file_exists( $file ) ) {
		return (string) filemtime( $file );
	}
	return TRUEPHARM_VERSION;
}

/**
 * Preconnect to the Google Fonts hosts for faster font loading.
 *
 * @param array  $urls           URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed for.
 * @return array
 */
function truepharm_resource_hints( array $urls, string $relation_type ): array {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'truepharm_resource_hints', 10, 2 );

/**
 * Enqueue front-end assets.
 */
function truepharm_enqueue_assets(): void {
	// Google Fonts — Montserrat (500–900) + Inter (400–600).
	wp_enqueue_style(
		'truepharm-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@500;600;700;800;900&display=swap',
		array(),
		null // phpcs:ignore -- Google Fonts must not carry a version query string.
	);

	// Main theme stylesheet (contains the full design system).
	wp_enqueue_style(
		'truepharm-style',
		get_stylesheet_uri(),
		array( 'truepharm-fonts' ),
		truepharm_asset_version( 'style.css' )
	);

	// Main theme script — vanilla JS, loaded in the footer, no jQuery.
	wp_enqueue_script(
		'truepharm-main',
		TRUEPHARM_URI . '/assets/js/main.js',
		array(),
		truepharm_asset_version( 'assets/js/main.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	// Data bridge for the front-end.
	wp_localize_script(
		'truepharm-main',
		'TruePharmData',
		array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'restUrl'      => esc_url_raw( rest_url() ),
			'storeCartUrl' => function_exists( 'WC' ) ? esc_url_raw( rest_url( 'wc/store/v1/cart' ) ) : '',
			'nonce'        => wp_create_nonce( 'truepharm_nonce' ),
		)
	);

	// WooCommerce cart fragments — keeps the header badge live site-wide.
	if ( function_exists( 'WC' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}

	// Threaded comments where applicable.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'truepharm_enqueue_assets' );
