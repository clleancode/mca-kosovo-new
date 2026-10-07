<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-overview',
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
    'header_color' => $color,
    'header_line_height' => $height,
    'text_type' => $text_size,
    'text_color' => $text_color,
    'text_line_height' => $text_height,
] = Helpers\get_block_typography(['header_color' => 'h-dark-blue', 'text_type' => 'l', 'text_color' => 'h-dark-blue']);

$content_id = sanitize_html_class(($block['id'] ?? 'overview') . '-content');
$links = get_field('overview_navigation') ?: [];
$factsheet = get_field('overview_factsheet') ?: [];
$factsheet_target = $factsheet['target'] ?? '';
$benefits = array_slice(get_field('overview_benefits') ?: [], 0, 3);
$main_image = get_field('overview_main_image');
$detail_image = get_field('overview_detail_image');
$description = get_field('overview_description');
$statistic = get_field('overview_statistic');
$statistic_color = Helpers\get_field_choice('overview_statistic_color', ['blue', 'purple', 'green'], 'blue');
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    data-overview
>
    <div class="m-overview__navigation">
        <div class="container">
            <div class="m-overview__bar">
                <div class="m-overview__project">
                    <span class="a-text a-text--m h-semibold"><?php echo esc_html(get_field('overview_project_name')); ?></span>
                </div>

                <nav class="m-overview__links" aria-label="Project sections">
                    <a class="a-overview-link a-text a-text--l" href="#<?php echo esc_attr($content_id); ?>" data-overview-link aria-current="location">
                        <?php echo esc_html(get_field('overview_navigation_label')); ?>
                    </a>
                    <?php foreach ($links as $row):
                        $link = $row['link'] ?? [];
                        if (empty($link['url'])) continue;
                        $target = ($link['target'] ?? '') ?: '_self';
                        ?>
                        <a class="a-overview-link a-text a-text--l" href="<?php echo esc_url($link['url']); ?>" data-overview-link target="<?php echo esc_attr($target); ?>"<?php if ($target === '_blank'): ?> rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html($link['title'] ?? ''); ?></a>
                    <?php endforeach; ?>
                </nav>

                <?php if (!empty($factsheet['url'])) : ?>
                    <a
                        class="a-overview-factsheet a-text a-text--m h-semibold"
                        href="<?php echo esc_url($factsheet['url']); ?>"
                        target="<?php echo esc_attr($factsheet_target); ?>"
                        <?php if ($factsheet_target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                    >
                        <?php echo esc_html($factsheet['title'] ?? ''); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <div id="<?php echo esc_attr($content_id); ?>" class="m-overview__content">
            <div class="m-overview__copy">
                <p class="a-badge h-dark-blue s-d-b-xs"><?php echo esc_html(get_field('overview_label')); ?></p>

                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                    <?php echo nl2br(esc_html(get_field('overview_title'))); ?>
                </<?php echo tag_escape($tag); ?>>

                <?php if ($description) : ?>
                    <div class="m-overview__description s-d-t-s s-m-t-xs">
                        <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                            <?php echo nl2br(esc_html($description)); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if ($benefits) : ?>
                    <ol class="m-overview__benefits">
                        <?php foreach ($benefits as $index => $benefit) : ?>
                            <li>
                                <div class="m-overview__number">
                                    <span class="a-text a-text--s"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                                </div>
                                <div class="m-overview__benefit-copy">
                                    <h3 class="a-text a-text--xxl h-semibold h-dark-blue"><?php echo esc_html($benefit['title'] ?? ''); ?></h3>
                                    <?php if (!empty($benefit['description'])) : ?>
                                        <p class="a-text a-text--l"><?php echo esc_html($benefit['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            </div>

            <div class="m-overview__visual">
                <?php if ($main_image) : ?>
                    <div class="m-overview__main-image">
                        <?php echo wp_get_attachment_image($main_image, 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                    </div>
                <?php endif; ?>

                <?php if ($detail_image) : ?>
                    <div class="m-overview__detail-image">
                        <?php echo wp_get_attachment_image($detail_image, 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                    </div>
                <?php endif; ?>

                <?php if ($statistic !== '' && $statistic !== null && $statistic !== false) : ?>
                    <div class="m-overview__statistic m-overview__statistic--<?php echo esc_attr($statistic_color); ?>">
                        <div class="m-overview__value">
                            <span class="h-medium a-heading a-heading--h3 h-white"><?php echo esc_html($statistic); ?></span>
                            <span class="h-medium a-heading a-heading--h6"><?php echo esc_html(get_field('overview_unit')); ?></span>
                        </div>
                        <p class="a-text a-text--xs"><?php echo esc_html(get_field('overview_statistic_label')); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
