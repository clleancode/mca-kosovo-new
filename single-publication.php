<?php
/*
 * single.php
 * Template for single post (blog/news).
 */
get_header();


    $post_title = get_the_title();
    $post_excerpt = get_the_excerpt();
    $featured_img_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
?>


<?php while ( have_posts() ) : the_post(); ?>
	<?php the_content();?>
<?php endwhile;?>
<?php
get_footer();
?>