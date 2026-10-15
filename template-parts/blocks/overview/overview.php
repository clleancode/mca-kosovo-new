<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-overview',
    $visibility['visibility_classes'],
    $block['className'] ?? '',
];

$is_activities = get_field('overview_layout') === 'activities';
$download = get_field('overview_download') ?: [];
$content_link = get_field('overview_content_link') ?: [];
if ($is_activities) {
    $classes[] = 'o-overview--activities';
}

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
$navigation_white = get_field('overview_navigation_style') === 'pills';
$button_background = get_field('overview_button_background');
$navigation_pills = $button_background === 'white'
    || (!in_array($button_background, ['white', 'transparent'], true) && $navigation_white);
$show_sort = (bool) get_field('overview_show_sort');
$sort_label = get_field('overview_sort_label') ?: 'Sort: Newest first';
$factsheet = get_field('overview_factsheet') ?: [];
$factsheet_target = $factsheet['target'] ?? '';
$benefits = array_slice(get_field('overview_benefits') ?: [], 0, 3);
$main_image = get_field('overview_main_image');
$detail_image = get_field('overview_detail_image');
$description = get_field('overview_description');
$statistic = trim((string) get_field('overview_statistic'));
$statistic_unit = trim((string) get_field('overview_unit'));
$statistic_label = trim((string) get_field('overview_statistic_label'));
$statistic_color = Helpers\get_field_choice('overview_statistic_color', ['blue', 'purple', 'green'], 'blue');
$has_content = trim((string) get_field('overview_title')) !== ''
    || trim((string) get_field('overview_label')) !== ''
    || trim((string) $description) !== ''
    || !empty($benefits) || $main_image || $detail_image
    || $statistic !== '' || $statistic_unit !== '' || $statistic_label !== '';
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    data-overview
>
    <?php if (!$is_activities) : ?>
    <div class="m-overview__navigation<?php echo $navigation_white ? ' m-overview__navigation--white' : ''; ?>">
        <div class="container">
            <div class="m-overview__bar<?php echo $navigation_pills ? ' m-overview__bar--pills' : ''; ?>">
                <?php if (trim((string) get_field('overview_project_name')) !== ''): ?>
                <div class="m-overview__project">
                    <span class="a-text a-text--m h-semibold"><?php echo esc_html(get_field('overview_project_name')); ?></span>
                </div>
                <?php endif; ?>

                <button type="button" class="m-overview__mobile-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr($content_id . '-navigation'); ?>" data-text-reveal="off">
                    <span data-overview-current><?php echo esc_html(get_field('overview_navigation_label')); ?></span>
                    <span class="m-overview__chevron" aria-hidden="true"></span>
                </button>
                <nav id="<?php echo esc_attr($content_id . '-navigation'); ?>" class="m-overview__links" aria-label="Project sections">
                    <a class="a-overview-link a-text a-text--l h-semibold" href="#<?php echo esc_attr($content_id); ?>" data-overview-link aria-current="location">
                        <?php echo esc_html(get_field('overview_navigation_label')); ?>
                    </a>
                    <?php foreach ($links as $row):
                        $link = $row['link'] ?? [];
                        if (empty($link['url'])) continue;
                        $target = ($link['target'] ?? '') ?: '_self';
                        ?>
                        <a class="a-overview-link a-text a-text--l h-semibold" href="<?php echo esc_url($link['url']); ?>" data-overview-link target="<?php echo esc_attr($target); ?>"<?php if ($target === '_blank'): ?> rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html($link['title'] ?? ''); ?></a>
                    <?php endforeach; ?>
                </nav>

                <?php if ($show_sort) : ?>
                    <span class="a-overview-factsheet a-overview-factsheet--sort a-text a-text--m h-semibold">
                        <?php echo esc_html($sort_label); ?>
                    </span>
                <?php elseif (!empty($factsheet['url'])) : ?>
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

    <?php endif; ?>
    <?php if ($has_content): ?>
    <div class="container">
        <div id="<?php echo esc_attr($content_id); ?>" class="m-overview__content">
            <div class="m-overview__copy">
                <?php if (trim((string) get_field('overview_label')) !== ''): ?>
                    <p class="a-badge h-dark-blue s-d-b-xs h-semibold"><?php echo esc_html(get_field('overview_label')); ?></p>
                <?php endif; ?>

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

                <?php if ($is_activities && (!empty($download['url']) || !empty($content_link['url']))) : ?>
                    <div class="m-overview__actions s-d-t-s s-m-t-xs">
                        <?php if (!empty($download['url'])) : ?>
                            <a class="a-btn a-btn--blue" href="<?php echo esc_url($download['url']); ?>"<?php if (($download['target'] ?? '') === '_blank') : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>>
                                <span><?php echo esc_html($download['title'] ?: __('Download', 'MCA')); ?> <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/white-download.svg')); ?>" width="18" height="18" alt="" aria-hidden="true"></span>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($content_link['url'])) : ?>
                            <a class="a-underline a-text a-text--m h-dark-blue" href="<?php echo esc_url($content_link['url']); ?>"<?php if (($content_link['target'] ?? '') === '_blank') : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>>
                                <span><?php echo esc_html($content_link['title'] ?? ''); ?></span><i class="icon-arrow-right a-icon" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($benefits && !$is_activities) : ?>
                    <ol class="m-overview__benefits">
                        <?php foreach ($benefits as $index => $benefit) : ?>
                            <li class="m-overview__benefit">
                                <div class="m-overview__benefit-inner a-underline">
                                    <?php
                                    $benefit_icon = $benefit['icon'] ?? null;
                                    if (!empty($benefit_icon)) :
                                        if (is_numeric($benefit_icon)) :
                                            echo wp_get_attachment_image($benefit_icon, 'thumbnail', false, ['loading' => 'lazy', 'decoding' => 'async', 'aria-hidden' => 'true', 'alt' => '']);
                                        elseif (is_array($benefit_icon) && !empty($benefit_icon['id'])) :
                                            echo wp_get_attachment_image($benefit_icon['id'], 'thumbnail', false, ['loading' => 'lazy', 'decoding' => 'async', 'aria-hidden' => 'true', 'alt' => '']);
                                        elseif (is_string($benefit_icon)) :
                                            echo '<img src="' . esc_url($benefit_icon) . '" alt="" loading="lazy" decoding="async" aria-hidden="true">';
                                        endif;
                                    else : ?>
                                        <i class="icon-star a-icon" aria-hidden="true"></i>
                                    <?php endif; ?>
                                    <span>
                                        <h3 class="a-text a-text--xxl h-semibold h-dark-blue"><?php echo esc_html($benefit['title'] ?? ''); ?></h3>
                                        <?php if (!empty($benefit['description'])) : ?>
                                            <p class="a-text a-text--l"><?php echo esc_html($benefit['description']); ?></p>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="m-overview__number" aria-hidden="true">
                                    <span><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
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

                <?php if ($statistic !== '' || $statistic_unit !== '' || $statistic_label !== '') : ?>
                    <div class="m-overview__statistic m-overview__statistic--<?php echo esc_attr($statistic_color); ?>">
                        <?php if ($statistic !== '' || $statistic_unit !== ''): ?>
                            <div class="m-overview__value">
                                <?php $unit_first = is_singular('project') && get_post_field('post_name', get_queried_object_id()) === 'jeta'; ?>
                                <?php if ($unit_first && $statistic_unit !== ''): ?><span class="h-medium a-heading a-heading--h6"><?php echo esc_html($statistic_unit); ?></span><?php endif; ?>
                                <?php if ($statistic !== ''): ?><span class="h-medium a-heading a-heading--h3 h-white"><?php echo esc_html($statistic); ?></span><?php endif; ?>
                                <?php if (!$unit_first && $statistic_unit !== ''): ?><span class="h-medium a-heading a-heading--h6"><?php echo esc_html($statistic_unit); ?></span><?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($statistic_label !== ''): ?><p class="a-text a-text--xs"><?php echo esc_html($statistic_label); ?></p><?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div id="<?php echo esc_attr($content_id); ?>"></div>
    <?php endif; ?>
</section>
