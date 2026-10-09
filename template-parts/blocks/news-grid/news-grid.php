<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-news-grid',
    $visibility['visibility_classes'],
    $block['className'] ?? '',
];

foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");

        if ($space) {
            $classes[] = "s-{$prefix}-{$suffix}-{$space}";
        }
    }
}

['header_tag' => $tag] = Helpers\get_block_typography([
    'header_tag' => 'h2',
    'header_size' => 'h5',
    'header_color' => 'h-dark-blue',
]);

$instance = sanitize_key($block['id'] ?? 'news-grid');
$page_key = 'news_grid_page_' . $instance;
$current_page = isset($_GET[$page_key]) ? max(1, absint(wp_unslash($_GET[$page_key]))) : 1;
$per_page = absint(get_field('news_grid_posts_per_page')) ?: 5;
$per_page = min(max($per_page, 1), 12);
$query = new WP_Query([
    'post_type'           => 'news',
    'post_status'         => 'publish',
    'posts_per_page'      => $per_page,
    'paged'               => $current_page,
    'ignore_sticky_posts' => true,
    'orderby'             => 'date',
    'order'               => 'DESC',
]);

$total_pages = max(1, (int) $query->max_num_pages);
if ($current_page > $total_pages) {
    $current_page = $total_pages;
    wp_reset_postdata();
    $query = new WP_Query([
        'post_type'           => 'news',
        'post_status'         => 'publish',
        'posts_per_page'      => $per_page,
        'paged'               => $current_page,
        'ignore_sticky_posts' => true,
        'orderby'             => 'date',
        'order'               => 'DESC',
    ]);
}

$base_url = remove_query_arg($page_key);
$newsletter_label = get_field('news_grid_newsletter_label') ?: 'Newsletter';
$newsletter_title = get_field('news_grid_newsletter_title') ?: 'Never miss a Compact update';
$newsletter_description = get_field('news_grid_newsletter_description') ?: 'News, procurement notices and opportunities — straight to your inbox.';
$newsletter_placeholder = get_field('news_grid_newsletter_placeholder') ?: 'Email address';
$newsletter_shortcode = get_field('news_grid_newsletter_shortcode');
$newsletter_link = get_field('news_grid_newsletter_link') ?: [];
$newsletter_action = $newsletter_link['url'] ?? '';
$newsletter_action = $newsletter_action ?: $base_url;
$newsletter_button = !empty($newsletter_link['title']) ? $newsletter_link['title'] : 'Subscribe';
$anchor = !empty($block['anchor']) ? '#' . rawurlencode($block['anchor']) : '';

$category_colors = ['pink', 'purple', 'blue', 'navy'];
$category_color_map = [
    'partnerships'          => 'purple',
    'compact'               => 'navy',
    'notice'                => 'pink',
    'energy-projects'       => 'blue',
    'millennium-development' => 'purple',
];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-news-grid__header">
            <<?php echo tag_escape($tag); ?> class="a-heading a-heading--h5 h-dark-blue">
                <?php echo esc_html(get_field('news_grid_title') ?: 'Latest news'); ?>
            </<?php echo tag_escape($tag); ?>>
            <p class="m-news-grid__page-count a-text a-text--xs" aria-live="polite">
                PAGE <?php echo esc_html($current_page); ?> OF <?php echo esc_html($total_pages); ?>
            </p>
        </div>

        <div class="m-news-grid__cards">
            <?php if ($query->have_posts()) : ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $post_id = get_the_ID();
                    $categories = get_the_terms($post_id, 'news_category');
                    $categories = is_array($categories) ? $categories : [];
                    $category = $categories[0] ?? null;
                    $color_index = $category ? absint($category->term_id) % count($category_colors) : 0;
                    $category_color = $category
                        ? ($category_color_map[$category->slug] ?? $category_colors[$color_index])
                        : $category_colors[0];
                    ?>
                    <article class="m-news-grid__card">
                        <a class="m-news-grid__image" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large', ['loading' => 'lazy', 'decoding' => 'async']); ?>
                            <?php endif; ?>
                        </a>

                        <div class="m-news-grid__content">
                            <div class="m-news-grid__meta">
                                <?php if ($category) : ?>
                                    <span class="m-news-grid__badge m-news-grid__badge--<?php echo esc_attr($category_color); ?> a-text a-text--xs h-semibold">
                                        <?php echo esc_html($category->name); ?>
                                    </span>
                                <?php endif; ?>
                                <time class="a-text a-text--xs" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo esc_html(get_the_date('M Y')); ?>
                                </time>
                            </div>

                            <h2 class="m-news-grid__title a-text a-text--xl h-semibold h-dark-blue">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <a class="m-news-grid__read a-text a-text--xs" href="<?php the_permalink(); ?>">
                                Read more <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <p class="m-news-grid__empty a-text a-text--m">No news has been published yet.</p>
            <?php endif; ?>

            <aside class="m-news-grid__newsletter">
                <p class="m-news-grid__newsletter-label a-text a-text--xs h-semibold"><?php echo esc_html($newsletter_label); ?></p>
                <h2 class="m-news-grid__newsletter-title a-heading a-heading--h6 h-white h-semibold"><?php echo esc_html($newsletter_title); ?></h2>
                <p class="m-news-grid__newsletter-description a-text a-text--s h-white"><?php echo esc_html($newsletter_description); ?></p>

                <?php if ($newsletter_shortcode) : ?>
                    <div class="m-news-grid__newsletter-form"><?php echo do_shortcode($newsletter_shortcode); ?></div>
                <?php else : ?>
                    <form class="m-news-grid__signup" action="<?php echo esc_url($newsletter_action); ?>" method="get">
                        <input type="email" name="email" placeholder="<?php echo esc_attr($newsletter_placeholder); ?>" aria-label="Email address" autocomplete="email" required>
                        <button class="a-text a-text--m h-dark-blue" type="submit"><?php echo esc_html($newsletter_button); ?></button>
                    </form>
                <?php endif; ?>
            </aside>
        </div>

        <?php if ($total_pages > 1) : ?>
            <nav class="m-news-grid__pagination" aria-label="News pages">
                <?php if ($current_page > 1) : ?>
                    <a class="m-news-grid__page m-news-grid__page--arrow a-text a-text--xs" href="<?php echo esc_url(add_query_arg($page_key, $current_page - 1, $base_url) . $anchor); ?>" aria-label="Previous page">&larr;</a>
                <?php else : ?>
                    <span class="m-news-grid__page m-news-grid__page--arrow a-text a-text--xs" aria-disabled="true">&larr;</span>
                <?php endif; ?>

                <?php for ($page = 1; $page <= $total_pages; $page++) : ?>
                    <?php if ($page === $current_page) : ?>
                        <span class="m-news-grid__page m-news-grid__page--current a-text a-text--xs" aria-current="page"><?php echo esc_html($page); ?></span>
                    <?php else : ?>
                        <a class="m-news-grid__page a-text a-text--xs" href="<?php echo esc_url(add_query_arg($page_key, $page, $base_url) . $anchor); ?>"><?php echo esc_html($page); ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages) : ?>
                    <a class="m-news-grid__page m-news-grid__page--arrow a-text a-text--xs" href="<?php echo esc_url(add_query_arg($page_key, $current_page + 1, $base_url) . $anchor); ?>" aria-label="Next page">&rarr;</a>
                <?php else : ?>
                    <span class="m-news-grid__page m-news-grid__page--arrow a-text a-text--xs" aria-disabled="true">&rarr;</span>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
</section>
<?php
wp_reset_postdata();
