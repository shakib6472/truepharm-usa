<?php
/**
 * 404 — Not Found.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="error-404">
	<div class="wrap">
		<h1><?php esc_html_e( '404 — Page Not Found', 'truepharm' ); ?></h1>
		<p><?php esc_html_e( 'The page or formulation you are looking for may have been moved, retired, or never existed. Let\'s get you back on track.', 'truepharm' ); ?></p>
		<div class="error-404-search"><?php get_search_form(); ?></div>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-cart"><?php esc_html_e( 'Return to Homepage', 'truepharm' ); ?></a>
	</div>
</section>

<?php
get_footer();
