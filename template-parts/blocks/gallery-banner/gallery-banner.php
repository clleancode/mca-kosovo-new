<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-gallery-banner',
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
    'header_size' => 'h6',
    'header_line_height' => 'xs',
    'text_type' => 's',
    'text_line_height' => 'm',
]);

$title       = get_field('gallery_banner_title') ?: 'Media & News';
$description = get_field('gallery_banner_description');
$placeholder = get_field('gallery_banner_search_placeholder') ?: 'Search news and stories';
$search_id   = 'gallery-banner-search-' . ($block['id'] ?? wp_unique_id());
$background  = get_theme_file_uri('/assets/img/pics/gallery/media-hero.png');
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    style="background-image: url('<?php echo esc_url($background); ?>');"
>
    <div class="container">
        <div class="m-gallery-banner">
            <div class="m-gallery-banner__content">
                <nav class="h-breadcrumbs s-d-b-s s-m-b-xs" aria-label="Breadcrumb">
                    <ol>
                        <li>
                            <a class="a-text a-text--xs h-white" href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                        </li>
                        <li>
                            <span class="a-text a-text--xs h-white" aria-current="page">
                                <?php echo esc_html(get_the_title()); ?>
                            </span>
                        </li>
                    </ol>
                </nav>

                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?> s-d-b-s s-m-b-xs">
                    <?php echo esc_html($title); ?>
                </<?php echo tag_escape($tag); ?>>

                <?php if ($description) : ?>
                    <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                        <?php echo nl2br(esc_html($description)); ?>
                    </p>
                <?php endif; ?>
            </div>

            <form class="m-gallery-banner__search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <input
                    id="<?php echo esc_attr($search_id); ?>"
                    type="search"
                    name="s"
                    value="<?php echo esc_attr(get_search_query()); ?>"
                    placeholder="<?php echo esc_attr($placeholder); ?>"
                    aria-label="<?php echo esc_attr($placeholder); ?>"
                >
                <button class="a-btn a-btn--smallest h-white" type="submit">Search</button>
            </form>
        </div>
    </div>
</section>
