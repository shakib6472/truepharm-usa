<?php
/**
 * Single product — TruePharm formulation detail page.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		continue;
	}

	$pid          = $product->get_id();
	$is_variable  = $product->is_type( 'variable' );
	$cat_terms    = get_the_terms( $pid, 'product_cat' );
	$cat_name     = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? $cat_terms[0]->name : '';
	$cat_link     = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? get_term_link( $cat_terms[0] ) : '';
	$shop_url     = wc_get_page_permalink( 'shop' );
	$main_img_id  = $product->get_image_id();
	$gallery_ids  = $product->get_gallery_image_ids();
	$thumb_ids    = array_slice( array_filter( array_merge( array( $main_img_id ), $gallery_ids ) ), 0, 4 );

	// Build variant pills (variable products) + capture the initial selection.
	$initial_variation_id = 0;
	$initial_price_html   = $product->get_price_html();
	$variation_pills      = '';

	if ( $is_variable ) {
		foreach ( $product->get_available_variations() as $variation ) {
			$vid   = (int) $variation['variation_id'];
			$label = '';
			foreach ( (array) $variation['attributes'] as $akey => $aval ) {
				$taxonomy = str_replace( 'attribute_', '', $akey );
				if ( $aval && taxonomy_exists( $taxonomy ) ) {
					$term  = get_term_by( 'slug', $aval, $taxonomy );
					$label = $term ? $term->name : $aval;
				} elseif ( $aval ) {
					$label = $aval;
				}
			}
			$price_html  = ! empty( $variation['price_html'] ) ? $variation['price_html'] : wc_price( $variation['display_price'] );
			$purchasable = ! empty( $variation['is_purchasable'] ) && ! empty( $variation['is_in_stock'] );
			$is_first    = ( 0 === $initial_variation_id && $purchasable );

			if ( $is_first ) {
				$initial_variation_id = $vid;
				$initial_price_html   = $price_html;
			}

			$variation_pills .= sprintf(
				'<button type="button" class="variant-btn%1$s" data-variation_id="%2$d" data-price="%3$s"%4$s>%5$s</button>',
				$is_first ? ' active' : '',
				$vid,
				esc_attr( $price_html ),
				$purchasable ? '' : ' disabled',
				esc_html( $label )
			);
		}
	}
	?>

	<section class="wrap" id="primary">

		<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'truepharm' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'truepharm' ); ?></a>
			<span>/</span>
			<a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Formulas', 'truepharm' ); ?></a>
			<?php if ( $cat_name && ! is_wp_error( $cat_link ) ) : ?>
				<span>/</span>
				<a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a>
			<?php endif; ?>
			<span>/</span>
			<?php echo esc_html( $product->get_name() ); ?>
		</nav>

		<?php woocommerce_output_all_notices(); ?>

		<div class="product-layout">

			<!-- Gallery -->
			<div class="product-gallery">
				<div class="main-image<?php echo $main_img_id ? '' : ' ph-img'; ?>">
					<?php
					if ( $main_img_id ) {
						echo wp_get_attachment_image( $main_img_id, 'large', false, array( 'id' => 'tp-main-image' ) );
					} else {
						esc_html_e( 'PRODUCT IMAGE - HIGH RES', 'truepharm' );
					}
					?>
					<div class="zoom-hint"><?php esc_html_e( 'Hover to Zoom', 'truepharm' ); ?></div>
				</div>
				<?php if ( count( $thumb_ids ) > 1 ) : ?>
					<div class="thumbnail-row">
						<?php foreach ( $thumb_ids as $index => $thumb_id ) : ?>
							<button type="button" class="thumbnail<?php echo 0 === $index ? ' active' : ''; ?>" data-large="<?php echo esc_url( wp_get_attachment_image_url( $thumb_id, 'large' ) ); ?>">
								<?php echo wp_get_attachment_image( $thumb_id, 'thumbnail' ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Details -->
			<div class="product-details"
				data-product_id="<?php echo esc_attr( (string) $pid ); ?>"
				data-type="<?php echo $is_variable ? 'variable' : 'simple'; ?>">

				<div class="compliance-badge"><?php esc_html_e( 'Research Only', 'truepharm' ); ?></div>
				<h1><?php echo esc_html( $product->get_name() ); ?></h1>
				<div class="sku-cat">
					<?php
					printf(
						/* translators: 1: SKU, 2: category. */
						esc_html__( 'SKU: %1$s | Category: %2$s', 'truepharm' ),
						esc_html( $product->get_sku() ? $product->get_sku() : __( 'N/A', 'truepharm' ) ),
						esc_html( $cat_name ? $cat_name : __( 'Uncategorized', 'truepharm' ) )
					);
					?>
				</div>

				<div class="price-box">
					<div class="price" id="tp-price"><?php echo wp_kses_post( $initial_price_html ); ?></div>
					<?php if ( $product->is_in_stock() ) : ?>
						<div class="stock-status"><?php esc_html_e( 'In Stock & Ready to Ship', 'truepharm' ); ?></div>
					<?php else : ?>
						<div class="stock-status out"><?php esc_html_e( 'Out of Stock', 'truepharm' ); ?></div>
					<?php endif; ?>
				</div>

				<?php if ( $product->get_short_description() ) : ?>
					<div class="short-desc"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
				<?php endif; ?>

				<?php if ( $is_variable && $variation_pills ) : ?>
					<div class="variant-group">
						<h4><?php esc_html_e( 'Select Vial Size:', 'truepharm' ); ?></h4>
						<div class="variant-options" id="tp-variants">
							<?php echo $variation_pills; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- assembled from esc_* above. ?>
						</div>
					</div>
				<?php endif; ?>

				<input type="hidden" id="tp-variation-id" value="<?php echo esc_attr( (string) $initial_variation_id ); ?>">

				<!-- Add to Cart (BLUE METALLIC) -->
				<div class="add-to-cart-wrap">
					<div class="qty-input">
						<button type="button" class="qty-btn" data-step="down" aria-label="<?php esc_attr_e( 'Decrease quantity', 'truepharm' ); ?>">-</button>
						<input type="number" id="tp-qty" min="1" max="99" value="1" aria-label="<?php esc_attr_e( 'Quantity', 'truepharm' ); ?>">
						<button type="button" class="qty-btn" data-step="up" aria-label="<?php esc_attr_e( 'Increase quantity', 'truepharm' ); ?>">+</button>
					</div>
					<button class="btn-buy" id="tp-add-to-cart" type="button">
						<span class="metallic-text"><?php esc_html_e( 'Add to Cart', 'truepharm' ); ?></span>
					</button>
				</div>

				<!-- Payment Banner -->
				<div class="payment-banner">
					<div class="payment-icons"><span>&#63743;</span> <span>&#9410;</span></div>
					<?php esc_html_e( 'We accept Apple Pay & Google Pay at checkout', 'truepharm' ); ?>
				</div>

				<!-- Bundle Box -->
				<div class="bundle-box">
					<div class="bundle-header">
						<span>&#127873; <?php esc_html_e( 'Bundle & Save', 'truepharm' ); ?></span>
					</div>
					<div class="bundle-options" id="tp-bundle">
						<div class="bundle-opt" data-qty="3">
							<div class="bundle-title"><input type="radio" name="bundle" style="pointer-events:none;"> <?php esc_html_e( '3-Pack', 'truepharm' ); ?></div>
							<div class="bundle-desc"><?php esc_html_e( 'Save 5%', 'truepharm' ); ?></div>
						</div>
						<div class="bundle-opt" data-qty="5">
							<div class="bundle-title"><input type="radio" name="bundle" style="pointer-events:none;"> <?php esc_html_e( '5-Pack', 'truepharm' ); ?></div>
							<div class="bundle-desc"><?php esc_html_e( 'Save 10%', 'truepharm' ); ?></div>
						</div>
						<div class="bundle-opt active" data-qty="10">
							<div class="best-value-badge"><?php esc_html_e( 'Best Value', 'truepharm' ); ?></div>
							<div class="bundle-title"><input type="radio" name="bundle" checked style="pointer-events:none;"> <?php esc_html_e( '10-Pack', 'truepharm' ); ?></div>
							<div class="bundle-desc"><?php esc_html_e( 'Save 20%', 'truepharm' ); ?></div>
						</div>
					</div>
					<button class="btn-bundle" id="tp-add-bundle" type="button">
						<span class="metallic-text-warm"><?php esc_html_e( 'Add Bundle to Cart', 'truepharm' ); ?></span>
					</button>
				</div>

				<!-- Guarantees -->
				<div class="product-guarantees">
					<ul>
						<li><strong><?php esc_html_e( 'Quality Assured:', 'truepharm' ); ?></strong> <?php esc_html_e( '>99% Purity Verified', 'truepharm' ); ?></li>
						<li><strong><?php esc_html_e( 'Traceability:', 'truepharm' ); ?></strong> <?php esc_html_e( 'Batch-specific COA available', 'truepharm' ); ?></li>
						<li><strong><?php esc_html_e( 'Logistics:', 'truepharm' ); ?></strong> <?php esc_html_e( 'Climate-controlled dispatch', 'truepharm' ); ?></li>
					</ul>
				</div>

				<!-- Warning -->
				<div class="warning-box">
					<strong><?php esc_html_e( 'Warning:', 'truepharm' ); ?></strong> <?php esc_html_e( 'For in-vitro research use only. Not for human or veterinary use.', 'truepharm' ); ?>
				</div>

			</div>
		</div>
	</section>

	<!-- Product Tabs -->
	<section class="product-tabs-section">
		<div class="wrap">

			<div class="tab-buttons" role="tablist">
				<button class="tab-btn active" data-tab="tab-desc" type="button"><?php esc_html_e( 'Compound Overview', 'truepharm' ); ?></button>
				<button class="tab-btn" data-tab="tab-specs" type="button"><?php esc_html_e( 'Chemical Specifications', 'truepharm' ); ?></button>
				<button class="tab-btn" data-tab="tab-storage" type="button"><?php esc_html_e( 'Storage & Handling', 'truepharm' ); ?></button>
				<button class="tab-btn" data-tab="tab-coa" type="button"><?php esc_html_e( 'Certificate of Analysis', 'truepharm' ); ?></button>
			</div>

			<!-- Tab 1: Compound Overview -->
			<div id="tab-desc" class="tab-panel active">
				<?php
				the_content();
				if ( ! get_the_content() ) {
					echo '<p style="color:var(--slate-soft);">' . esc_html__( 'A detailed research overview for this compound is coming soon.', 'truepharm' ) . '</p>';
				}
				?>
			</div>

			<!-- Tab 2: Chemical Specifications -->
			<div id="tab-specs" class="tab-panel">
				<?php
				$specs = array(
					__( 'Compound Name', 'truepharm' )    => $product->get_name(),
					__( 'Sequence', 'truepharm' )         => get_post_meta( $pid, TP_PF_SEQUENCE, true ),
					__( 'Molecular Formula', 'truepharm' ) => get_post_meta( $pid, TP_PF_FORMULA, true ),
					__( 'Molecular Weight', 'truepharm' )  => get_post_meta( $pid, TP_PF_WEIGHT, true ),
					__( 'CAS Number', 'truepharm' )        => get_post_meta( $pid, TP_PF_CAS, true ),
					__( 'Form', 'truepharm' )              => tp_get_product_form( $pid ),
					__( 'Purity (HPLC)', 'truepharm' )     => get_post_meta( $pid, TP_PF_PURITY, true ),
				);
				$has_specs = false;
				foreach ( $specs as $value ) {
					if ( '' !== trim( (string) $value ) ) {
						$has_specs = true;
						break;
					}
				}
				if ( $has_specs ) :
					?>
					<table class="chem-table">
						<tbody>
							<?php foreach ( $specs as $label => $value ) : ?>
								<?php if ( '' !== trim( (string) $value ) ) : ?>
									<tr>
										<th><?php echo esc_html( $label ); ?></th>
										<td><?php echo esc_html( $value ); ?></td>
									</tr>
								<?php endif; ?>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<p style="color:var(--slate-soft);"><?php esc_html_e( 'Chemical specifications for this compound are being finalised.', 'truepharm' ); ?></p>
				<?php endif; ?>
			</div>

			<!-- Tab 3: Storage & Handling -->
			<div id="tab-storage" class="tab-panel">
				<h3 style="margin-bottom:14px; color:var(--navy);"><?php esc_html_e( 'Laboratory Storage Guidelines', 'truepharm' ); ?></h3>
				<?php
				$storage = (string) get_post_meta( $pid, TP_PF_STORAGE, true );
				if ( '' !== trim( $storage ) ) {
					echo wp_kses_post( wpautop( $storage ) );
				} else {
					echo '<p style="color:var(--slate-soft);">' . esc_html__( 'Store desiccated at -20°C. Avoid repeated freeze-thaw cycles and direct light exposure. Detailed handling notes coming soon.', 'truepharm' ) . '</p>';
				}
				?>
			</div>

			<!-- Tab 4: Certificate of Analysis -->
			<div id="tab-coa" class="tab-panel">
				<h3 style="margin-bottom:14px; color:var(--navy);"><?php esc_html_e( 'Batch Traceability & Reports', 'truepharm' ); ?></h3>
				<?php
				// COA compound name is the COA post title (Phase 2 design).
				$coa_query = new WP_Query(
					array(
						'post_type'      => 'coa_library',
						'title'          => $product->get_name(),
						'posts_per_page' => 1,
						'no_found_rows'  => true,
						'fields'         => 'ids',
					)
				);
				if ( ! empty( $coa_query->posts ) ) :
					$coa_id = (int) $coa_query->posts[0];
					?>
					<p style="color:var(--slate-soft); max-width:800px; margin-bottom:24px;"><?php esc_html_e( 'TruePharm USA guarantees that every synthesized batch is verified by an independent laboratory. View the mass spectrometry and HPLC data for the current batch below.', 'truepharm' ); ?></p>
					<a href="<?php echo esc_url( get_permalink( $coa_id ) ); ?>" class="btn-rosegold"><?php esc_html_e( 'View Current Batch COA', 'truepharm' ); ?></a>
				<?php else : ?>
					<p style="color:var(--slate-soft); max-width:800px;"><?php esc_html_e( 'COA coming soon — the laboratory report for this compound is being finalised.', 'truepharm' ); ?></p>
				<?php endif; ?>
			</div>

		</div>
	</section>

	<!-- Related Products -->
	<?php
	$related_ids = wc_get_related_products( $pid, 4 );
	if ( ! empty( $related_ids ) ) :
		?>
		<section class="related-products wrap">
			<h2><?php esc_html_e( 'Related Formulas', 'truepharm' ); ?></h2>
			<div class="shop-grid">
				<?php
				foreach ( $related_ids as $related_id ) {
					$related_product = wc_get_product( $related_id );
					if ( $related_product ) {
						tp_product_card( $related_product, 'view' );
					}
				}
				?>
			</div>
		</section>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
