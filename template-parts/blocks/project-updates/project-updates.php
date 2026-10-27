<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-project-updates',
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
    'header_tag'         => $tag,
    'header_size'        => $size,
    'header_color'       => $color,
    'header_line_height' => $height,
    'text_type'          => $text_size,
    'text_color'         => $text_color,
    'text_line_height'   => $text_height,
] = Helpers\get_block_typography([
    'header_color' => 'h-dark-blue',
    'text_type'    => 's',
    'text_color'   => 'h-dark-blue',
]);

$args = [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'no_found_rows'  => true,
    'orderby'        => 'date',
    'order'          => 'DESC',
];

$selected = array_values(array_filter(array_map('absint', (array) get_field('project_updates_posts')), static function ($post_id) {
    return get_post_type($post_id) === 'post' && get_post_status($post_id) === 'publish';
}));

if ($selected) {
    $args['post__in'] = $selected;
}

$query = new WP_Query($args);

$archive = get_field('project_updates_archive_link') ?: [];
$archive_url = $archive['url'] ?? '';
if (!$archive_url || $archive_url === '#') {
    $news_page = get_page_by_path('media-news') ?: get_page_by_path('media');
    $archive_url = $news_page ? get_permalink($news_page) : home_url('/media-news/');
}
$archive_target = $archive['target'] ?? '';
?>
<section id="updates" class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <div class="m-project-updates__header s-d-b-s s-m-b-xs">
            <div class="m-project-updates__heading">
                <p class="a-badge h-dark-blue h-semibold e-reveal e-reveal--text"><?php echo esc_html(get_field('project_updates_label')); ?></p>
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?> e-reveal e-reveal--text" data-delay="100">
                    <?php echo esc_html(get_field('project_updates_title')); ?>
                </<?php echo tag_escape($tag); ?>>
            </div>

            <a
                class="a-underline a-text a-text--l e-reveal"
                data-delay="350"
                href="<?php echo esc_url($archive_url); ?>"
                target="<?php echo esc_attr($archive_target); ?>"
                <?php if ($archive_target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
            >
                <span><?php echo esc_html($archive['title'] ?? ''); ?></span>
                <i class="icon-arrow-right a-icon" aria-hidden="true"></i>
            </a>
        </div>

        <?php if ($query->posts) : ?>
            <div class="m-project-updates__grid">
                <?php foreach ($query->posts as $post_index => $news_post) : ?>
                    <?php
                    $terms = get_the_category($news_post->ID);
                    $type = $terms ? $terms[0]->name : '';
                    ?>
                    <article class="m-project-updates__card">
                        <a class="a-project-updates-image e-reveal e-reveal--image" data-delay="<?php echo esc_attr(150 + ($post_index % 3) * 120); ?>" href="<?php echo esc_url(get_permalink($news_post)); ?>" aria-label="<?php echo esc_attr(get_the_title($news_post)); ?>">
                            <?php echo get_the_post_thumbnail($news_post, 'large', ['loading' => 'lazy', 'decoding' => 'async']); ?>
                        </a>

                        <div class="m-project-updates__meta s-d-t-xs s-d-b-xs s-m-t-xs s-m-b-xs">
                            <?php if ($type) : ?>
                                <span class="a-project-updates-pill h-semibold a-text a-text--xs e-reveal" data-delay="<?php echo esc_attr(350 + ($post_index % 3) * 120); ?>"><?php echo esc_html($type); ?></span>
                            <?php endif; ?>
                            <time class="a-text a-text--xs h-semibold e-reveal e-reveal--text" data-delay="<?php echo esc_attr(430 + ($post_index % 3) * 120); ?>" datetime="<?php echo esc_attr(get_the_date('c', $news_post)); ?>">
                                <?php echo esc_html(get_the_date('M Y', $news_post)); ?>
                            </time>
                        </div>

                        <h3 class="a-heading a-heading--h6 l h-dark-blue e-reveal e-reveal--text" data-delay="<?php echo esc_attr(500 + ($post_index % 3) * 120); ?>">
                            <a class="a-project-updates-title" href="<?php echo esc_url(get_permalink($news_post)); ?>"><?php echo esc_html(get_the_title($news_post)); ?></a>
                        </h3>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="a-text a-text--m e-reveal e-reveal--text">No project updates have been published yet.</p>
        <?php endif; ?>
    </div>
</section>
