<?php
/**
 * Page template.
 *
 * WooCommerce pages (account / checkout / cart) render their own full-width
 * layout, so they output bare; other pages get a centred content wrapper.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$tp_is_wc_page = function_exists( 'is_woocommerce' )
	&& ( is_account_page() || is_checkout() || is_cart() );

while ( have_posts() ) :
	the_post();

	if ( $tp_is_wc_page ) :
		?>
		<main id="primary" class="site-main"><?php the_content(); ?></main>
		<?php
	else :
		?>
		<main id="primary" class="site-main">
			<div class="wrap" style="padding-top:48px;padding-bottom:64px;">
				<?php if ( ! is_front_page() ) : ?>
					<header class="section-head"><h2><?php the_title(); ?></h2></header>
				<?php endif; ?>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'truepharm' ),
							'after'  => '</div>',
						)
					);
					?>
				</div>
			</div>
		</main>
		<?php
	endif;

endwhile;

get_footer();
