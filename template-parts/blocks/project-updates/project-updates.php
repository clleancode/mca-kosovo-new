<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();

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
    'post_type'      => 'procurement',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'no_found_rows'  => true,
    'orderby'        => 'date',
    'order'          => 'DESC',
];

$selected = array_filter(array_map('absint', (array) get_field('project_updates_posts')));

if ($selected) {
    $args['post__in'] = $selected;
}

$query = new WP_Query($args);

$archive = get_field('project_updates_archive_link') ?: [];
$archive_url = $archive['url'] ?? '';
$archive_target = $archive['target'] ?? '';
?>
<section id="updates" class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <div class="m-project-updates__header s-d-b-s s-m-b-xs">
            <div class="m-project-updates__heading">
                <p class="a-badge h-dark-blue h-semibold"><?php echo esc_html(get_field('project_updates_label')); ?></p>
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                    <?php echo esc_html(get_field('project_updates_title')); ?>
                </<?php echo tag_escape($tag); ?>>
            </div>

            <a
                class="a-project-updates-all a-text a-text--m h-semibold"
                href="<?php echo esc_url($archive_url); ?>"
                target="<?php echo esc_attr($archive_target); ?>"
                <?php if ($archive_target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
            >
                <?php echo esc_html($archive['title'] ?? ''); ?>
                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/small-right.svg')); ?>" alt="" aria-hidden="true">
            </a>
        </div>

        <?php if ($query->posts) : ?>
            <div class="m-project-updates__grid">
                <?php foreach ($query->posts as $procurement) : ?>
                    <?php
                    $type = get_field('procurement_notice_type', $procurement->ID);

                    if (!$type) {
                        $terms = get_the_terms($procurement->ID, 'procurement_status');
                        $type = $terms && !is_wp_error($terms) ? $terms[0]->name : '';
                    }
                    ?>
                    <article class="m-project-updates__card">
                        <a class="a-project-updates-image" href="<?php echo esc_url(get_permalink($procurement)); ?>" aria-label="<?php echo esc_attr(get_the_title($procurement)); ?>">
                            <?php echo get_the_post_thumbnail($procurement, 'large', ['loading' => 'lazy', 'decoding' => 'async']); ?>
                        </a>

                        <div class="m-project-updates__meta s-d-t-xs s-d-b-xs s-m-t-xs s-m-b-xs">
                            <?php if ($type) : ?>
                                <span class="a-project-updates-pill h-semibold a-text a-text--xs"><?php echo esc_html($type); ?></span>
                            <?php endif; ?>
                            <time class="a-text a-text--xs h-semibold" datetime="<?php echo esc_attr(get_the_date('c', $procurement)); ?>">
                                <?php echo esc_html(get_the_date('M Y', $procurement)); ?>
                            </time>
                        </div>

                        <h3 class="a-heading a-heading--h6 l h-dark-blue">
                            <a class="a-project-updates-title" href="<?php echo esc_url(get_permalink($procurement)); ?>"><?php echo esc_html(get_the_title($procurement)); ?></a>
                        </h3>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="a-text a-text--m">No project updates have been published yet.</p>
        <?php endif; ?>
    </div>
</section>