<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-spotlight',
    'o-spotlight--featured',
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
    'header_color'       => $heading_color,
    'header_line_height' => $heading_height,
    'text_type'          => $text_size,
    'text_color'         => $text_color,
    'text_line_height'   => $text_height,
] = Helpers\get_block_typography([
    'header_size' => 'h5',
    'header_color' => 'h-dark-blue',
    'header_line_height' => 'xs',
    'text_type' => 'm',
    'text_color' => 'h-dark-blue',
    'text_line_height' => 'l',
]);

$badge       = get_field('gallery_filter_badge') ?: 'Featured';
$category    = get_field('gallery_filter_category') ?: 'Energy Storage';
$date        = get_field('gallery_filter_date') ?: 'Oct 2026';
$title       = get_field('gallery_filter_title') ?: 'The procurement of the Battery Energy Systems in its final phase';
$description = get_field('gallery_filter_description');
$link        = get_field('gallery_filter_link') ?: [];
$link_url    = $link['url'] ?? '';
$link_target = $link['target'] ?? '';
$image       = get_theme_file_uri('/assets/img/pics/gallery/media-hero.png');

if (!$link_url) {
    $posts_page = absint(get_option('page_for_posts'));
    $link_url   = $posts_page ? get_permalink($posts_page) : home_url('/');
}
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <article class="m-spotlight m-spotlight--featured">
            <div class="m-spotlight__image" aria-hidden="true">
                <img src="<?php echo esc_url($image); ?>" alt="" loading="lazy" decoding="async">
            </div>

            <div class="m-spotlight__content">
                <div class="m-spotlight__label m-spotlight__featured-meta">
                    <?php if ($badge) : ?>
                        <span class="a-spotlight-pill a-text a-text--xs h-semibold"><?php echo esc_html($badge); ?></span>
                    <?php endif; ?>
                    <?php if ($category) : ?><span class="a-text a-text--xs"><?php echo esc_html($category); ?></span><?php endif; ?>
                    <?php if ($date) : ?><span class="a-text a-text--xs"><?php echo esc_html($date); ?></span><?php endif; ?>
                </div>

                <<?php echo tag_escape($tag); ?> class="m-spotlight__featured-title a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($heading_color); ?> h--<?php echo esc_attr($heading_height); ?>">
                    <?php echo esc_html($title); ?>
                </<?php echo tag_escape($tag); ?>>

                <?php if ($description) : ?>
                    <p class="m-spotlight__featured-description a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                        <?php echo esc_html($description); ?>
                    </p>
                <?php endif; ?>

                <?php if ($link_url) : ?>
                    <div class="m-spotlight__buttons">
                        <a class="a-spotlight-button a-spotlight-button--primary a-text a-text--s h-white" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"<?php if ($link_target === '_blank') : ?> rel="noopener noreferrer"<?php endif; ?>>
                            <?php echo esc_html(!empty($link['title']) ? $link['title'] : 'Read the story'); ?>
                            <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    </div>
</section>
