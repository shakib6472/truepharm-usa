<?php
/**
 * The homepage template.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$tp_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$tp_coa_url  = get_post_type_archive_link( 'coa_library' ) ?: home_url( '/coa-library/' );

$tp_hero_mission = get_theme_mod(
	'truepharm_hero_mission',
	__( 'High-purity lyophilized peptides, bioregulators, and precise cellular modulators engineered strictly for rigorous laboratory and in-vitro research applications.', 'truepharm' )
);
?>

<!-- 1. HERO -->
<header class="hero">
	<div class="hero-anim" aria-hidden="true">
		<div class="glow"></div>
		<div class="base-ring b1"></div>
		<div class="base-ring b2"></div>
		<div class="base-ring b3"></div>
		<div class="ring r1"></div>
		<div class="ring r2"></div>
		<div class="ring r3"></div>
		<div class="ring r4"></div>
	</div>
	<div class="hero-content" id="primary">
		<div class="hero-logo">
			<?php
			if ( has_custom_logo() ) {
				echo get_custom_logo();
			} else {
				echo '<span class="logo-text" style="font-size:2rem;">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
			}
			?>
		</div>
		<p class="hero-mission"><?php echo esc_html( $tp_hero_mission ); ?></p>
		<div class="hero-cta">
			<a href="<?php echo esc_url( $tp_shop_url ); ?>" class="btn btn-cart"><?php esc_html_e( 'View Research Catalog', 'truepharm' ); ?></a>
		</div>
	</div>
</header>

<!-- 2. CREDENTIALS -->
<section class="creds reveal" id="credentials">
	<div class="wrap creds-grid">
		<div class="cred"><div class="cred-ic">&#10003;</div><h4><?php esc_html_e( 'Third-party tested', 'truepharm' ); ?></h4><p><?php esc_html_e( 'Every batch is verified through independent labs.', 'truepharm' ); ?></p></div>
		<div class="cred"><div class="cred-ic">&#10022;</div><h4><?php esc_html_e( 'Highest Purity', 'truepharm' ); ?></h4><p><?php esc_html_e( 'Quality assurance to achieve 99% or greater purity.', 'truepharm' ); ?></p></div>
		<div class="cred"><div class="cred-ic">&#10052;</div><h4><?php esc_html_e( 'Cold Chain Logistics', 'truepharm' ); ?></h4><p><?php esc_html_e( 'Preserved molecular stability', 'truepharm' ); ?></p></div>
		<div class="cred">
			<div class="cred-ic">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
			</div>
			<h4><?php esc_html_e( 'Batch Traceability', 'truepharm' ); ?></h4><p><?php esc_html_e( 'Transparent COA documentation', 'truepharm' ); ?></p>
		</div>
	</div>
</section>

<!-- 3. CATEGORIES -->
<section class="block sky-bg reveal" id="categories">
	<div class="wrap">
		<div class="section-head"><div><span class="eyebrow"><?php esc_html_e( 'Compound Directory', 'truepharm' ); ?></span><h2><?php esc_html_e( 'Shop by Molecular Class', 'truepharm' ); ?></h2></div></div>
		<div class="cat-grid">
			<?php
			$tp_categories = array(
				'modulators'    => __( 'Modulators', 'truepharm' ),
				'bioregulators' => __( 'Bioregulators', 'truepharm' ),
				'neuroactives'  => __( 'Neuroactives', 'truepharm' ),
				'amino'         => __( 'Amino', 'truepharm' ),
				'peptides'      => __( 'Peptides', 'truepharm' ),
			);

			foreach ( $tp_categories as $tp_slug => $tp_label ) {
				$tp_term = taxonomy_exists( 'product_cat' ) ? get_term_by( 'slug', $tp_slug, 'product_cat' ) : false;
				$tp_link = $tp_shop_url;
				$tp_bg   = '';

				if ( $tp_term && ! is_wp_error( $tp_term ) ) {
					$tp_term_link = get_term_link( $tp_term );
					if ( ! is_wp_error( $tp_term_link ) ) {
						$tp_link = $tp_term_link;
					}
					$tp_thumb_id = (int) get_term_meta( $tp_term->term_id, 'thumbnail_id', true );
					if ( $tp_thumb_id ) {
						$tp_bg = wp_get_attachment_image_url( $tp_thumb_id, 'large' );
					}
					$tp_label = $tp_term->name;
				}
				?>
				<a class="cat-tile" href="<?php echo esc_url( $tp_link ); ?>">
					<?php if ( $tp_bg ) : ?>
						<div class="cat-bg" style="background-image:url('<?php echo esc_url( $tp_bg ); ?>');"></div>
					<?php else : ?>
						<div class="ph-img cat-bg"><?php echo esc_html( strtoupper( $tp_label ) ); ?></div>
					<?php endif; ?>
					<div class="cat-label"><?php echo esc_html( $tp_label ); ?></div>
				</a>
				<?php
			}
			?>
		</div>
	</div>
</section>

<!-- 4. FEATURED PRODUCTS CAROUSEL -->
<section class="block sky-bg reveal" id="shop">
	<div class="wrap">
		<div class="section-head">
			<div><span class="eyebrow"><?php esc_html_e( 'Validated Compounds', 'truepharm' ); ?></span><h2><?php esc_html_e( 'Featured Formulations', 'truepharm' ); ?></h2></div>
			<a href="<?php echo esc_url( $tp_shop_url ); ?>"><?php esc_html_e( 'View Full Catalog', 'truepharm' ); ?> &rarr;</a>
		</div>

		<?php
		$tp_products = array();
		if ( function_exists( 'wc_get_products' ) ) {
			$tp_products = wc_get_products(
				array(
					'status'     => 'publish',
					'limit'      => 6,
					'orderby'    => 'date',
					'order'      => 'DESC',
					'visibility' => 'catalog',
				)
			);
		}

		if ( ! empty( $tp_products ) ) :
			?>
			<div class="carousel-wrap">
				<button class="carousel-btn prev" id="carPrev" aria-label="<?php esc_attr_e( 'Previous', 'truepharm' ); ?>">&#8249;</button>

				<div class="product-grid" id="carousel">
					<?php
					foreach ( $tp_products as $tp_product ) {
						$tp_pid = $tp_product->get_id();

						// Molecular Class = primary product category; Form = product meta.
						$tp_class_name = function_exists( 'tp_get_molecular_class' ) ? tp_get_molecular_class( $tp_pid ) : '';
						$tp_form       = function_exists( 'tp_get_product_form' ) ? tp_get_product_form( $tp_pid ) : __( 'Lyophilized', 'truepharm' );

						$tp_add_classes = 'btn btn-add';
						$tp_add_atts    = '';
						if ( $tp_product->is_purchasable() && $tp_product->is_in_stock() && ! $tp_product->is_type( 'variable' ) ) {
							$tp_add_classes .= ' ajax_add_to_cart add_to_cart_button product_type_' . $tp_product->get_type();
							$tp_add_atts     = sprintf( ' data-product_id="%d" data-quantity="1" rel="nofollow"', $tp_pid );
						}
						?>
						<div class="product-card">
							<div class="compliance-badge"><?php esc_html_e( 'Research Only', 'truepharm' ); ?></div>
							<a href="<?php echo esc_url( get_permalink( $tp_pid ) ); ?>" class="pimg<?php echo $tp_product->get_image_id() ? '' : ' ph-img'; ?>">
								<?php
								if ( $tp_product->get_image_id() ) {
									echo wp_get_attachment_image( $tp_product->get_image_id(), 'woocommerce_thumbnail' );
								} else {
									esc_html_e( 'PRODUCT IMAGE', 'truepharm' );
								}
								?>
							</a>
							<div class="product-info">
								<div class="specs"><span><?php echo esc_html( $tp_class_name ); ?></span> <span><?php echo esc_html( $tp_form ); ?></span></div>
								<h3><a href="<?php echo esc_url( get_permalink( $tp_pid ) ); ?>"><?php echo esc_html( $tp_product->get_name() ); ?></a></h3>
								<div class="price"><?php echo wp_kses_post( $tp_product->get_price_html() ); ?></div>
								<a href="<?php echo esc_url( $tp_product->add_to_cart_url() ); ?>" class="<?php echo esc_attr( $tp_add_classes ); ?>"<?php echo $tp_add_atts; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, pre-escaped attributes. ?>><?php echo esc_html( $tp_product->add_to_cart_text() ); ?></a>
							</div>
						</div>
						<?php
					}
					?>
				</div>

				<button class="carousel-btn next" id="carNext" aria-label="<?php esc_attr_e( 'Next', 'truepharm' ); ?>">&#8250;</button>
			</div>
			<p class="swipe-hint">&#8592; <?php esc_html_e( 'swipe to see more', 'truepharm' ); ?> &#8594;</p>
		<?php else : ?>
			<div class="coa-empty">
				<h3><?php esc_html_e( 'Catalog coming soon', 'truepharm' ); ?></h3>
				<p><?php esc_html_e( 'Featured compounds will appear here once products are published.', 'truepharm' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>

<!-- 5. LAB GOALS -->
<section class="block goals blush-bg reveal" id="goals">
	<div class="wrap goals-grid">
		<?php
		$tp_goals_img = get_theme_mod( 'truepharm_goals_image' );
		if ( $tp_goals_img ) :
			?>
			<div class="goals-img"><?php echo wp_get_attachment_image( (int) $tp_goals_img, 'large' ); ?></div>
		<?php else : ?>
			<div class="ph-img goals-img"><?php esc_html_e( 'CLINICAL LABORATORY IMAGE', 'truepharm' ); ?></div>
		<?php endif; ?>
		<div>
			<span class="eyebrow"><?php esc_html_e( 'Scientific Rigor', 'truepharm' ); ?></span>
			<h2><?php echo esc_html( get_theme_mod( 'truepharm_goals_title', __( 'Pioneering Cellular Integrity', 'truepharm' ) ) ); ?></h2>
			<p><?php echo esc_html( get_theme_mod( 'truepharm_goals_p1', __( 'TruePharm USA was established to bridge the gap between high-level laboratory research and clinical-grade, third-party verified cellular compounds. We prioritize absolute compound identity, strict chain of custody, and molecular stability above all else.', 'truepharm' ) ) ); ?></p>
			<p><?php echo esc_html( get_theme_mod( 'truepharm_goals_p2', __( 'Through empirical vetting and rigorous batch protocols, we provide independent researchers and institutions with reliable, data-backed compounds engineered for absolute precision.', 'truepharm' ) ) ); ?></p>
			<a href="<?php echo esc_url( $tp_coa_url ); ?>" class="btn btn-ghost" style="margin-top:12px"><?php esc_html_e( 'Read Our Quality Manifesto', 'truepharm' ); ?></a>
		</div>
	</div>
</section>

<!-- 6. REWARDS PROGRAM -->
<section class="block rewards blush-bg reveal" id="rewards">
	<div class="wrap">
		<span class="eyebrow"><?php esc_html_e( 'TruePharm Rewards', 'truepharm' ); ?></span>
		<h2><?php esc_html_e( 'Earn points, get rewarded', 'truepharm' ); ?></h2>
		<p><?php esc_html_e( 'Join free and earn points on every order — redeem for discounts and perks.', 'truepharm' ); ?></p>
		<?php
		$tp_bonus      = tp_rewards_signup_bonus();
		$tp_per_dollar = tp_rewards_points_per_dollar();
		$tp_redeem_pts = tp_rewards_redeem_points();
		$tp_redeem_val = tp_rewards_redeem_value_display();
		?>
		<div class="reward-steps">
			<div class="reward-step">
				<div class="rnum">1</div>
				<h4><?php esc_html_e( 'Create an account', 'truepharm' ); ?></h4>
				<p>
				<?php
				/* translators: %s: number of signup bonus points. */
				printf( esc_html__( 'Sign up free and instantly earn %s bonus points.', 'truepharm' ), esc_html( number_format_i18n( $tp_bonus ) ) );
				?>
				</p>
			</div>
			<div class="reward-step">
				<div class="rnum">2</div>
				<h4><?php esc_html_e( 'Earn points', 'truepharm' ); ?></h4>
				<p>
				<?php
				/* translators: %s: points earned per dollar. */
				printf( esc_html( _n( 'Get %s point for every $1 spent, plus points for reviews and referrals.', 'Get %s points for every $1 spent, plus points for reviews and referrals.', $tp_per_dollar, 'truepharm' ) ), esc_html( number_format_i18n( $tp_per_dollar ) ) );
				?>
				</p>
			</div>
			<div class="reward-step">
				<div class="rnum">3</div>
				<h4><?php esc_html_e( 'Redeem rewards', 'truepharm' ); ?></h4>
				<p>
				<?php
				/* translators: 1: points required, 2: dollar value. */
				printf( esc_html__( 'Turn %1$s points into %2$s off future orders.', 'truepharm' ), esc_html( number_format_i18n( $tp_redeem_pts ) ), esc_html( $tp_redeem_val ) );
				?>
				</p>
			</div>
		</div>
	</div>
</section>

<!-- 7. NEWSLETTER -->
<section class="news reveal">
	<div class="wrap">
		<div class="news-box">
			<h2><?php printf( /* translators: %s: discount, e.g. 10%% off. */ esc_html__( 'Sign up &amp; get %s your first order', 'truepharm' ), '<span class="big">' . esc_html__( '10% off', 'truepharm' ) . '</span>' ); ?></h2>
			<p><?php esc_html_e( 'Join for new drops, restock alerts, and members-only deals.', 'truepharm' ); ?></p>
			<form class="news-form" id="tp-news-form" novalidate>
				<input type="email" id="tp-news-email" name="email" placeholder="<?php esc_attr_e( 'Enter your email', 'truepharm' ); ?>" aria-label="<?php esc_attr_e( 'Email', 'truepharm' ); ?>" required>
				<button class="btn" type="submit"><?php esc_html_e( 'Get 10% off', 'truepharm' ); ?></button>
			</form>
			<p class="news-msg" id="tp-news-msg" role="status" aria-live="polite"></p>
		</div>
	</div>
</section>

<?php
get_footer();
