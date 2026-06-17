<?php
/**
 * WooCommerce custom product fields.
 *
 * - "Form" text field on the General tab.
 * - "Chemical Data" product tab (CAS, formula, weight, sequence, purity).
 * - "Storage & Handling" product tab (storage info).
 * - Global "Vial Size" (pa_vial_size) attribute for variable products.
 *
 * Molecular Class uses the native product_cat taxonomy (no custom field).
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Meta keys. */
const TP_PF_FORM    = '_tp_product_form';
const TP_PF_CAS     = '_tp_cas_number';
const TP_PF_FORMULA = '_tp_molecular_formula';
const TP_PF_WEIGHT  = '_tp_molecular_weight';
const TP_PF_SEQUENCE = '_tp_sequence';
const TP_PF_PURITY  = '_tp_purity';
const TP_PF_STORAGE = '_tp_storage_info';

/* ---------------------------------------------------------------------
 * Register product meta (native register_post_meta — no ACF).
 * ------------------------------------------------------------------- */
function tp_register_product_meta(): void {
	$can_edit = static function () {
		return current_user_can( 'edit_products' );
	};

	$text_keys = array( TP_PF_FORM, TP_PF_CAS, TP_PF_FORMULA, TP_PF_WEIGHT, TP_PF_PURITY );
	foreach ( $text_keys as $key ) {
		register_post_meta(
			'product',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => false,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $can_edit,
			)
		);
	}

	foreach ( array( TP_PF_SEQUENCE, TP_PF_STORAGE ) as $key ) {
		register_post_meta(
			'product',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => false,
				'sanitize_callback' => 'sanitize_textarea_field',
				'auth_callback'     => $can_edit,
			)
		);
	}
}
add_action( 'init', 'tp_register_product_meta' );

/* ---------------------------------------------------------------------
 * General tab — "Form" field.
 * ------------------------------------------------------------------- */
function tp_product_general_fields(): void {
	woocommerce_wp_text_input(
		array(
			'id'          => TP_PF_FORM,
			'label'       => __( 'Form', 'truepharm' ),
			'placeholder' => __( 'e.g. Lyophilized', 'truepharm' ),
			'desc_tip'    => true,
			'description' => __( 'Physical form of the compound (shown in product cards and specs).', 'truepharm' ),
		)
	);
}
add_action( 'woocommerce_product_options_general_product_data', 'tp_product_general_fields' );

/* ---------------------------------------------------------------------
 * Extra product data tabs (Chemical Data + Storage & Handling).
 * ------------------------------------------------------------------- */
function tp_product_data_tabs( array $tabs ): array {
	$tabs['tp_chem_data'] = array(
		'label'    => __( 'Chemical Data', 'truepharm' ),
		'target'   => 'tp_chem_data_panel',
		'class'    => array(),
		'priority' => 65,
	);
	$tabs['tp_storage'] = array(
		'label'    => __( 'Storage & Handling', 'truepharm' ),
		'target'   => 'tp_storage_panel',
		'class'    => array(),
		'priority' => 66,
	);
	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'tp_product_data_tabs' );

function tp_product_data_panels(): void {
	?>
	<div id="tp_chem_data_panel" class="panel woocommerce_options_panel">
		<?php
		woocommerce_wp_text_input(
			array(
				'id'    => TP_PF_CAS,
				'label' => __( 'CAS Number', 'truepharm' ),
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'    => TP_PF_FORMULA,
				'label' => __( 'Molecular Formula', 'truepharm' ),
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'    => TP_PF_WEIGHT,
				'label' => __( 'Molecular Weight', 'truepharm' ),
			)
		);
		woocommerce_wp_textarea_input(
			array(
				'id'    => TP_PF_SEQUENCE,
				'label' => __( 'Sequence', 'truepharm' ),
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'          => TP_PF_PURITY,
				'label'       => __( 'Purity (HPLC)', 'truepharm' ),
				'placeholder' => __( 'e.g. ≥ 99.0%', 'truepharm' ),
			)
		);
		?>
	</div>

	<div id="tp_storage_panel" class="panel woocommerce_options_panel">
		<?php
		woocommerce_wp_textarea_input(
			array(
				'id'          => TP_PF_STORAGE,
				'label'       => __( 'Storage Information', 'truepharm' ),
				'description' => __( 'Shown under the Storage &amp; Handling tab on the product page.', 'truepharm' ),
				'rows'        => 5,
			)
		);
		?>
	</div>
	<?php
}
add_action( 'woocommerce_product_data_panels', 'tp_product_data_panels' );

/* ---------------------------------------------------------------------
 * Save all custom product meta.
 * ------------------------------------------------------------------- */
function tp_save_product_meta( int $post_id ): void {
	// WooCommerce verifies woocommerce_meta_nonce before this hook; re-check defensively.
	if ( ! isset( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) {
		return;
	}

	$text_keys = array( TP_PF_FORM, TP_PF_CAS, TP_PF_FORMULA, TP_PF_WEIGHT, TP_PF_PURITY );
	foreach ( $text_keys as $key ) {
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		update_post_meta( $post_id, $key, $value );
	}

	foreach ( array( TP_PF_SEQUENCE, TP_PF_STORAGE ) as $key ) {
		$value = isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '';
		update_post_meta( $post_id, $key, $value );
	}
}
add_action( 'woocommerce_process_product_meta', 'tp_save_product_meta' );

/* ---------------------------------------------------------------------
 * Global "Vial Size" attribute (pa_vial_size) for variable products.
 * ------------------------------------------------------------------- */
function tp_register_vial_size_attribute(): void {
	if ( ! function_exists( 'wc_get_attribute_taxonomies' ) || ! function_exists( 'wc_create_attribute' ) ) {
		return;
	}

	foreach ( wc_get_attribute_taxonomies() as $tax ) {
		if ( 'vial_size' === $tax->attribute_name ) {
			update_option( 'tp_vial_attr_created', '1' );
			return; // Already exists.
		}
	}

	$result = wc_create_attribute(
		array(
			'name'         => __( 'Vial Size', 'truepharm' ),
			'slug'         => 'vial_size',
			'type'         => 'select',
			'order_by'     => 'menu_order',
			'has_archives' => false,
		)
	);

	if ( ! is_wp_error( $result ) ) {
		delete_transient( 'wc_attribute_taxonomies' );
		update_option( 'tp_vial_attr_created', '1' );
	}
}
add_action( 'after_switch_theme', 'tp_register_vial_size_attribute' );

/**
 * Self-heal: create the attribute once for installs already active.
 */
function tp_maybe_register_vial_size(): void {
	if ( is_admin() && '1' !== get_option( 'tp_vial_attr_created' ) ) {
		tp_register_vial_size_attribute();
	}
}
add_action( 'admin_init', 'tp_maybe_register_vial_size' );

/* ---------------------------------------------------------------------
 * Shared accessors used by the templates.
 * ------------------------------------------------------------------- */
/**
 * Product "Form" value, defaulting to "Lyophilized" when unset.
 */
function tp_get_product_form( int $product_id ): string {
	$form = (string) get_post_meta( $product_id, TP_PF_FORM, true );
	if ( '' === $form ) {
		$form = __( 'Lyophilized', 'truepharm' );
	}
	return $form;
}

/**
 * Molecular Class = name of the product's first product_cat term ('' if none).
 */
function tp_get_molecular_class( int $product_id ): string {
	$terms = get_the_terms( $product_id, 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		return $terms[0]->name;
	}
	return '';
}

/**
 * Render a product card (shared by the shop grid and related products).
 *
 * @param WC_Product $product Product object.
 * @param string     $button  'add' for an Add to Cart button, 'view' for a View Formula link.
 */
function tp_product_card( WC_Product $product, string $button = 'add' ): void {
	$pid       = $product->get_id();
	$permalink = get_permalink( $pid );
	$class     = tp_get_molecular_class( $pid );
	$form      = tp_get_product_form( $pid );
	$image_id  = $product->get_image_id();
	?>
	<div class="product-card">
		<a class="pimg<?php echo $image_id ? '' : ' ph-img'; ?>" href="<?php echo esc_url( $permalink ); ?>">
			<?php
			if ( $image_id ) {
				echo wp_get_attachment_image( $image_id, 'woocommerce_thumbnail' );
			} else {
				esc_html_e( 'PRODUCT IMAGE', 'truepharm' );
			}
			?>
		</a>
		<div class="product-info">
			<div class="specs"><span><?php echo esc_html( $class ); ?></span> <span><?php echo esc_html( $form ); ?></span></div>
			<h3><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
			<div class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			<?php
			if ( 'view' === $button ) {
				printf(
					'<a class="btn-add" href="%s">%s</a>',
					esc_url( $permalink ),
					esc_html__( 'View Formula', 'truepharm' )
				);
			} else {
				$add_classes = 'btn-add';
				$add_atts    = '';
				if ( $product->is_purchasable() && $product->is_in_stock() && ! $product->is_type( 'variable' ) ) {
					$add_classes .= ' ajax_add_to_cart add_to_cart_button product_type_' . $product->get_type();
					$add_atts     = sprintf( ' data-product_id="%d" data-quantity="1" rel="nofollow"', $pid );
				}
				printf(
					'<a href="%s" class="%s"%s>%s</a>',
					esc_url( $product->add_to_cart_url() ),
					esc_attr( $add_classes ),
					$add_atts, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static pre-escaped attributes.
					esc_html( $product->add_to_cart_text() )
				);
			}
			?>
		</div>
	</div>
	<?php
}
