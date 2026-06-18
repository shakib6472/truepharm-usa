<?php
/**
 * Customizer — editable text/image fields used across the theme.
 *
 * Registers the exact theme-mod keys the templates read so every brief
 * default becomes client-editable. (Cloudflare Turnstile keys are registered
 * separately in inc/turnstile.php.)
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function truepharm_customize_register( WP_Customize_Manager $wp_customize ): void {

	$text     = 'sanitize_text_field';
	$textarea = 'sanitize_textarea_field';

	/**
	 * Helper to add a setting + control quickly.
	 */
	$add = static function ( $id, $label, $section, $default, $type, $sanitize ) use ( $wp_customize ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $default,
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => $section,
				'type'    => $type,
			)
		);
	};

	/* ---- Top Bar ---- */
	$wp_customize->add_section( 'truepharm_topbar', array( 'title' => __( 'TruePharm: Top Bar', 'truepharm' ), 'priority' => 130 ) );
	$add( 'truepharm_topbar_left', __( 'Left text', 'truepharm' ), 'truepharm_topbar', __( 'Secure Checkout', 'truepharm' ), 'text', $text );
	$add( 'truepharm_topbar_center', __( 'Center text', 'truepharm' ), 'truepharm_topbar', __( 'Authorized Clinical Research Formulas', 'truepharm' ), 'text', $text );
	$add( 'truepharm_topbar_right', __( 'Right text', 'truepharm' ), 'truepharm_topbar', __( 'Free Priority Logistics on Orders $200+', 'truepharm' ), 'text', $text );

	/* ---- Homepage ---- */
	$wp_customize->add_section( 'truepharm_home', array( 'title' => __( 'TruePharm: Homepage', 'truepharm' ), 'priority' => 131 ) );
	$add( 'truepharm_hero_mission', __( 'Hero mission text', 'truepharm' ), 'truepharm_home', __( 'High-purity lyophilized peptides, bioregulators, and precise cellular modulators engineered strictly for rigorous laboratory and in-vitro research applications.', 'truepharm' ), 'textarea', $textarea );
	$add( 'truepharm_goals_title', __( 'Lab goals heading', 'truepharm' ), 'truepharm_home', __( 'Pioneering Cellular Integrity', 'truepharm' ), 'text', $text );
	$add( 'truepharm_goals_p1', __( 'Lab goals paragraph 1', 'truepharm' ), 'truepharm_home', __( 'TruePharm USA was established to bridge the gap between high-level laboratory research and clinical-grade, third-party verified cellular compounds. We prioritize absolute compound identity, strict chain of custody, and molecular stability above all else.', 'truepharm' ), 'textarea', $textarea );
	$add( 'truepharm_goals_p2', __( 'Lab goals paragraph 2', 'truepharm' ), 'truepharm_home', __( 'Through empirical vetting and rigorous batch protocols, we provide independent researchers and institutions with reliable, data-backed compounds engineered for absolute precision.', 'truepharm' ), 'textarea', $textarea );
	$wp_customize->add_setting( 'truepharm_goals_image', array( 'default' => '', 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'truepharm_goals_image', array( 'label' => __( 'Lab goals image', 'truepharm' ), 'section' => 'truepharm_home', 'mime_type' => 'image' ) ) );

	/* ---- Why Us ---- */
	$wp_customize->add_section( 'truepharm_why', array( 'title' => __( 'TruePharm: Why Us', 'truepharm' ), 'priority' => 132 ) );
	$add( 'truepharm_why_heading', __( 'Mission heading', 'truepharm' ), 'truepharm_why', __( 'Research demands precision. The market lacked transparency.', 'truepharm' ), 'text', $text );
	$add( 'truepharm_why_p1', __( 'Mission paragraph 1', 'truepharm' ), 'truepharm_why', __( 'The research peptide and cellular modulator industry has historically been plagued by a severe lack of accountability. Researchers are frequently forced to rely on opaque suppliers who hide behind generic, outdated Certificates of Analysis or fail to test their batches entirely.', 'truepharm' ), 'textarea', $textarea );
	$add( 'truepharm_why_p2', __( 'Mission paragraph 2', 'truepharm' ), 'truepharm_why', __( 'TruePharm USA was founded to solve this problem.', 'truepharm' ), 'textarea', $textarea );
	$add( 'truepharm_why_p3', __( 'Mission paragraph 3', 'truepharm' ), 'truepharm_why', __( 'We do not compromise on molecular identity or compound purity. Every vial that leaves our facility has been mathematically verified through independent, third-party laboratory analysis to guarantee it performs exactly as intended in your in-vitro applications.', 'truepharm' ), 'textarea', $textarea );
	$add( 'truepharm_why_step1', __( 'Pipeline step 1', 'truepharm' ), 'truepharm_why', __( 'Compounds are synthesized and immediately freeze-dried (lyophilized) to remove moisture and lock the peptide into a stable, solid state for transport and storage.', 'truepharm' ), 'textarea', $textarea );
	$add( 'truepharm_why_step2', __( 'Pipeline step 2', 'truepharm' ), 'truepharm_why', __( 'Before any product is cleared for our catalog, samples from the new batch are sent to an independent analytical laboratory for unbiased verification.', 'truepharm' ), 'textarea', $textarea );
	$add( 'truepharm_why_step3', __( 'Pipeline step 3', 'truepharm' ), 'truepharm_why', __( 'The lab performs Mass Spectrometry to confirm the exact amino acid sequence (Identity) and HPLC to ensure the sample meets our strict >99% standard (Purity).', 'truepharm' ), 'textarea', $textarea );
	$add( 'truepharm_why_step4', __( 'Pipeline step 4', 'truepharm' ), 'truepharm_why', __( 'Once cleared, the Certificates of Analysis are uploaded to our public library. The batch is moved to our climate-controlled staging area, ready for secure dispatch to your research facility.', 'truepharm' ), 'textarea', $textarea );
	$wp_customize->add_setting( 'truepharm_why_image', array( 'default' => '', 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'truepharm_why_image', array( 'label' => __( 'Mission image', 'truepharm' ), 'section' => 'truepharm_why', 'mime_type' => 'image' ) ) );

	/* ---- Contact ---- */
	$wp_customize->add_section( 'truepharm_contact', array( 'title' => __( 'TruePharm: Contact', 'truepharm' ), 'priority' => 133 ) );
	$add( 'truepharm_contact_email', __( 'Support email', 'truepharm' ), 'truepharm_contact', 'info@truepharmusa.com', 'text', 'sanitize_email' );
	$add( 'truepharm_contact_phone', __( 'Support phone', 'truepharm' ), 'truepharm_contact', '', 'text', $text );
	$add( 'truepharm_contact_address', __( 'Corporate address', 'truepharm' ), 'truepharm_contact', '', 'textarea', $textarea );

	/* ---- Footer ---- */
	$wp_customize->add_section( 'truepharm_footer', array( 'title' => __( 'TruePharm: Footer', 'truepharm' ), 'priority' => 134 ) );
	$add( 'truepharm_footer_tagline', __( 'Footer tagline', 'truepharm' ), 'truepharm_footer', __( 'USA-synthesized, clinical-grade cellular research solutions backed by empirical batch verification.', 'truepharm' ), 'textarea', $textarea );
	$add( 'truepharm_legal_disclaimer', __( 'Footer legal disclaimer', 'truepharm' ), 'truepharm_footer', __( 'All products offered by TruePharm USA are intended for laboratory research use only. These materials are not for human or veterinary consumption, medical use, diagnostic procedures, or therapeutic applications. TruePharm USA supplies products exclusively to qualified professionals and licensed research institutions. Statements on this website are for informational and educational purposes only and have not been evaluated by the U.S. Food and Drug Administration (FDA). By accessing this site, you confirm that you are a qualified researcher or institution representative and agree to use all materials in accordance with applicable laws, regulations, and safety guidelines.', 'truepharm' ), 'textarea', $textarea );
}
add_action( 'customize_register', 'truepharm_customize_register' );
