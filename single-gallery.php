<?php
/*
 * single-gallery.php
 * Template for single Galleries posts.
 */
get_header();


$gallery_title = get_the_title();
$gallery_year = get_the_date('Y');
?>
<section class="o-hero-banner o-hero-banner--left  s-d-t-2xl s-d-b-m  s-m-t-2xl s-m-b-s">
    <div class="container">
        <div class="m-content h-full">
            <h1 class="a-heading a-heading--h3"><?php echo esc_html($gallery_title); ?></h1>
            <h5 class="a-heading a-heading--h5"><?php echo esc_html($gallery_year); ?></h5>
        </div>
    </div>
</section>


<section class="o-gallery-inside s-d-b-l s-m-b-m">
    <div class="container">
        <?php
        $columns = [[], [], []];
        if (have_rows('images')) {
            $index = 0;
            while (have_rows('images')) {
                the_row();
                $image_id = get_sub_field('image');
                $image_url = wp_get_attachment_image_url($image_id, 'full');
                if ($image_id && $image_url) {
                    $columns[$index % 3][] = (int) $image_id;
                    $index++;
                }
            }
        }
        foreach ($columns as $column_images) {
            echo '<div>';
            foreach ($column_images as $image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'full');
                if (!$image_url) {
                    continue;
                }

                echo '<a href="' . esc_url($image_url) . '" data-fancybox="group">';
                echo wp_get_attachment_image($image_id, 'full', false, [
                    'class' => 'a-img',
                    'loading' => 'lazy',
                    'decoding' => 'async',
                ]);
                echo '</a>';
            }
            echo '</div>';
        }
        ?>
    </div>
</section>


<?php while ( have_posts() ) : the_post(); ?>
	<?php the_content();?>
<?php endwhile;?>

<?php get_footer(); ?>