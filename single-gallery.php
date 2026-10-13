<?php
/*
 * single-gallery.php
 * Template for single Galleries posts.
 */
get_header();


$gallery_title = get_the_title();
$gallery_year = get_the_date('Y');
?>


<?php
if (!has_block('acf/gallery', get_the_ID())) {
    $block = ['className' => 's-d-b-l s-m-b-m'];
    include get_template_directory() . '/template-parts/blocks/gallery/gallery.php';
}
?>

<?php while ( have_posts() ) : the_post(); ?>
	<?php the_content();?>
<?php endwhile;?>

<?php get_footer(); ?>
