<?php
/**
 * My Account navigation — themed .dash-nav sidebar with icons.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inline SVG for a given account endpoint.
 */
if ( ! function_exists( 'tp_account_nav_icon' ) ) :
function tp_account_nav_icon( string $endpoint ): string {
	$icons = array(
		'dashboard'        => '<rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect>',
		'orders'           => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>',
		'rewards'          => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>',
		'edit-address'     => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle>',
		'edit-account'     => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
		'downloads'        => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line>',
		'payment-methods'  => '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line>',
		'customer-logout'  => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>',
	);
	$paths = $icons[ $endpoint ] ?? '<circle cx="12" cy="12" r="9"></circle>';
	return '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">' . $paths . '</svg>';
}
endif;
?>
<ul class="dash-nav">
	<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
		<?php
		$classes   = wc_get_account_menu_item_classes( $endpoint );
		$is_active = false !== strpos( $classes, 'is-active' );
		$extra     = 'customer-logout' === $endpoint ? ' logout-link' : '';
		?>
		<li>
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="<?php echo esc_attr( trim( ( $is_active ? 'active' : '' ) . $extra ) ); ?>">
				<?php echo tp_account_nav_icon( $endpoint ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG. ?>
				<?php echo esc_html( $label ); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
