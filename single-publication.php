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
<section class="o-hero-banner o-hero--half s-d-t-3xl s-d-b-m s-m-t-xl s-m-b-m">
    <div class="m-content h-text-center">
        <div class="m-tag h-text-center h-no-margin m-tag--center">
            <a href="#" class="a-tag a-tag--purple" data-aos="fade-up">
                <span>Publication</span>
            </a>
            <?php
                $post_cats = get_the_category();
                $excluded_slugs = ['all', 'news', 'publication'];
                
                if (!empty($post_cats)) {

                    $filtered_cats = array_filter($post_cats, function($cat) use ($excluded_slugs) {
                        return !in_array(strtolower($cat->slug), $excluded_slugs);
                    });
                $filtered_cats = array_values($filtered_cats);

                foreach ($filtered_cats as $index => $cat) {
                    $pos = $index + 1;
                    if ($pos % 3 === 1) {
                        $class = 'a-tag--green';
                    } elseif ($pos % 3 === 2) {
                        $class = 'a-tag--blue';
                    } else { 
                        $class = 'a-tag--purple';
                    }
                    $category_link = get_category_link($cat->term_id);
                    echo '<a href="' . esc_url($category_link) . '" class="a-tag ' . esc_attr($class) . '">' . esc_html($cat->name) . '</a> ';
                }
            }
            ?>
        </div>
        <h1 class="a-heading a-heading--h3" data-aos="fade-up" data-aos-delay="100"><?php echo esc_html( $post_title ); ?></h1>
        <p class="a-text a-text--xl h-semibold" data-aos="fade-up" data-aos-delay="200"><?php echo get_the_date(); ?></p>
    </div>
    <?php if ( $featured_img_url ) : ?>
        <img src="<?php echo esc_url( $featured_img_url ); ?>" class="a-img" loading="lazy" decoding="async" alt="<?php echo esc_attr( $post_title ); ?>"  data-aos="fade-up" data-aos-delay="300">
    <?php endif; ?>
</section>

<?php while ( have_posts() ) : the_post(); ?>
	<?php the_content();?>
<?php endwhile;?>
<?php
get_footer();
?>