<?php
/**
 * Single Certificate of Analysis — data summary + PDF viewer + download.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$tp_archive_url = get_post_type_archive_link( 'coa_library' ) ?: home_url( '/coa-library/' );

while ( have_posts() ) :
	the_post();

	$tp_compound = get_the_title();
	$tp_batch    = (string) get_post_meta( get_the_ID(), TP_COA_BATCH, true );
	$tp_date_raw = (string) get_post_meta( get_the_ID(), TP_COA_DATE, true );
	$tp_purity   = (string) get_post_meta( get_the_ID(), TP_COA_PURITY, true );
	$tp_pdf_id   = (int) get_post_meta( get_the_ID(), TP_COA_PDF, true );
	$tp_pdf_url  = $tp_pdf_id ? wp_get_attachment_url( $tp_pdf_id ) : '';
	$tp_date     = $tp_date_raw ? date_i18n( get_option( 'date_format' ), strtotime( $tp_date_raw ) ) : '—';
	?>

	<section class="doc-wrap" id="primary">

		<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'truepharm' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'truepharm' ); ?></a>
			<span>/</span>
			<a href="<?php echo esc_url( $tp_archive_url ); ?>"><?php esc_html_e( 'COA Library', 'truepharm' ); ?></a>
			<span>/</span>
			<?php echo esc_html( $tp_compound ); ?>
		</nav>

		<div class="coa-header">
			<h1><?php printf( /* translators: %s: compound name. */ esc_html__( 'Certificate of Analysis: %s', 'truepharm' ), esc_html( $tp_compound ) ); ?></h1>
			<?php if ( $tp_batch ) : ?>
				<p><?php printf( /* translators: %s: batch number. */ esc_html__( 'Official third-party laboratory verification for Batch %s', 'truepharm' ), '<strong>' . esc_html( $tp_batch ) . '</strong>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- printf args escaped. ?></p>
			<?php endif; ?>
		</div>

		<!-- Data Summary -->
		<div class="data-summary">
			<div class="data-block">
				<span><?php esc_html_e( 'Compound', 'truepharm' ); ?></span>
				<strong><?php echo esc_html( $tp_compound ); ?></strong>
			</div>
			<div class="data-block">
				<span><?php esc_html_e( 'Batch Number', 'truepharm' ); ?></span>
				<strong><?php echo esc_html( $tp_batch ?: '—' ); ?></strong>
			</div>
			<div class="data-block">
				<span><?php esc_html_e( 'Testing Date', 'truepharm' ); ?></span>
				<strong><?php echo esc_html( $tp_date ); ?></strong>
			</div>
			<div class="data-block">
				<span><?php esc_html_e( 'Verified Purity', 'truepharm' ); ?></span>
				<strong class="highlight"><?php echo esc_html( $tp_purity ?: '—' ); ?></strong>
			</div>
		</div>

		<!-- PDF Viewer -->
		<div class="pdf-viewer-wrap">
			<?php if ( $tp_pdf_url ) : ?>
				<iframe class="pdf-viewer" src="<?php echo esc_url( $tp_pdf_url ); ?>" title="<?php esc_attr_e( 'Certificate of Analysis PDF', 'truepharm' ); ?>" frameborder="0">
					<p><?php esc_html_e( 'Your browser does not support PDF viewing.', 'truepharm' ); ?> <a href="<?php echo esc_url( $tp_pdf_url ); ?>"><?php esc_html_e( 'Download the PDF to view it.', 'truepharm' ); ?></a></p>
				</iframe>
			<?php else : ?>
				<div class="pdf-missing">
					<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
					<h3><?php esc_html_e( 'PDF not yet available', 'truepharm' ); ?></h3>
					<p><?php esc_html_e( 'The laboratory report for this batch has not been uploaded yet. Please check back shortly.', 'truepharm' ); ?></p>
				</div>
			<?php endif; ?>
		</div>

		<!-- Download Bar -->
		<?php if ( $tp_pdf_url ) : ?>
			<div class="download-bar">
				<p><?php esc_html_e( 'Need a copy for your laboratory records?', 'truepharm' ); ?></p>
				<a href="<?php echo esc_url( $tp_pdf_url ); ?>" download class="btn-pdf btn-pdf-lg">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
					<?php esc_html_e( 'Download Official PDF', 'truepharm' ); ?>
				</a>
			</div>
		<?php endif; ?>

		<a class="coa-back" href="<?php echo esc_url( $tp_archive_url ); ?>">&larr; <?php esc_html_e( 'Return to COA Library', 'truepharm' ); ?></a>

	</section>

	<?php
endwhile;

get_footer();
