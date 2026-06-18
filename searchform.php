<?php
/**
 * Search form — styled to match the shop sidebar .search-box.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="search-box" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="tp-search-field"><?php esc_html_e( 'Search for:', 'truepharm' ); ?></label>
	<input type="search" id="tp-search-field" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e( 'Search compounds & pages...', 'truepharm' ); ?>">
	<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'truepharm' ); ?>">
		<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="vertical-align:middle;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
	</button>
</form>
