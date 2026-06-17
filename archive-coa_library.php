<?php
/**
 * COA Library archive — searchable certificate table.
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
		<h1><?php esc_html_e( 'Certificate of Analysis Library', 'truepharm' ); ?></h1>
		<p><?php esc_html_e( 'We prioritize absolute transparency. Every compound synthesized for TruePharm USA undergoes rigorous third-party testing to guarantee purity, identity, and stability.', 'truepharm' ); ?></p>
	</div>
</header>

<section class="wrap" id="primary">
	<div class="education-panel">
		<div class="edu-card">
			<h3><span>1</span> <?php esc_html_e( 'HPLC (High-Performance Liquid Chromatography)', 'truepharm' ); ?></h3>
			<p><?php printf( /* translators: %s: emphasised "Purity". */ esc_html__( 'HPLC testing verifies the %s of the compound. It separates the peptide from any residual synthetic byproducts or impurities, ensuring your batch meets our strict >99%% purity standard for precise in-vitro research.', 'truepharm' ), '<strong>' . esc_html__( 'Purity', 'truepharm' ) . '</strong>' ); ?></p>
		</div>
		<div class="edu-card">
			<h3><span>2</span> <?php esc_html_e( 'Mass Spectrometry (MS)', 'truepharm' ); ?></h3>
			<p><?php printf( /* translators: %s: emphasised "Identity". */ esc_html__( 'Mass Spectrometry verifies the %s of the compound. By measuring the molecular weight of the sample, we confirm that the exact amino acid sequence of the peptide matches the intended formulation without structural degradation.', 'truepharm' ), '<strong>' . esc_html__( 'Identity', 'truepharm' ) . '</strong>' ); ?></p>
		</div>
	</div>
</section>

<section class="wrap">

	<div class="coa-controls">
		<label class="search-label" for="batch-search"><?php esc_html_e( 'Verify Your Specific Batch', 'truepharm' ); ?></label>
		<form class="coa-search-box" role="search" onsubmit="return false;">
			<input type="text" id="batch-search" placeholder="<?php esc_attr_e( 'Enter your Batch or Lot # (e.g., TP-8842)...', 'truepharm' ); ?>" aria-label="<?php esc_attr_e( 'Search certificates', 'truepharm' ); ?>">
			<button type="submit"><?php esc_html_e( 'Search', 'truepharm' ); ?></button>
		</form>
		<p style="font-size:0.9rem; color:var(--slate-soft); margin-top:16px;">
			<?php esc_html_e( "Can't find your batch?", 'truepharm' ); ?>
			<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" style="color:var(--navy); font-weight:600;"><?php esc_html_e( 'Contact our lab support team.', 'truepharm' ); ?></a>
		</p>
	</div>

	<?php if ( have_posts() ) : ?>

		<div class="table-wrapper">
			<table class="coa-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Compound Name', 'truepharm' ); ?></th>
						<th><?php esc_html_e( 'Batch Number', 'truepharm' ); ?></th>
						<th><?php esc_html_e( 'Testing Date', 'truepharm' ); ?></th>
						<th><?php esc_html_e( 'Verified Purity', 'truepharm' ); ?></th>
						<th><?php esc_html_e( 'Full Report', 'truepharm' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					while ( have_posts() ) :
						the_post();

						$tp_compound = get_the_title();
						$tp_batch    = (string) get_post_meta( get_the_ID(), TP_COA_BATCH, true );
						$tp_date_raw = (string) get_post_meta( get_the_ID(), TP_COA_DATE, true );
						$tp_purity   = (string) get_post_meta( get_the_ID(), TP_COA_PURITY, true );
						$tp_date     = $tp_date_raw ? date_i18n( get_option( 'date_format' ), strtotime( $tp_date_raw ) ) : '—';
						?>
						<tr data-compound="<?php echo esc_attr( $tp_compound ); ?>" data-batch="<?php echo esc_attr( $tp_batch ); ?>">
							<td><strong><?php echo esc_html( $tp_compound ); ?></strong></td>
							<td><?php echo $tp_batch ? '<span class="batch-tag">' . esc_html( $tp_batch ) . '</span>' : '&mdash;'; ?></td>
							<td><?php echo esc_html( $tp_date ); ?></td>
							<td class="purity-tag"><?php echo $tp_purity ? esc_html( $tp_purity ) : '&mdash;'; ?></td>
							<td><a href="<?php the_permalink(); ?>" class="btn-pdf"><?php esc_html_e( 'View COA', 'truepharm' ); ?> &rarr;</a></td>
						</tr>
						<?php
					endwhile;
					?>
				</tbody>
			</table>
			<div class="coa-noresults" id="coa-noresults"></div>
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

		<div class="coa-empty">
			<h3><?php esc_html_e( 'No certificates published yet', 'truepharm' ); ?></h3>
			<p><?php esc_html_e( 'Certificates of Analysis will appear here as batches are verified. Please check back soon.', 'truepharm' ); ?></p>
		</div>

	<?php endif; ?>

</section>

<?php
get_footer();
