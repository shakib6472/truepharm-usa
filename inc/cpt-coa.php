<?php
/**
 * COA Library — custom post type, meta fields, meta box, and admin columns.
 *
 * No ACF: registration uses register_post_type(), register_post_meta(),
 * and add_meta_box() only.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Meta keys for the COA fields. */
const TP_COA_BATCH   = '_coa_batch_number';
const TP_COA_DATE    = '_coa_testing_date';
const TP_COA_PURITY  = '_coa_verified_purity';
const TP_COA_PDF     = '_coa_pdf_file';

/* ---------------------------------------------------------------------
 * Register the post type.
 * ------------------------------------------------------------------- */
function tp_register_coa_cpt(): void {
	$labels = array(
		'name'                  => _x( 'COA Library', 'Post type general name', 'truepharm' ),
		'singular_name'         => _x( 'Certificate of Analysis', 'Post type singular name', 'truepharm' ),
		'menu_name'             => __( 'COA Library', 'truepharm' ),
		'name_admin_bar'        => __( 'Certificate of Analysis', 'truepharm' ),
		'add_new'               => __( 'Add New', 'truepharm' ),
		'add_new_item'          => __( 'Add New Certificate of Analysis', 'truepharm' ),
		'new_item'              => __( 'New Certificate of Analysis', 'truepharm' ),
		'edit_item'             => __( 'Edit Certificate of Analysis', 'truepharm' ),
		'view_item'             => __( 'View Certificate of Analysis', 'truepharm' ),
		'all_items'             => __( 'All Certificates', 'truepharm' ),
		'search_items'          => __( 'Search Certificates', 'truepharm' ),
		'not_found'             => __( 'No certificates found.', 'truepharm' ),
		'not_found_in_trash'    => __( 'No certificates found in Trash.', 'truepharm' ),
		'archives'              => __( 'COA Library', 'truepharm' ),
		'item_published'        => __( 'Certificate published.', 'truepharm' ),
		'item_updated'          => __( 'Certificate updated.', 'truepharm' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => false,
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 26,
		'menu_icon'          => 'dashicons-clipboard',
		'rewrite'            => array(
			'slug'       => 'coa-library',
			'with_front' => false,
		),
		'capability_type'    => 'post',
		'supports'           => array( 'title' ),
	);

	register_post_type( 'coa_library', $args );
}
add_action( 'init', 'tp_register_coa_cpt' );

/* ---------------------------------------------------------------------
 * Register meta (native, no ACF).
 * ------------------------------------------------------------------- */
function tp_register_coa_meta(): void {
	$can_edit = static function () {
		return current_user_can( 'edit_posts' );
	};

	register_post_meta(
		'coa_library',
		TP_COA_BATCH,
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => false,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $can_edit,
		)
	);

	register_post_meta(
		'coa_library',
		TP_COA_DATE,
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => false,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $can_edit,
		)
	);

	register_post_meta(
		'coa_library',
		TP_COA_PURITY,
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => false,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $can_edit,
		)
	);

	register_post_meta(
		'coa_library',
		TP_COA_PDF,
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => false,
			'sanitize_callback' => 'absint',
			'auth_callback'     => $can_edit,
		)
	);
}
add_action( 'init', 'tp_register_coa_meta' );

/* ---------------------------------------------------------------------
 * Meta box.
 * ------------------------------------------------------------------- */
function tp_add_coa_meta_box(): void {
	add_meta_box(
		'tp_coa_details',
		__( 'COA Details', 'truepharm' ),
		'tp_render_coa_meta_box',
		'coa_library',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'tp_add_coa_meta_box' );

function tp_render_coa_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'tp_save_coa_meta', 'tp_coa_nonce' );

	$batch   = (string) get_post_meta( $post->ID, TP_COA_BATCH, true );
	$date    = (string) get_post_meta( $post->ID, TP_COA_DATE, true );
	$purity  = (string) get_post_meta( $post->ID, TP_COA_PURITY, true );
	$pdf_id  = (int) get_post_meta( $post->ID, TP_COA_PDF, true );
	$pdf_url = $pdf_id ? wp_get_attachment_url( $pdf_id ) : '';
	$pdf_name = $pdf_id ? get_the_title( $pdf_id ) : '';
	?>
	<style>
		.tp-coa-field{margin-bottom:18px;}
		.tp-coa-field label{display:block;font-weight:600;margin-bottom:6px;}
		.tp-coa-field input[type=text],.tp-coa-field input[type=date]{width:100%;max-width:420px;}
		.tp-coa-pdf-current{margin-top:8px;}
		.tp-coa-pdf-current.is-empty{display:none;}
	</style>

	<div class="tp-coa-field">
		<label for="tp_coa_batch"><?php esc_html_e( 'Batch Number', 'truepharm' ); ?></label>
		<input type="text" id="tp_coa_batch" name="tp_coa_batch" value="<?php echo esc_attr( $batch ); ?>" placeholder="TP-8842">
	</div>

	<div class="tp-coa-field">
		<label for="tp_coa_date"><?php esc_html_e( 'Testing Date', 'truepharm' ); ?></label>
		<input type="date" id="tp_coa_date" name="tp_coa_date" value="<?php echo esc_attr( $date ); ?>">
	</div>

	<div class="tp-coa-field">
		<label for="tp_coa_purity"><?php esc_html_e( 'Verified Purity', 'truepharm' ); ?></label>
		<input type="text" id="tp_coa_purity" name="tp_coa_purity" value="<?php echo esc_attr( $purity ); ?>" placeholder="99.2%">
	</div>

	<div class="tp-coa-field">
		<label><?php esc_html_e( 'PDF File', 'truepharm' ); ?></label>
		<input type="hidden" id="tp_coa_pdf" name="tp_coa_pdf" value="<?php echo esc_attr( (string) $pdf_id ); ?>">
		<button type="button" class="button" id="tp_coa_pdf_select"><?php esc_html_e( 'Select / Upload PDF', 'truepharm' ); ?></button>
		<div class="tp-coa-pdf-current<?php echo $pdf_id ? '' : ' is-empty'; ?>" id="tp_coa_pdf_current">
			<a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener" id="tp_coa_pdf_link"><?php echo esc_html( $pdf_name ); ?></a>
			&nbsp;&mdash;&nbsp;
			<a href="#" id="tp_coa_pdf_remove"><?php esc_html_e( 'Remove', 'truepharm' ); ?></a>
		</div>
	</div>
	<?php
}

/* ---------------------------------------------------------------------
 * Save handler.
 * ------------------------------------------------------------------- */
function tp_save_coa_meta( int $post_id ): void {
	if ( ! isset( $_POST['tp_coa_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tp_coa_nonce'] ) ), 'tp_save_coa_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( 'coa_library' !== get_post_type( $post_id ) ) {
		return;
	}

	$batch  = isset( $_POST['tp_coa_batch'] ) ? sanitize_text_field( wp_unslash( $_POST['tp_coa_batch'] ) ) : '';
	$date   = isset( $_POST['tp_coa_date'] ) ? sanitize_text_field( wp_unslash( $_POST['tp_coa_date'] ) ) : '';
	$purity = isset( $_POST['tp_coa_purity'] ) ? sanitize_text_field( wp_unslash( $_POST['tp_coa_purity'] ) ) : '';
	$pdf_id = isset( $_POST['tp_coa_pdf'] ) ? absint( wp_unslash( $_POST['tp_coa_pdf'] ) ) : 0;

	update_post_meta( $post_id, TP_COA_BATCH, $batch );
	update_post_meta( $post_id, TP_COA_DATE, $date );
	update_post_meta( $post_id, TP_COA_PURITY, $purity );

	if ( $pdf_id > 0 ) {
		update_post_meta( $post_id, TP_COA_PDF, $pdf_id );
	} else {
		delete_post_meta( $post_id, TP_COA_PDF );
	}
}
add_action( 'save_post_coa_library', 'tp_save_coa_meta' );

/* ---------------------------------------------------------------------
 * Admin assets (media uploader on the COA edit screen).
 * ------------------------------------------------------------------- */
function tp_coa_admin_assets( string $hook ): void {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'coa_library' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'tp-admin-coa',
		TRUEPHARM_URI . '/assets/js/admin-coa.js',
		array(),
		truepharm_asset_version( 'assets/js/admin-coa.js' ),
		true
	);
	wp_localize_script(
		'tp-admin-coa',
		'tpCoaAdmin',
		array(
			'title'  => __( 'Select or Upload a COA PDF', 'truepharm' ),
			'button' => __( 'Use this PDF', 'truepharm' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'tp_coa_admin_assets' );

/* ---------------------------------------------------------------------
 * Admin list columns.
 * ------------------------------------------------------------------- */
function tp_coa_columns( array $columns ): array {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['coa_batch']  = __( 'Batch Number', 'truepharm' );
			$new['coa_date']   = __( 'Testing Date', 'truepharm' );
			$new['coa_purity'] = __( 'Verified Purity', 'truepharm' );
			$new['coa_pdf']    = __( 'PDF', 'truepharm' );
		}
	}
	return $new;
}
add_filter( 'manage_coa_library_posts_columns', 'tp_coa_columns' );

function tp_coa_column_content( string $column, int $post_id ): void {
	switch ( $column ) {
		case 'coa_batch':
			echo esc_html( (string) get_post_meta( $post_id, TP_COA_BATCH, true ) ?: '—' );
			break;

		case 'coa_date':
			$date = (string) get_post_meta( $post_id, TP_COA_DATE, true );
			echo $date ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) : '—';
			break;

		case 'coa_purity':
			echo esc_html( (string) get_post_meta( $post_id, TP_COA_PURITY, true ) ?: '—' );
			break;

		case 'coa_pdf':
			$pdf_id = (int) get_post_meta( $post_id, TP_COA_PDF, true );
			$url    = $pdf_id ? wp_get_attachment_url( $pdf_id ) : '';
			if ( $url ) {
				printf(
					'<a href="%s" target="_blank" rel="noopener">%s</a>',
					esc_url( $url ),
					esc_html__( 'View PDF', 'truepharm' )
				);
			} else {
				echo '<span style="color:#a00;">' . esc_html__( 'No PDF', 'truepharm' ) . '</span>';
			}
			break;
	}
}
add_action( 'manage_coa_library_posts_custom_column', 'tp_coa_column_content', 10, 2 );

function tp_coa_sortable_columns( array $columns ): array {
	$columns['coa_date'] = 'coa_date';
	return $columns;
}
add_filter( 'manage_edit-coa_library_sortable_columns', 'tp_coa_sortable_columns' );

function tp_coa_orderby( WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( 'coa_date' === $query->get( 'orderby' ) ) {
		$query->set( 'meta_key', TP_COA_DATE );
		$query->set( 'orderby', 'meta_value' );
	}
}
add_action( 'pre_get_posts', 'tp_coa_orderby' );

/* ---------------------------------------------------------------------
 * Flush rewrite rules so the CPT archive / single permalinks resolve.
 * Runs on theme activation, and self-heals once for installs that were
 * already active before the CPT shipped.
 * ------------------------------------------------------------------- */
function tp_coa_flush_rewrites(): void {
	tp_register_coa_cpt();
	flush_rewrite_rules();
	update_option( 'tp_coa_rewrites', '1' );
}
add_action( 'after_switch_theme', 'tp_coa_flush_rewrites' );

function tp_coa_maybe_flush(): void {
	if ( '1' !== get_option( 'tp_coa_rewrites' ) ) {
		flush_rewrite_rules();
		update_option( 'tp_coa_rewrites', '1' );
	}
}
add_action( 'init', 'tp_coa_maybe_flush', 11 );
