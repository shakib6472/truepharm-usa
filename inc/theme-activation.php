<?php
/**
 * One-time theme activation setup.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runs once when the theme is activated.
 */
function truepharm_activate(): void {
	// Register rewrite-affecting objects, then flush.
	if ( function_exists( 'tp_register_coa_cpt' ) ) {
		tp_register_coa_cpt();
	}
	if ( function_exists( 'truepharm_add_rewards_endpoint' ) ) {
		truepharm_add_rewards_endpoint();
	}

	// Custom tables.
	if ( function_exists( 'tp_newsletter_install' ) ) {
		tp_newsletter_install();
	}
	if ( function_exists( 'tp_contact_install' ) ) {
		tp_contact_install();
	}

	// WooCommerce store options for this build.
	if ( class_exists( 'WooCommerce' ) ) {
		update_option( 'woocommerce_coming_soon', 'no' );
		update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
		update_option( 'woocommerce_registration_generate_password', 'no' );
		update_option( 'woocommerce_registration_generate_username', 'yes' );
	}

	// Daily birthday cron.
	if ( ! wp_next_scheduled( 'tp_birthday_check' ) ) {
		wp_schedule_event( time(), 'daily', 'tp_birthday_check' );
	}

	// Seed content + legal pages and assign templates (idempotent).
	truepharm_ensure_pages();

	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'truepharm_activate' );

/**
 * Ensure a page exists (by slug), is published, and uses the given template.
 *
 * @return int Page ID (0 on failure).
 */
function truepharm_ensure_page( string $slug, string $title, string $template, string $content = '', int $parent = 0, int $order = 0 ): int {
	$existing = get_page_by_path( $slug );

	if ( $existing ) {
		$page_id = (int) $existing->ID;
		if ( 'publish' !== $existing->post_status ) {
			wp_update_post( array( 'ID' => $page_id, 'post_status' => 'publish' ) );
		}
	} else {
		$page_id = (int) wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_parent'  => $parent,
				'menu_order'   => $order,
				'post_content' => $content,
			)
		);
	}

	if ( $page_id > 0 ) {
		update_post_meta( $page_id, '_wp_page_template', $template );
	}
	return $page_id;
}

/**
 * Create / repair every theme page and assign its template.
 */
function truepharm_ensure_pages(): void {
	$lorem_simple = '<p><!-- CLIENT TO FILL --> Lorem ipsum dolor sit amet, consectetur adipiscing elit. This placeholder content is editable from the WordPress editor.</p>';

	// Standalone content pages (default page.php template).
	truepharm_ensure_page( 'why-us', 'Why Us', 'page-why-us.php' );
	truepharm_ensure_page( 'faq', 'Frequently Asked Questions', 'page-faq.php' );
	truepharm_ensure_page( 'contact-us', 'Contact Us', 'page-contact.php' );
	truepharm_ensure_page( 'rewards-program', 'Rewards Program', 'page-rewards.php' );
	truepharm_ensure_page( 'returns-logistics', 'Returns & Logistics', 'default', '<h2>Returns &amp; Logistics</h2>' . $lorem_simple );
	truepharm_ensure_page( 'track-my-order', 'Track My Order', 'default', '<h2>Track Your Order</h2><p>Enter your order details below to view the latest logistics status.</p>[woocommerce_order_tracking]' );

	// Legal parent + policy sub-pages.
	$legal_id = truepharm_ensure_page(
		'legal',
		'Legal & Policies',
		'page-legal.php',
		'<h2>Legal &amp; Policies</h2><p>Select a policy from the menu to review our operational and legal guidelines.</p>'
	);

	$lorem = '<p><!-- CLIENT TO FILL --> Lorem ipsum dolor sit amet, consectetur adipiscing elit. This placeholder content is editable from the WordPress editor. Replace it with your finalized policy text.</p><p>Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>';

	$children = array(
		'terms-of-service'     => array( 'Terms of Service', '<h2>Terms of Service</h2>' . $lorem ),
		'privacy-policy'       => array( 'Privacy Policy', '<h2>Privacy Policy</h2>' . $lorem ),
		'shipping-policy'      => array( 'Shipping Policy', '<h2>Shipping Policy</h2>' . $lorem ),
		'compliance-statement' => array( 'Compliance Statement', '<div class="legal-highlight"><h3>Mandatory Research-Use Acknowledgment</h3><p>All products are sold strictly for in-vitro laboratory research only and are NOT for human or veterinary use.</p></div><h2>Compliance Statement</h2>' . $lorem ),
	);

	// Top-level pages (so /terms-of-service/ etc. resolve by slug); grouped via the legal nav.
	unset( $legal_id );
	$order = 1;
	foreach ( $children as $slug => $data ) {
		truepharm_ensure_page( $slug, $data[0], 'page-legal.php', $data[1], 0, $order );
		++$order;
	}
}
