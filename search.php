<?php
/**
 * Search results.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<header class="page-header">
	<div class="wrap">
		<h1>
			<?php
			/* translators: %s: search query. */
			printf( esc_html__( 'Search Results for: %s', 'truepharm' ), '<span style="color:var(--warm);">' . esc_html( get_search_query() ) . '</span>' );
			?>
		</h1>
		<p>
			<?php
			global $wp_query;
			printf(
				/* translators: %s: result count. */
				esc_html( _n( '%s result found.', '%s results found.', (int) $wp_query->found_posts, 'truepharm' ) ),
				esc_html( number_format_i18n( (int) $wp_query->found_posts ) )
			);
			?>
		</p>
	</div>
</header>

<section class="wrap" style="padding:60px 28px;">
	<?php get_search_form(); ?>

	<?php if ( have_posts() ) : ?>
		<div class="shop-grid" style="margin-top:40px;">
			<?php
			while ( have_posts() ) :
				the_post();

				if ( 'product' === get_post_type() && function_exists( 'wc_get_product' ) ) {
					$tp_product = wc_get_product( get_the_ID() );
					if ( $tp_product ) {
						tp_product_card( $tp_product, 'view' );
						continue;
					}
				}
				?>
				<article class="product-card">
					<a class="pimg<?php echo has_post_thumbnail() ? '' : ' ph-img'; ?>" href="<?php the_permalink(); ?>">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'woocommerce_thumbnail' );
						} else {
							echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ?? __( 'Result', 'truepharm' ) );
						}
						?>
					</a>
					<div class="product-info">
						<div class="specs"><span><?php echo esc_html( get_post_type() ); ?></span></div>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p style="font-size:0.9rem;color:var(--slate-soft);"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
						<a href="<?php the_permalink(); ?>" class="btn-add"><?php esc_html_e( 'View', 'truepharm' ); ?></a>
					</div>
				</article>
				<?php
			endwhile;
			?>
		</div>

		<?php
		the_posts_pagination(
			array(
				'mid_size'  => 1,
				'prev_text' => __( 'Previous', 'truepharm' ),
				'next_text' => __( 'Next', 'truepharm' ),
			)
		);
		?>

	<?php else : ?>
		<div class="shop-empty" style="margin-top:40px;">
			<h3><?php esc_html_e( 'No results found', 'truepharm' ); ?></h3>
			<p><?php esc_html_e( 'Try a different search term, or browse the full catalog.', 'truepharm' ); ?></p>
			<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>" class="btn btn-cart"><?php esc_html_e( 'Browse Formulations', 'truepharm' ); ?></a>
		</div>
	<?php endif; ?>
</section>

<?php
get_footer();
