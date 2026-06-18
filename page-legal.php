<?php
/**
 * Template: Legal & Policies (parent + four policy sub-pages).
 *
 * Assigned to the Legal pages via _wp_page_template in inc/theme-activation.php.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$tp_legal_pages = array(
	'terms-of-use'         => __( 'Terms of Use', 'truepharm' ),
	'privacy-policy'       => __( 'Privacy Policy', 'truepharm' ),
	'shipping-returns'     => __( 'Shipping & Returns', 'truepharm' ),
	'compliance-statement' => __( 'Compliance Statement', 'truepharm' ),
);

$tp_current_id = get_queried_object_id();

while ( have_posts() ) :
	the_post();
	?>

	<header class="page-header">
		<div class="wrap reveal">
			<h1><?php the_title(); ?></h1>
			<p><?php esc_html_e( 'Please review our operational and legal guidelines.', 'truepharm' ); ?></p>
		</div>
	</header>

	<section class="legal-section">
		<div class="wrap legal-layout">

			<aside class="legal-nav-wrap">
				<nav class="legal-nav" aria-label="<?php esc_attr_e( 'Legal pages', 'truepharm' ); ?>">
					<?php
					foreach ( $tp_legal_pages as $tp_slug => $tp_label ) :
						$tp_page = get_page_by_path( $tp_slug );
						if ( ! $tp_page ) {
							continue;
						}
						$tp_active = ( (int) $tp_page->ID === (int) $tp_current_id ) ? 'active' : '';
						printf(
							'<a href="%s" class="%s">%s</a>',
							esc_url( get_permalink( $tp_page ) ),
							esc_attr( $tp_active ),
							esc_html( $tp_label )
						);
					endforeach;
					?>
				</nav>
			</aside>

			<div class="legal-content reveal">
				<span class="update-date">
					<?php
					/* translators: %s: last modified date. */
					printf( esc_html__( 'Last Updated: %s', 'truepharm' ), esc_html( get_the_modified_date() ) );
					?>
				</span>
				<?php the_content(); ?>
			</div>

		</div>
	</section>

	<?php
endwhile;

get_footer();
