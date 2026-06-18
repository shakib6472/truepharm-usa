<?php
/**
 * Template: Contact — Laboratory Support.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$tp_email   = get_theme_mod( 'truepharm_contact_email', 'info@truepharmusa.com' );
$tp_phone   = get_theme_mod( 'truepharm_contact_phone', '' );
$tp_address = get_theme_mod( 'truepharm_contact_address', '' );
?>

<header class="page-header">
	<div class="wrap reveal">
		<h1><?php esc_html_e( 'Laboratory Support', 'truepharm' ); ?></h1>
		<p><?php esc_html_e( 'Our team is available to assist with logistics, order inquiries, and institutional documentation.', 'truepharm' ); ?></p>
	</div>
</header>

<section class="contact-section wrap reveal">
	<div class="contact-layout">

		<!-- Info -->
		<div class="contact-info-block">
			<h3><?php esc_html_e( 'Get in Touch', 'truepharm' ); ?></h3>

			<div class="info-item">
				<div class="info-icon"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div>
				<div class="info-text">
					<h4><?php esc_html_e( 'Email Support', 'truepharm' ); ?></h4>
					<a href="mailto:<?php echo esc_attr( $tp_email ); ?>"><?php echo esc_html( $tp_email ); ?></a>
					<p style="font-size:0.8rem; margin-top:4px;"><?php esc_html_e( 'Response time: 24-48 hours', 'truepharm' ); ?></p>
				</div>
			</div>

			<div class="info-item">
				<div class="info-icon"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg></div>
				<div class="info-text">
					<h4><?php esc_html_e( 'Phone Support', 'truepharm' ); ?></h4>
					<p><?php echo esc_html( $tp_phone ? $tp_phone : __( 'Available upon request', 'truepharm' ) ); ?></p>
					<p style="font-size:0.8rem; margin-top:4px;"><?php esc_html_e( 'Mon-Fri: 9AM - 5PM EST', 'truepharm' ); ?></p>
				</div>
			</div>

			<div class="info-item">
				<div class="info-icon"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></div>
				<div class="info-text">
					<h4><?php esc_html_e( 'Corporate Headquarters', 'truepharm' ); ?></h4>
					<p><?php echo nl2br( esc_html( $tp_address ? $tp_address : __( 'Available upon request', 'truepharm' ) ) ); ?></p>
				</div>
			</div>

			<div class="compliance-warning">
				<div class="warning-title">
					<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
					<?php esc_html_e( 'Strict Compliance Notice', 'truepharm' ); ?>
				</div>
				<p><?php esc_html_e( 'Due to FDA regulations, TruePharm USA cannot and will not answer any questions regarding human or veterinary consumption, medical conditions, dosage, or administration protocols. Any inquiries of this nature will be permanently ignored and your account may be restricted.', 'truepharm' ); ?></p>
			</div>
		</div>

		<!-- Form -->
		<div class="contact-form-wrap">
			<h2><?php esc_html_e( 'Send us a message', 'truepharm' ); ?></h2>
			<p><?php esc_html_e( 'Fill out the form below and a member of our team will respond to the email address provided.', 'truepharm' ); ?></p>

			<form id="tp-contact-form" novalidate>
				<div class="form-row">
					<div class="form-group">
						<label for="cf-fname"><?php esc_html_e( 'First Name', 'truepharm' ); ?></label>
						<input type="text" id="cf-fname" name="first_name" class="form-control" placeholder="John" required>
					</div>
					<div class="form-group">
						<label for="cf-lname"><?php esc_html_e( 'Last Name', 'truepharm' ); ?></label>
						<input type="text" id="cf-lname" name="last_name" class="form-control" placeholder="Doe" required>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="cf-email"><?php esc_html_e( 'Email Address', 'truepharm' ); ?></label>
						<input type="email" id="cf-email" name="email" class="form-control" placeholder="john@example.com" required>
					</div>
					<div class="form-group">
						<label for="cf-order"><?php esc_html_e( 'Order Number (Optional)', 'truepharm' ); ?></label>
						<input type="text" id="cf-order" name="order_number" class="form-control" placeholder="#TP-12345">
					</div>
				</div>

				<div class="form-group">
					<label for="cf-subject"><?php esc_html_e( 'Subject', 'truepharm' ); ?></label>
					<select id="cf-subject" name="subject" class="form-control" required>
						<option value="" disabled selected><?php esc_html_e( 'Please select a topic...', 'truepharm' ); ?></option>
						<option value="shipping"><?php esc_html_e( 'Shipping & Logistics Tracking', 'truepharm' ); ?></option>
						<option value="missing"><?php esc_html_e( 'Missing or Damaged Items', 'truepharm' ); ?></option>
						<option value="coa"><?php esc_html_e( 'COA / Analytical Data Request', 'truepharm' ); ?></option>
						<option value="wholesale"><?php esc_html_e( 'Institutional / Bulk Inquiry', 'truepharm' ); ?></option>
						<option value="other"><?php esc_html_e( 'Other Inquiry', 'truepharm' ); ?></option>
					</select>
				</div>

				<div class="form-group">
					<label for="cf-message"><?php esc_html_e( 'Message', 'truepharm' ); ?></label>
					<textarea id="cf-message" name="message" class="form-control" placeholder="<?php esc_attr_e( 'How can we assist your research today?', 'truepharm' ); ?>" required></textarea>
				</div>

				<?php do_action( 'tp_turnstile_widget' ); ?>

				<button type="submit" class="btn-rosegold"><?php esc_html_e( 'Submit Inquiry', 'truepharm' ); ?></button>
				<p class="contact-msg" id="tp-contact-msg" role="status" aria-live="polite"></p>
			</form>
		</div>

	</div>
</section>

<?php
get_footer();
