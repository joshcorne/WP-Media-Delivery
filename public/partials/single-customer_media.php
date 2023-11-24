<?php
/**
 * The template for displaying all single customer_media posts
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/public/partials
 */
?>

<?php
get_header();

/* Start the Loop */
while ( have_posts() ) :
	the_post();
	?>

	<article id="customer_media-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header alignwide">
		<?php //the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->

	</article><!-- #post-<?php the_ID(); ?>-->

<?php
endwhile; // End of the loop.

get_footer();

?>
