<?php
/*
 * single.php
 * Template for single post (blog/news).
 */
get_header();


    $post_title = get_the_title();
    $post_excerpt = get_the_excerpt();
    $featured_img_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
    $posts_page_id = (int) get_option('page_for_posts');
    $posts_page_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/news/');
    $post_cats = get_the_category();
    $excluded_slugs = ['all', 'news', 'publication'];
    $filtered_cats = [];
    $related_category_ids = [];

    if (!empty($post_cats)) {
        $filtered_cats = array_values(array_filter($post_cats, function ($cat) use ($excluded_slugs) {
            return !in_array(strtolower($cat->slug), $excluded_slugs, true);
        }));
        $related_category_ids = wp_list_pluck($filtered_cats, 'term_id');
    }

    $show_fallback_related = !has_block('acf/related-posts', get_the_ID());
?>
<section class="o-hero-banner o-hero--half s-d-t-3xl s-d-b-m s-m-t-xl s-m-b-m">
    <div class="m-content h-text-center">
        <div class="m-tag h-text-center h-no-margin m-tag--center">
            <a href="<?php echo esc_url($posts_page_url); ?>" class="a-tag a-tag--purple" data-aos="fade-up">
                <span>News</span>
            </a>
            <?php
                if (!empty($filtered_cats)) {
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

<section class="m-content s-d-b-m s-m-b-m">
    <div class="container h-flex-center">
        <a href="<?php echo esc_url( home_url( '/media/' ) ); ?>" class="a-btn a-btn--link h-bold">
            <span class="h-link">
                <?php esc_html_e( 'Back to Media', 'mca-kosovo' ); ?>
            </span>
            <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
        </a>
    </div>
</section>

<?php
get_footer();
?>