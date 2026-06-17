<?php
/**
 * Site footer: compliance four-column grid, legal disclaimer box,
 * and copyright / security row.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$truepharm_tagline = get_theme_mod(
	'truepharm_footer_tagline',
	__( 'USA-synthesized, clinical-grade cellular research solutions backed by empirical batch verification.', 'truepharm' )
);

$truepharm_legal = get_theme_mod(
	'truepharm_legal_disclaimer',
	__( 'All products offered by TruePharm USA are intended for laboratory research use only. These materials are not for human or veterinary consumption, medical use, diagnostic procedures, or therapeutic applications. TruePharm USA supplies products exclusively to qualified professionals and licensed research institutions. Statements on this website are for informational and educational purposes only and have not been evaluated by the U.S. Food and Drug Administration (FDA). By accessing this site, you confirm that you are a qualified researcher or institution representative and agree to use all materials in accordance with applicable laws, regulations, and safety guidelines.', 'truepharm' )
);

// Information-architecture links from the brief.
$truepharm_compounds = array(
	home_url( '/product-category/modulators/' )    => __( 'Modulators', 'truepharm' ),
	home_url( '/product-category/bioregulators/' ) => __( 'Bioregulators', 'truepharm' ),
	home_url( '/product-category/neuroactives/' )  => __( 'Neuroactives', 'truepharm' ),
	home_url( '/product-category/amino/' )         => __( 'Amino', 'truepharm' ),
	home_url( '/product-category/peptides/' )      => __( 'Peptides', 'truepharm' ),
);

$truepharm_support = array(
	home_url( '/shipping-policy/' )  => __( 'Shipping Policy', 'truepharm' ),
	home_url( '/returns-logistics/' ) => __( 'Returns &amp; Logistics', 'truepharm' ),
	home_url( '/coa-library/' )      => __( 'Certificate of Analysis (COA)', 'truepharm' ),
);

$truepharm_regulatory = array(
	home_url( '/why-us/' )           => __( 'About Us', 'truepharm' ),
	home_url( '/privacy-policy/' )   => __( 'Privacy Policy', 'truepharm' ),
	home_url( '/terms-of-service/' ) => __( 'Terms of Use', 'truepharm' ),
);
?>

<footer class="site-footer">
	<div class="wrap">
		<div class="foot-grid">

			<div class="foot-brand">
				<div class="foot-logo">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<span class="logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
					<?php endif; ?>
				</div>
				<p><?php echo esc_html( $truepharm_tagline ); ?></p>
			</div>

			<div>
				<h5><?php esc_html_e( 'Compounds', 'truepharm' ); ?></h5>
				<?php foreach ( $truepharm_compounds as $truepharm_url => $truepharm_label ) : ?>
					<a href="<?php echo esc_url( $truepharm_url ); ?>"><?php echo wp_kses_post( $truepharm_label ); ?></a>
				<?php endforeach; ?>
			</div>

			<div>
				<h5><?php esc_html_e( 'Support', 'truepharm' ); ?></h5>
				<?php foreach ( $truepharm_support as $truepharm_url => $truepharm_label ) : ?>
					<a href="<?php echo esc_url( $truepharm_url ); ?>"><?php echo wp_kses_post( $truepharm_label ); ?></a>
				<?php endforeach; ?>
			</div>

			<div>
				<h5><?php esc_html_e( 'Regulatory', 'truepharm' ); ?></h5>
				<?php foreach ( $truepharm_regulatory as $truepharm_url => $truepharm_label ) : ?>
					<a href="<?php echo esc_url( $truepharm_url ); ?>"><?php echo wp_kses_post( $truepharm_label ); ?></a>
				<?php endforeach; ?>
			</div>

		</div>

		<div class="legal-box">
			<p><?php echo esc_html( $truepharm_legal ); ?></p>
		</div>

		<div class="foot-bottom">
			<span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All Rights Reserved.', 'truepharm' ); ?></span>

			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'foot-legal-menu',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
			}
			?>

			<span class="foot-ssl">
				<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" style="vertical-align:-2px;margin-right:6px;"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
				<?php esc_html_e( 'Secure SSL / Encrypted Payment Gateway', 'truepharm' ); ?>
			</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
