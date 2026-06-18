<?php
/**
 * Template: Why Us — The TruePharm Standard.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$tp_shop_url  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$tp_why_image = get_theme_mod( 'truepharm_why_image' );

$tp_steps = array(
	__( 'Synthesis & Lyophilization', 'truepharm' )              => get_theme_mod( 'truepharm_why_step1', __( 'Compounds are synthesized and immediately freeze-dried (lyophilized) to remove moisture and lock the peptide into a stable, solid state for transport and storage.', 'truepharm' ) ),
	__( 'Independent Laboratory Quarantine', 'truepharm' )       => get_theme_mod( 'truepharm_why_step2', __( 'Before any product is cleared for our catalog, samples from the new batch are sent to an independent analytical laboratory for unbiased verification.', 'truepharm' ) ),
	__( 'HPLC & Mass Spectrometry Analysis', 'truepharm' )       => get_theme_mod( 'truepharm_why_step3', __( 'The lab performs Mass Spectrometry to confirm the exact amino acid sequence (Identity) and HPLC to ensure the sample meets our strict >99% standard (Purity).', 'truepharm' ) ),
	__( 'Documentation & Dispatch', 'truepharm' )                => get_theme_mod( 'truepharm_why_step4', __( 'Once cleared, the Certificates of Analysis are uploaded to our public library. The batch is moved to our climate-controlled staging area, ready for secure dispatch to your research facility.', 'truepharm' ) ),
);
?>

<header class="page-header">
	<div class="wrap reveal">
		<h1><?php esc_html_e( 'The TruePharm Standard', 'truepharm' ); ?></h1>
		<p><?php esc_html_e( 'Bridging the critical gap between commercial availability and clinical-grade reliability. We supply independent researchers and institutions with empirical, data-backed cellular compounds.', 'truepharm' ); ?></p>
	</div>
</header>

<main id="primary">

	<!-- Mission -->
	<section class="mission-section wrap reveal">
		<div class="mission-grid">
			<div class="mission-text">
				<span class="eyebrow"><?php esc_html_e( 'The Industry Problem', 'truepharm' ); ?></span>
				<h2><?php echo esc_html( get_theme_mod( 'truepharm_why_heading', __( 'Research demands precision. The market lacked transparency.', 'truepharm' ) ) ); ?></h2>
				<p><?php echo esc_html( get_theme_mod( 'truepharm_why_p1', __( 'The research peptide and cellular modulator industry has historically been plagued by a severe lack of accountability. Researchers are frequently forced to rely on opaque suppliers who hide behind generic, outdated Certificates of Analysis or fail to test their batches entirely.', 'truepharm' ) ) ); ?></p>
				<p><strong><?php echo esc_html( get_theme_mod( 'truepharm_why_p2', __( 'TruePharm USA was founded to solve this problem.', 'truepharm' ) ) ); ?></strong></p>
				<p><?php echo esc_html( get_theme_mod( 'truepharm_why_p3', __( 'We do not compromise on molecular identity or compound purity. Every vial that leaves our facility has been mathematically verified through independent, third-party laboratory analysis to guarantee it performs exactly as intended in your in-vitro applications.', 'truepharm' ) ) ); ?></p>
			</div>
			<?php if ( $tp_why_image ) : ?>
				<div class="mission-img"><?php echo wp_get_attachment_image( (int) $tp_why_image, 'large', false, array( 'style' => 'width:100%;height:100%;object-fit:cover;' ) ); ?></div>
			<?php else : ?>
				<div class="ph-img mission-img"><?php esc_html_e( 'LABORATORY / MICROSCOPE IMAGE', 'truepharm' ); ?></div>
			<?php endif; ?>
		</div>
	</section>

	<!-- Standards -->
	<section class="standards-section reveal">
		<div class="wrap">
			<div class="standards-header">
				<span class="eyebrow"><?php esc_html_e( 'Our Guarantees', 'truepharm' ); ?></span>
				<h2><?php esc_html_e( 'Uncompromising Quality Control', 'truepharm' ); ?></h2>
			</div>
			<div class="standards-grid">
				<div class="standard-card">
					<div class="standard-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></div>
					<h3><?php esc_html_e( 'Empirical Purity (>99%)', 'truepharm' ); ?></h3>
					<p><?php esc_html_e( 'We utilize independent High-Performance Liquid Chromatography (HPLC) to ensure absolute compound purity, stripping away synthetic byproducts.', 'truepharm' ); ?></p>
				</div>
				<div class="standard-card">
					<div class="standard-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></div>
					<h3><?php esc_html_e( 'Transparent Traceability', 'truepharm' ); ?></h3>
					<p><?php esc_html_e( 'Every single batch is assigned a unique lot number. We publicly publish the corresponding Mass Spectrometry and HPLC reports in our COA Library.', 'truepharm' ); ?></p>
				</div>
				<div class="standard-card">
					<div class="standard-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="22"></line><line x1="22" y1="12" x2="2" y2="12"></line><line x1="19.07" y1="4.93" x2="4.93" y2="19.07"></line><line x1="19.07" y1="19.07" x2="4.93" y2="4.93"></line></svg></div>
					<h3><?php esc_html_e( 'Molecular Stability', 'truepharm' ); ?></h3>
					<p><?php esc_html_e( 'Peptides are delicate. Our lyophilized compounds are stored in climate-controlled environments and dispatched rapidly to prevent molecular degradation.', 'truepharm' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<!-- Process / Timeline -->
	<section class="process-section reveal">
		<div class="wrap">
			<div class="process-header">
				<span class="eyebrow"><?php esc_html_e( 'The Pipeline', 'truepharm' ); ?></span>
				<h2><?php esc_html_e( 'How We Verify Every Batch', 'truepharm' ); ?></h2>
			</div>
			<div class="timeline">
				<?php
				$tp_i = 1;
				foreach ( $tp_steps as $tp_title => $tp_desc ) :
					?>
					<div class="timeline-item">
						<div class="timeline-number"><?php echo esc_html( (string) $tp_i ); ?></div>
						<div class="timeline-content">
							<h3><?php echo esc_html( $tp_title ); ?></h3>
							<p><?php echo esc_html( $tp_desc ); ?></p>
						</div>
					</div>
					<?php
					++$tp_i;
				endforeach;
				?>
			</div>
		</div>
	</section>

	<!-- CTA Banner -->
	<section class="cta-banner reveal">
		<div class="wrap">
			<h2><?php esc_html_e( 'Ready to elevate your research?', 'truepharm' ); ?></h2>
			<p><?php esc_html_e( 'Browse our complete catalog of verified, clinical-grade formulations.', 'truepharm' ); ?></p>
			<div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
				<a href="<?php echo esc_url( $tp_shop_url ); ?>" class="btn-white"><?php esc_html_e( 'Shop Formulations', 'truepharm' ); ?></a>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
