<?php
/**
 * Template: FAQ.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$tp_coa_url     = get_post_type_archive_link( 'coa_library' ) ?: home_url( '/coa-library/' );
$tp_contact_url = home_url( '/contact-us/' );
$tp_track_url   = home_url( '/track-my-order/' );

/**
 * Render one accordion item.
 */
$faq_item = static function ( string $q, string $answer_html ): void {
	?>
	<div class="faq-item">
		<button class="faq-question" aria-expanded="false"><?php echo esc_html( $q ); ?><span class="faq-icon"></span></button>
		<div class="faq-answer">
			<div class="faq-answer-inner"><?php echo wp_kses_post( $answer_html ); ?></div>
		</div>
	</div>
	<?php
};
?>

<header class="page-header">
	<div class="wrap reveal">
		<h1><?php esc_html_e( 'Frequently Asked Questions', 'truepharm' ); ?></h1>
		<p><?php esc_html_e( 'Answers on compliance, laboratory storage, logistics, and batch verification for our research community.', 'truepharm' ); ?></p>
	</div>
</header>

<section class="wrap faq-layout">

	<aside class="faq-sidebar reveal">
		<h3><?php esc_html_e( 'Categories', 'truepharm' ); ?></h3>
		<ul class="faq-nav">
			<li><a href="#compliance" class="active"><?php esc_html_e( 'Legal & Compliance', 'truepharm' ); ?></a></li>
			<li><a href="#storage"><?php esc_html_e( 'Laboratory Storage', 'truepharm' ); ?></a></li>
			<li><a href="#shipping"><?php esc_html_e( 'Logistics & Shipping', 'truepharm' ); ?></a></li>
			<li><a href="#quality"><?php esc_html_e( 'Quality & Purity', 'truepharm' ); ?></a></li>
		</ul>
	</aside>

	<div class="faq-content">

		<div class="faq-group reveal" id="compliance">
			<h2><?php esc_html_e( 'Legal & Compliance', 'truepharm' ); ?></h2>
			<?php
			$faq_item(
				__( 'Are these products intended for human or animal consumption?', 'truepharm' ),
				'<p><strong>' . esc_html__( 'Absolutely not.', 'truepharm' ) . '</strong> ' . esc_html__( 'All formulations provided by TruePharm USA are strictly intended for in-vitro research and laboratory experimentation only.', 'truepharm' ) . '</p><p>' . esc_html__( 'These products are not intended to diagnose, treat, cure, or prevent any disease. Purchasing requires acknowledgment that you are a qualified researcher utilizing these compounds in a controlled laboratory environment.', 'truepharm' ) . '</p>'
			);
			$faq_item(
				__( 'Do I need a prescription to order?', 'truepharm' ),
				'<p>' . esc_html__( 'No prescription is required because our products are not medications, drugs, or supplements. They are laboratory research chemicals. However, you must agree to our Terms and Conditions verifying your status as a researcher prior to checkout.', 'truepharm' ) . '</p>'
			);
			?>
		</div>

		<div class="faq-group reveal" id="storage">
			<h2><?php esc_html_e( 'Laboratory Storage & Reconstitution', 'truepharm' ); ?></h2>
			<?php
			$faq_item(
				__( 'How should I store my lyophilized peptides?', 'truepharm' ),
				'<p>' . esc_html__( 'For long-term storage, lyophilized (freeze-dried) powder should be stored at or below -20°C in a desiccated environment. For short-term storage (up to 30 days), room temperature away from direct UV light is acceptable without significant molecular degradation.', 'truepharm' ) . '</p>'
			);
			$faq_item(
				__( 'How do I store reconstituted formulations?', 'truepharm' ),
				'<p>' . esc_html__( 'Once reconstituted with bacteriostatic water, the solution must be stored in a refrigerator between 2°C and 8°C. Do not freeze reconstituted solutions, as this will destroy the peptide bonds. Avoid shaking the vial; gently swirl to mix.', 'truepharm' ) . '</p>'
			);
			$faq_item(
				__( 'Do you provide reconstitution instructions or water?', 'truepharm' ),
				'<p>' . esc_html__( 'TruePharm USA supplies the raw lyophilized compound only. We do not provide bacteriostatic water, syringes, or specific reconstitution ratios, as these variables depend entirely on your specific research protocol and experimental design.', 'truepharm' ) . '</p>'
			);
			?>
		</div>

		<div class="faq-group reveal" id="shipping">
			<h2><?php esc_html_e( 'Logistics & Shipping', 'truepharm' ); ?></h2>
			<?php
			$faq_item(
				__( 'When will my order dispatch?', 'truepharm' ),
				'<p>' . esc_html__( 'All orders placed before 2:00 PM EST (Monday-Friday) are processed and dispatched the same day from our climate-controlled facility. Orders placed on weekends will be dispatched the following Monday.', 'truepharm' ) . '</p>'
			);
			$faq_item(
				__( 'How do I track my shipment?', 'truepharm' ),
				'<p>' . esc_html__( 'Once your shipping label is generated, you will automatically receive an email containing your tracking data. You can also visit our', 'truepharm' ) . ' <a href="' . esc_url( $tp_track_url ) . '">' . esc_html__( 'Track My Order', 'truepharm' ) . '</a> ' . esc_html__( 'page and input your order ID and email to see live logistics updates.', 'truepharm' ) . '</p>'
			);
			$faq_item(
				__( 'What is your return policy?', 'truepharm' ),
				'<p>' . esc_html__( 'Due to the sensitive nature of research compounds and strict chain-of-custody protocols, we cannot accept returns once a product has left our facility. If an error occurred on our end regarding fulfillment, please contact our support team within 48 hours of delivery.', 'truepharm' ) . '</p>'
			);
			?>
		</div>

		<div class="faq-group reveal" id="quality">
			<h2><?php esc_html_e( 'Quality & Purity', 'truepharm' ); ?></h2>
			<?php
			$faq_item(
				__( 'How do I verify the purity of my batch?', 'truepharm' ),
				'<p>' . esc_html__( "Locate the specific Batch or Lot Number printed on your vial's label. Navigate to our", 'truepharm' ) . ' <a href="' . esc_url( $tp_coa_url ) . '">' . esc_html__( 'COA Library', 'truepharm' ) . '</a> ' . esc_html__( 'and enter that number into the database to view and download the official HPLC and Mass Spectrometry reports for your exact formulation.', 'truepharm' ) . '</p>'
			);
			?>
		</div>

		<div class="faq-cta reveal">
			<h3><?php esc_html_e( 'Still have questions?', 'truepharm' ); ?></h3>
			<p><?php esc_html_e( 'Our laboratory support team is available to assist with logistics or documentation inquiries.', 'truepharm' ); ?></p>
			<a href="<?php echo esc_url( $tp_contact_url ); ?>" class="btn-rosegold"><?php esc_html_e( 'Contact Support', 'truepharm' ); ?></a>
		</div>

	</div>
</section>

<?php
get_footer();
