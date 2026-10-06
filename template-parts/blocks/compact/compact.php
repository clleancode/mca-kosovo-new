<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();
if (!$visibility['show']) return;

$classes = ['o-compact', $visibility['visibility_classes'], $block['className'] ?? ''];
foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");
        if ($space) $classes[] = "s-{$prefix}-{$suffix}-{$space}";
    }
}

$title = get_field('compact_title');
$description = get_field('compact_description');

$heading_tag = get_field('header_tag') ?: 'h2';
if (!in_array($heading_tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'], true)) $heading_tag = 'h2';
$heading_classes = ['a-heading', 'a-heading--' . (get_field('header_size') ?: 'h3'), get_field('header_color')];
if (get_field('header_line_height')) $heading_classes[] = 'h-text-l h--' . get_field('header_line_height');

$text_classes = ['a-text', 'a-text--' . (get_field('text_type') ?: 'xl'), get_field('text_color'), 's-d-t-m'];
if (get_field('text_line_height')) $text_classes[] = 'h-text-l h--' . get_field('text_line_height');

$link = get_field('compact_link');
$image = get_field('compact_image');
$statistics = get_field('compact_statistics') ?: [];
$milestones = get_field('compact_milestones') ?: [];
$us_share = max(0, min(100, (float) (get_field('compact_us_share') ?? 85)));
$end_date = get_field('compact_end_date');
$end = $end_date ? date_create_immutable($end_date, wp_timezone()) : false;
$countdown_image = get_field('compact_countdown_image');
$allowed_br = ['br' => []];
?>
<section <?php if (!empty($block['anchor'])): ?>id="<?php echo esc_attr($block['anchor']); ?>" <?php endif; ?>class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">

        <div class="m-compact__intro">
            <div class="m-content">
                <?php if (get_field('compact_label')): ?>
                    <p class="a-text a-text--s a-compact__eyebrow"><?php echo esc_html(get_field('compact_label')); ?></p>
                <?php endif; ?>
                <?php if ($title): ?>
                    <<?php echo tag_escape($heading_tag); ?> class="<?php echo esc_attr(implode(' ', array_filter($heading_classes))); ?>"><?php echo nl2br(esc_html($title)); ?></<?php echo tag_escape($heading_tag); ?>>
                <?php endif; ?>
            </div>

            <div class="m-content s-d-t-m s-m-t-s">
                <?php if ($description): ?>
                    <p class="<?php echo esc_attr(implode(' ', array_filter($text_classes))); ?>"><?php echo nl2br(esc_html($description)); ?></p>
                <?php endif; ?>
                <?php if (!empty($link['url'])): ?>
                    <a class="a-text a-text--m" href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr(($link['target'] ?? '') ?: '_self'); ?>"<?php if (($link['target'] ?? '') === '_blank') echo ' rel="noopener noreferrer"'; ?>>
                        <?php echo esc_html(($link['title'] ?? '') ?: 'How the Compact works'); ?>
                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/small-right.svg')); ?>" alt="" aria-hidden="true">
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="m-compact__investment">
            <?php if ($image) echo wp_get_attachment_image($image, 'full', false, ['class' => 'a-img', 'loading' => 'lazy', 'decoding' => 'async']); ?>

            <?php if (get_field('compact_image_label')): ?>
                <p class="a-text a-text--xs"><?php echo esc_html(get_field('compact_image_label')); ?></p>
            <?php endif; ?>

            <?php if ($statistics): ?>
                <div class="m-compact__statistics">
                    <?php foreach ($statistics as $statistic): ?>
                        <div class="m-compact__statistic" style="--compact-accent: <?php echo esc_attr(sanitize_hex_color($statistic['accent'] ?? '') ?: '#2F358B'); ?>">
                            <p class="a-text"><?php echo wp_kses($statistic['value'] ?? '', $allowed_br); ?><span><?php echo wp_kses($statistic['unit'] ?? '', $allowed_br); ?></span></p>
                            <p class="a-text a-text--l"><?php echo wp_kses($statistic['description'] ?? '', $allowed_br); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (get_field('compact_total')): ?>
            <div class="m-compact__funding">
                <div class="m-compact__funding-labels">
                    <p class="a-text a-text--xs h-semibold">Total Compact investment · <?php echo esc_html(get_field('compact_total')); ?></p>
                    <p class="a-text a-text--xs h-semibold">U.S. <?php echo esc_html($us_share); ?>% / Kosovo <?php echo esc_html(100 - $us_share); ?>%</p>
                </div>
                <meter class="m-compact__funding-bar" min="0" max="100" value="<?php echo esc_attr($us_share); ?>" aria-label="U.S. share of Compact investment"><?php echo esc_html($us_share); ?>%</meter>
            </div>
        <?php endif; ?>

        <?php if ($milestones): ?>
            <div class="m-compact__timeline">
                <p class="a-text a-text--xs a-compact__caption">Compact timeline</p>
                <ol class="m-compact__milestones">
                    <?php foreach ($milestones as $milestone):
                        $status = in_array($milestone['status'] ?? '', ['complete', 'active', 'upcoming'], true) ? $milestone['status'] : 'upcoming'; ?>
                        <li class="m-compact__milestone m-compact__milestone--<?php echo esc_attr($status); ?>">
                            <p class="a-text a-text--s a-compact__year">
                                <?php echo esc_html($milestone['year'] ?? ''); ?>
                                <?php if ($status === 'active'): ?>
                                    <span class="a-text a-text--xs a-compact__status">We are here</span>
                                <?php endif; ?>
                            </p>
                            <h3 class="a-text a-text--xxl h-semibold s-d-b-xs s-m-b-xs"><?php echo wp_kses($milestone['title'] ?? '', $allowed_br); ?></h3>
                            <p class="a-text a-text--m"><?php echo wp_kses($milestone['description'] ?? '', $allowed_br); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        <?php endif; ?>

        <?php if ($end): ?>
            <div class="m-compact__countdown" data-compact-end="<?php echo esc_attr($end->format(DATE_ATOM)); ?>">
                <?php if ($countdown_image) echo wp_get_attachment_image($countdown_image, 'large', false, ['class' => 'a-img', 'loading' => 'lazy', 'decoding' => 'async']); ?>

                <div class="m-content">
                    <p class="a-text a-text--xs a-compact__live">Compact countdown · Live</p>
                    <h3 class="a-text a-text--xxl"><?php echo esc_html(get_field('compact_countdown_title')); ?></h3>
                </div>

                <div class="m-compact__clock" role="timer" aria-label="Time remaining until Compact completion">
                    <?php foreach (['years', 'months', 'days', 'hours', 'minutes', 'seconds'] as $unit): ?>
                        <div class="m-compact__time">
                            <span class="a-heading a-heading--h3" data-compact-unit="<?php echo esc_attr($unit); ?>">00</span>
                            <span class="a-text a-text--xs"><?php echo esc_html(ucfirst($unit)); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>
