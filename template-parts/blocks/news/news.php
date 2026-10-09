<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-news',
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

[
    'header_tag' => $tag,
    'header_size' => $size,
    'header_color' => $heading_color,
    'header_line_height' => $heading_height,
    'text_type' => $text_size,
    'text_color' => $text_color,
    'text_line_height' => $text_height,
] = Helpers\get_block_typography(['header_size' => 'h5', 'header_color' => 'h-dark-blue', 'text_color' => 'h-dark-blue']);

$category_ids = array_filter(array_map('absint', (array) get_field('news_categories')));

$categories = get_categories([
    'hide_empty' => true,
    'exclude'    => [get_option('default_category')],
    'number'     => 5,
]);

if ($category_ids) {
    $categories = get_categories([
        'include'    => $category_ids,
        'hide_empty' => false,
        'orderby'    => 'include',
    ]);
}

$category_ids = wp_list_pluck($categories, 'term_id');

$instance = sanitize_html_class($block['id'] ?? 'news');
$filter_key = 'news_category_' . $instance;
$selected = isset($_GET[$filter_key]) ? absint(wp_unslash($_GET[$filter_key])) : 0;

if (!in_array($selected, $category_ids, true)) {
    $selected = 0;
}

$args = [
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 4,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
    'orderby'             => 'date',
    'order'               => 'DESC',
];

if ($selected) {
    $args['cat'] = $selected;
}

$featured_id = absint(get_field('news_featured_post'));
$featured = $featured_id ? get_post($featured_id) : null;

if (
    $featured
    && (
        $featured->post_type !== 'post'
        || $featured->post_status !== 'publish'
        || ($selected && !has_category($selected, $featured))
    )
) {
    $featured = null;
}

if ($featured) {
    $args['post__not_in'] = [$featured->ID];
    $args['posts_per_page'] = 3;
}

$query = new WP_Query($args);
$stories = $query->posts;

if ($featured) {
    array_unshift($stories, $featured);
}

$archive = get_field('news_archive_link') ?: [];
$archive_url = $archive['url'] ?? '';
$base_url = remove_query_arg($filter_key);

$newsletter_shortcode = get_field('news_newsletter_shortcode');
$newsletter_link = get_field('news_newsletter_link') ?: [];

$meta = static function ($story, $is_feature = false) use ($selected) {
    $terms = get_the_category($story->ID);
    $category = $terms[0] ?? null;

    if ($selected) {
        foreach ($terms as $term) {
            if ($term->term_id === $selected) {
                $category = $term;
            }
        }
    }
    ?>
    <div class="m-news__meta<?php echo $is_feature ? ' m-news__meta--featured' : ''; ?>">
        <?php if ($category) : ?>
            <span class="a-text a-text--xs"><?php echo esc_html($category->name); ?></span>
        <?php endif; ?>
        <time class="a-text a-text--xs" datetime="<?php echo esc_attr(get_the_date('c', $story)); ?>">
            <?php echo esc_html(get_the_date('M Y', $story)); ?>
        </time>
    </div>
    <?php
};

$render_filters = static function () use ($base_url, $selected, $categories, $filter_key, $archive_url, $archive) {
    ?>
    <nav class="m-news__filters" aria-label="News categories">
        <a class="a-text a-text--l" href="<?php echo esc_url($base_url); ?>" data-news-filter<?php if (!$selected) : ?> aria-current="true"<?php endif; ?>>All</a>
        <?php foreach ($categories as $category) : ?>
            <a class="a-text a-text--l" href="<?php echo esc_url(add_query_arg($filter_key, $category->term_id, $base_url)); ?>" data-news-filter<?php if ($selected === $category->term_id) : ?> aria-current="true"<?php endif; ?>>
                <?php echo esc_html($category->name); ?>
            </a>
        <?php endforeach; ?>
        <a class="a-text a-text--l" href="<?php echo esc_url($archive_url); ?>">
            <?php echo esc_html($archive['title'] ?? ''); ?>
            <span aria-hidden="true">&rarr;</span>
        </a>
    </nav>
    <?php
};
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    data-news="<?php echo esc_attr($instance); ?>"
>
    <div class="container">
        <div class="m-news__header">
            <div class="m-news__heading">
                <p class="a-badge"><?php echo esc_html(get_field('news_label')); ?></p>
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($heading_color); ?> h--<?php echo esc_attr($heading_height); ?>">
                    <?php echo esc_html(get_field('news_title')); ?>
                </<?php echo tag_escape($tag); ?>>
            </div>
            <?php $render_filters(); ?>
        </div>

        <div class="m-news__stories" aria-live="polite">
            <?php if ($stories) : ?>
                <?php $lead = $stories[0]; ?>

                <article class="m-news__feature">
                    <a class="m-news__image" href="<?php echo esc_url(get_permalink($lead)); ?>" aria-label="<?php echo esc_attr(get_the_title($lead)); ?>">
                        <?php echo get_the_post_thumbnail($lead, 'large', ['loading' => 'lazy', 'decoding' => 'async']); ?>
                    </a>

                    <?php $meta($lead, true); ?>

                    <div class="m-news__feature-title">
                        <h3 class="a-heading a-heading--h5 h-dark-blue">
                            <a href="<?php echo esc_url(get_permalink($lead)); ?>"><?php echo esc_html(get_the_title($lead)); ?></a>
                        </h3>
                    </div>

                    <div class="m-news__excerpt">
                        <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                            <?php echo esc_html(wp_strip_all_tags(get_the_excerpt($lead))); ?>
                        </p>
                    </div>

                    <a class="m-news__read a-text a-text--s" href="<?php echo esc_url(get_permalink($lead)); ?>">
                        Read the story <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                    </a>
                </article>

                <div class="m-news__sidebar">
                    <div class="m-news__list">
                        <?php foreach (array_slice($stories, 1) as $story) : ?>
                            <article class="m-news__item">
                                <div class="m-news__item-content">
                                    <?php $meta($story); ?>
                                    <h3 class="a-text a-text--xxl h-dark-blue">
                                        <a href="<?php echo esc_url(get_permalink($story)); ?>"><?php echo esc_html(get_the_title($story)); ?></a>
                                    </h3>
                                </div>
                                <a class="m-news__thumbnail" href="<?php echo esc_url(get_permalink($story)); ?>" aria-label="<?php echo esc_attr(get_the_title($story)); ?>">
                                    <?php echo get_the_post_thumbnail($story, 'medium', ['loading' => 'lazy', 'decoding' => 'async']); ?>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <div class="m-news__newsletter">
                        <div class="m-news__newsletter-header">
                            <div class="m-news__newsletter-icon" aria-hidden="true">
                                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/pics/icons/message.svg'); ?>" alt="" width="24" height="24">
                            </div>

                            <div class="m-news__newsletter-header-text">
                                <p class="m-news__newsletter-label a-text a-text--xs h-semibold"><?php echo esc_html(get_field('news_newsletter_label')); ?></p>
                                <h3 class="m-news__newsletter-title a-text a-text--xxl h-dark-blue h-semibold"><?php echo esc_html(get_field('news_newsletter_title')); ?></h3>
                            </div>
                        </div>

                        <?php
                        $newsletter_items = array_filter([
                            get_field('news_newsletter_item_1'),
                            get_field('news_newsletter_item_2'),
                            get_field('news_newsletter_item_3'),
                        ]);
                        if ($newsletter_items) : ?>
                            <ul class="m-news__newsletter-list">
                                <?php foreach ($newsletter_items as $item) : ?>
                                    <li class="m-news__newsletter-item a-text a-text--m">
                                        <img class="m-news__newsletter-check" src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/pics/icons/Check.svg'); ?>" alt="" aria-hidden="true" width="24" height="24">
                                        <?php echo esc_html($item); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if ($newsletter_shortcode) : ?>
                            <div class="m-news__form"><?php echo do_shortcode($newsletter_shortcode); ?></div>
                        <?php else : ?>
                            <form class="m-news__signup" action="<?php echo esc_url($newsletter_link['url'] ?? ''); ?>" method="get">
                                <img class="m-news__signup-icon" src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/pics/icons/message.svg'); ?>" alt="" aria-hidden="true" width="20" height="20">
                                <input class="a-text a-text--l h-regular" type="email" name="email" placeholder="<?php echo esc_attr(get_field('news_newsletter_placeholder') ?: 'Your email address'); ?>" aria-label="Email address" autocomplete="email" required>
                                <button class="m-news__subscribe a-text a-text--m" type="submit">
                                    <?php echo esc_html($newsletter_link['title'] ?? 'Subscribe'); ?>
                                    <i class="icon-arrow-right a-icon" aria-hidden="true"></i>
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php $newsletter_privacy = get_field('news_newsletter_privacy'); if ($newsletter_privacy) : ?>
                            <p class="m-news__newsletter-privacy a-text a-text--m">
                                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/pics/icons/policy.svg'); ?>" alt="" aria-hidden="true" width="16" height="16">
                                <?php echo esc_html($newsletter_privacy); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="m-news__empty">
                    <p class="a-text a-text--m">No news has been published in this category yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>