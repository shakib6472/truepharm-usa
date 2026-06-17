<?php
/**
 * The main template file — required fallback for any view without a more
 * specific template. Page-specific templates are added in later phases.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main">
	<div class="wrap" style="padding-top:48px;padding-bottom:48px;">

		<?php if ( have_posts() ) : ?>

			<?php if ( is_home() && ! is_front_page() ) : ?>
				<header class="section-head">
					<h2><?php single_post_title(); ?></h2>
				</header>
			<?php endif; ?>

			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="margin-bottom:40px;">
					<header>
						<?php the_title( sprintf( '<h2 style="margin-bottom:12px;"><a href="%s" style="text-decoration:none;color:var(--navy);">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" style="display:block;margin-bottom:16px;">
							<?php the_post_thumbnail( 'large', array( 'style' => 'border-radius:var(--radius);' ) ); ?>
						</a>
					<?php endif; ?>

					<div class="entry-content">
						<?php the_excerpt(); ?>
					</div>
				</article>
				<?php
			endwhile;

			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __( 'Previous', 'truepharm' ),
					'next_text' => __( 'Next', 'truepharm' ),
				)
			);

		else :
			?>
			<p><?php esc_html_e( 'Nothing found.', 'truepharm' ); ?></p>
			<?php
		endif;
		?>

	</div>
</main>

<?php
get_footer();
