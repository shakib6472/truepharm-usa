<?php
/**
 * WooCommerce shop / product archive — "Clinical Formulations".
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

global $wp_query;

$tp_shop_url = wc_get_page_permalink( 'shop' );

// Current archive base URL (category term link when viewing a category).
$tp_base_url = $tp_shop_url;
if ( is_product_category() ) {
	$tp_current_term = get_queried_object();
	$tp_term_link    = get_term_link( $tp_current_term );
	if ( ! is_wp_error( $tp_term_link ) ) {
		$tp_base_url = $tp_term_link;
	}
}

// phpcs:disable WordPress.Security.NonceVerification.Recommended
$tp_orderby   = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'menu_order';
$tp_min_price = isset( $_GET['min_price'] ) ? esc_attr( wp_unslash( $_GET['min_price'] ) ) : '';
$tp_max_price = isset( $_GET['max_price'] ) ? esc_attr( wp_unslash( $_GET['max_price'] ) ) : '';
// phpcs:enable WordPress.Security.NonceVerification.Recommended

$tp_orderby_options = array(
	'menu_order' => __( 'Default sorting', 'truepharm' ),
	'popularity' => __( 'Sort by popularity', 'truepharm' ),
	'date'       => __( 'Sort by latest', 'truepharm' ),
	'price'      => __( 'Sort by price: low to high', 'truepharm' ),
	'price-desc' => __( 'Sort by price: high to low', 'truepharm' ),
);
?>

<header class="page-header">
	<div class="wrap">
		<h1><?php esc_html_e( 'Clinical Formulations', 'truepharm' ); ?></h1>
		<p><?php esc_html_e( 'Browse our complete catalog of high-purity, third-party verified cellular modulators and peptides engineered strictly for in-vitro research.', 'truepharm' ); ?></p>
	</div>
</header>

<section>
	<div class="shop-layout" id="primary">

		<!-- Sidebar -->
		<aside class="shop-sidebar">

			<div class="filter-group">
				<form class="search-box" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<input type="text" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e( 'Search compounds...', 'truepharm' ); ?>">
					<input type="hidden" name="post_type" value="product">
					<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'truepharm' ); ?>">&#128269;</button>
				</form>
			</div>

			<div class="filter-group">
				<h3><?php esc_html_e( 'Molecular Class', 'truepharm' ); ?></h3>
				<ul>
					<li><a href="<?php echo esc_url( $tp_shop_url ); ?>" class="<?php echo is_shop() ? 'active' : ''; ?>"><?php esc_html_e( 'All Compounds', 'truepharm' ); ?></a></li>
					<?php
					$tp_terms = get_terms(
						array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => false,
							'exclude'    => array( get_option( 'default_product_cat', 0 ) ),
						)
					);
					if ( $tp_terms && ! is_wp_error( $tp_terms ) ) {
						foreach ( $tp_terms as $tp_term ) {
							$tp_active = ( is_product_category() && get_queried_object_id() === $tp_term->term_id ) ? 'active' : '';
							printf(
								'<li><a href="%s" class="%s">%s</a></li>',
								esc_url( get_term_link( $tp_term ) ),
								esc_attr( $tp_active ),
								esc_html( $tp_term->name )
							);
						}
					}
					?>
				</ul>
			</div>

			<div class="filter-group">
				<h3><?php esc_html_e( 'Filter by Price', 'truepharm' ); ?></h3>
				<form method="get" action="<?php echo esc_url( $tp_base_url ); ?>">
					<div class="price-inputs">
						<input type="number" name="min_price" min="0" step="1" value="<?php echo esc_attr( $tp_min_price ); ?>" placeholder="<?php esc_attr_e( 'Min', 'truepharm' ); ?>" aria-label="<?php esc_attr_e( 'Minimum price', 'truepharm' ); ?>">
						<span>-</span>
						<input type="number" name="max_price" min="0" step="1" value="<?php echo esc_attr( $tp_max_price ); ?>" placeholder="<?php esc_attr_e( 'Max', 'truepharm' ); ?>" aria-label="<?php esc_attr_e( 'Maximum price', 'truepharm' ); ?>">
					</div>
					<?php if ( 'menu_order' !== $tp_orderby ) : ?>
						<input type="hidden" name="orderby" value="<?php echo esc_attr( $tp_orderby ); ?>">
					<?php endif; ?>
					<button type="submit" class="filter-btn"><?php esc_html_e( 'Apply Filter', 'truepharm' ); ?></button>
				</form>
			</div>

			<div class="trust-badge">
				<span><?php esc_html_e( '>99% Purity', 'truepharm' ); ?></span>
				<p><?php esc_html_e( 'Verified via independent HPLC & Mass Spectrometry', 'truepharm' ); ?></p>
			</div>
		</aside>

		<!-- Main -->
		<div class="shop-main">

			<?php woocommerce_output_all_notices(); ?>

			<div class="shop-controls">
				<span class="shop-result-count">
					<?php
					$tp_total = (int) $wp_query->found_posts;
					printf(
						/* translators: %s: number of products. */
						esc_html( _n( '%s formula', '%s formulas', $tp_total, 'truepharm' ) ),
						esc_html( number_format_i18n( $tp_total ) )
					);
					?>
				</span>
				<select class="sort-dropdown" id="tp-sort" aria-label="<?php esc_attr_e( 'Shop order', 'truepharm' ); ?>">
					<?php foreach ( $tp_orderby_options as $tp_val => $tp_lbl ) : ?>
						<option value="<?php echo esc_attr( $tp_val ); ?>" <?php selected( $tp_orderby, $tp_val ); ?>><?php echo esc_html( $tp_lbl ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<?php if ( have_posts() ) : ?>
				<div class="shop-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						$tp_product = wc_get_product( get_the_ID() );
						if ( $tp_product ) {
							tp_product_card( $tp_product, 'add' );
						}
					endwhile;
					?>
				</div>

				<?php
				if ( function_exists( 'woocommerce_pagination' ) ) {
					woocommerce_pagination();
				}
				?>
			<?php else : ?>
				<div class="shop-empty">
					<h3><?php esc_html_e( 'No formulas found', 'truepharm' ); ?></h3>
					<p><?php esc_html_e( 'Try adjusting your filters or search terms.', 'truepharm' ); ?></p>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>

<?php
get_footer();
