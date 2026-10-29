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
    $media_news_page = $posts_page_id ? get_post($posts_page_id) : get_page_by_path('media-news');
    $posts_page_url = $media_news_page ? get_permalink($media_news_page) : home_url('/media-news/');
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
    $has_single_hero = has_block('acf/single-hero', get_the_ID());
?>
<?php if (!$has_single_hero) : ?>
<section class="o-hero-banner o-hero--half s-d-t-3xl s-d-b-m s-m-t-xl s-m-b-m">
    <div class="m-content h-text-center">
        <div class="m-tag h-text-center h-no-margin m-tag--center">
            <a href="<?php echo esc_url($posts_page_url); ?>" class="a-tag a-tag--purple e-reveal">
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
                    echo '<a href="' . esc_url($category_link) . '" class="a-tag ' . esc_attr($class) . ' e-reveal" data-delay="' . esc_attr($pos * 100) . '">' . esc_html($cat->name) . '</a> ';
                }
            }
            ?>
        </div>
        <h1 class="a-heading a-heading--h3 e-reveal e-reveal--text" data-delay="100"><?php echo esc_html( $post_title ); ?></h1>
        <p class="a-text a-text--xl h-semibold e-reveal e-reveal--text" data-delay="350"><?php echo get_the_date(); ?></p>
    </div>
    <?php if ( $featured_img_url ) : ?>
        <img src="<?php echo esc_url( $featured_img_url ); ?>" class="a-img e-reveal" data-delay="500" loading="lazy" decoding="async" alt="<?php echo esc_attr( $post_title ); ?>">
    <?php endif; ?>
</section>
<?php endif; ?>

<?php while (have_posts()) : the_post(); ?>
    <?php the_content(); ?>
<?php endwhile; ?>


<?php
get_footer();
?>
