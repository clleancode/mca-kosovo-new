<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-careers-hero',
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
    'header_tag' => 'h1',
    'text_type'  => 'l',
]);

$page_id     = get_the_ID();
$description = get_field('careers_hero_description');
$photos      = array_slice(get_field('careers_hero_photos') ?: [], 0, 6);
$statistic   = get_field('careers_hero_statistic');
$caption     = get_field('careers_hero_statistic_caption');

$links = [
    'primary'   => get_field('careers_hero_primary_link'),
    'secondary' => get_field('careers_hero_secondary_link'),
];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-careers-hero">
            <div class="m-careers-hero__content">
                <nav class="h-breadcrumbs s-d-b-xs" aria-label="Breadcrumb">
                    <ol>
                        <li>
                            <a class="a-text a-text--xs h-white" href="<?php echo esc_url(home_url('/')); ?>">
                                Home
                            </a>
                        </li>
                        <?php foreach (array_reverse(get_post_ancestors($page_id)) as $parent_id) : ?>
                            <li>
                                <a class="a-text a-text--xs h-white" href="<?php echo esc_url(get_permalink($parent_id)); ?>">
                                    <?php echo esc_html(get_the_title($parent_id)); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <li>
                            <span class="a-text a-text--xs h-white" aria-current="page">
                                <?php echo esc_html(get_the_title($page_id)); ?>
                            </span>
                        </li>
                    </ol>
                </nav>
                <<?php echo tag_escape($tag); ?>
                    class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?> h-uppercase s-d-b-xs s-m-b-xs"
                >
                    <?php echo nl2br(esc_html(get_field('careers_hero_title') ?: 'Careers at MCA Kosovo')); ?>
                </<?php echo tag_escape($tag); ?>>
                <?php if ($description) : ?>
                    <div class="m-careers-hero__description s-d-t-s s-d-b-s s-m-b-xs s-m-t-xs">
                        <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                            <?php echo nl2br(esc_html($description)); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="m-careers-hero__buttons">
                    <?php foreach ($links as $type => $link) : ?>
                        <?php
                        if (empty($link['url'])) {
                            continue;
                        }
                        $target = ($link['target'] ?? '') ?: '_self';
                        $title  = ($link['title'] ?? '') ?: ($type === 'primary' ? 'View vacancies' : 'Get job alerts');
                        $btn    = $type === 'primary' ? 'a-btn--white' : 'a-btn--border-white h-white';
                        ?>
                        <a
                            class="a-btn h-semibold <?php echo esc_attr($btn); ?>"
                            href="<?php echo esc_url($link['url']); ?>"
                            target="<?php echo esc_attr($target); ?>"
                            <?php if ($target === '_blank') : ?>
                                rel="noopener noreferrer"
                            <?php endif; ?>
                        >
                            <span><?php echo esc_html($title); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if ($photos) : ?>
                <div class="m-careers-hero__collage">
                    <?php foreach ($photos as $photo) : ?>
                        <div class="m-careers-hero__photo">
                            <?php
                            if (!empty($photo['image'])) {
                                echo wp_get_attachment_image($photo['image'], 'large', false, [
                                    'loading'  => 'eager',
                                    'decoding' => 'async',
                                ]);
                            }
                            ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($statistic || $caption) : ?>
                        <div class="m-careers-hero__statistic">
                            <?php if ($statistic) : ?>
                                <span class="a-heading a-heading--h6 h-white h-semibold">
                                    <?php echo wp_kses_post(nl2br($statistic)); ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($caption) : ?>
                                <span class="a-text a-text--xs h-white h-semibold">
                                    <?php echo wp_kses_post(nl2br($caption)); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>