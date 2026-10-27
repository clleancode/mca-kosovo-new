<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-compact',
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

$title       = get_field('compact_title');
$description = get_field('compact_description');

[
    'header_tag'         => $heading_tag,
    'header_size'        => $heading_size,
    'header_color'       => $heading_color,
    'header_line_height' => $heading_height,
    'text_type'          => $text_size,
    'text_color'         => $text_color,
    'text_line_height'   => $text_height,
] = Helpers\get_block_typography();

$heading_classes = ['a-heading', 'a-heading--' . $heading_size, $heading_color, 'h--' . $heading_height];
$text_classes    = ['a-text', 'a-text--' . $text_size, $text_color, 'h--' . $text_height];

$link       = get_field('compact_link');
$image      = get_field('compact_image');
$statistics = get_field('compact_statistics') ?: [];

$us_share = max(0, min(100, (float) get_field('compact_us_share')));

$end_date   = get_field('compact_end_date');
$end        = $end_date ? date_create_immutable($end_date, wp_timezone()) : false;
$allowed_br = ['br' => []];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">

        <div class="m-compact__intro s-d-b-m s-m-b-s">
            <div class="m-content">
                <?php if (get_field('compact_label')) : ?>
                    <p class="a-badge h-dark-blue e-reveal e-reveal--text">
                        <?php echo esc_html(get_field('compact_label')); ?>
                    </p>
                <?php endif; ?>

                <?php if ($title) : ?>
                    <<?php echo tag_escape($heading_tag); ?> class="<?php echo esc_attr(implode(' ', array_filter($heading_classes))); ?> e-reveal e-reveal--text" data-delay="100">
                        <?php echo nl2br(esc_html($title)); ?>
                    </<?php echo tag_escape($heading_tag); ?>>
                <?php endif; ?>
            </div>

            <div class="m-content s-d-t-m s-m-t-xs">
                <?php if ($description) : ?>
                    <p class="<?php echo esc_attr(implode(' ', array_filter($text_classes))); ?> e-reveal e-reveal--text" data-delay="350">
                        <?php echo nl2br(esc_html($description)); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($link['url'])) : ?>
                    <a
                        class="a-underline a-text a-text--l e-reveal"
                        data-delay="500"
                        href="<?php echo esc_url($link['url']); ?>"
                        target="<?php echo esc_attr(($link['target'] ?? '') ?: '_self'); ?>"
                        <?php if (($link['target'] ?? '') === '_blank') : ?>
                            rel="noopener noreferrer"
                        <?php endif; ?>
                    >
                        <span><?php echo esc_html($link['title'] ?? ''); ?></span>
                        <i class="icon-arrow-right a-icon" aria-hidden="true"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="m-compact__investment e-reveal e-reveal--image">
            <?php
            if ($image) {
                echo wp_get_attachment_image($image, 'full', false, [
                    'class'    => 'a-img',
                    'loading'  => 'lazy',
                    'decoding' => 'async',
                ]);
            }
            ?>

            <?php if (get_field('compact_image_label')) : ?>
                <div class="m-compact__image-label">
                    <span class="a-text a-text--xs">
                        <?php echo esc_html(get_field('compact_image_label')); ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if ($statistics) : ?>
                <div class="m-compact__statistics">
                    <?php foreach ($statistics as $stat_index => $statistic) : ?>
                        <?php $accent = sanitize_hex_color($statistic['accent'] ?? ''); ?>
                        <div
                            class="m-compact__statistic e-reveal"
                            data-delay="<?php echo esc_attr(800 + $stat_index * 150); ?>"
                            <?php if ($accent) : ?>
                                style="--compact-accent: <?php echo esc_attr($accent); ?>"
                            <?php endif; ?>
                        >
                            <div class="m-compact__statistic-value">
                                <span class="a-heading a-heading--h2 h-white">
                                    <?php echo wp_kses($statistic['value'] ?? '', $allowed_br); ?>
                                </span>
                                <span class="a-heading a-heading--h6 h-white">
                                    <?php echo wp_kses($statistic['unit'] ?? '', $allowed_br); ?>
                                </span>
                            </div>
                            <p class="a-text a-text--l">
                                <?php echo wp_kses($statistic['description'] ?? '', $allowed_br); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (get_field('compact_total')) : ?>
            <div class="m-compact__funding s-d-t-s s-m-t-xs s-d-b-l s-m-b-s">
                <div class="m-compact__funding-labels s-d-b-s s-m-b-xs">
                    <div class="m-compact__funding-total">
                        <p class="a-text a-text--xs h-semibold e-reveal e-reveal--text">Total Compact investment</p>
                        <p class="a-heading a-heading--h4 h-semibold e-reveal e-reveal--text" data-delay="100">
                            <?php echo esc_html(get_field('compact_total')); ?>
                        </p>
                    </div>

                    <div class="m-compact__funding-donors">
                        <div class="m-compact__donor m-compact__donor--us e-reveal" data-delay="250">
                            <p class="a-text a-text--xs h-semibold">United States</p>
                            <div>
                                <span class="a-heading a-heading--h6 h-medium">
                                    <?php echo esc_html(get_field('compact_us_amount')); ?>
                                </span>
                                <span class="a-text a-text--s h-semibold">
                                    <?php echo esc_html($us_share); ?>%
                                </span>
                            </div>
                        </div>

                        <div class="m-compact__donor m-compact__donor--kosovo e-reveal" data-delay="370">
                            <p class="a-text a-text--xs h-semibold">Kosovo</p>
                            <div>
                                <span class="a-heading a-heading--h6 h-medium">
                                    <?php echo esc_html(get_field('compact_kosovo_amount')); ?>
                                </span>
                                <span class="a-text a-text--s h-semibold">
                                    <?php echo esc_html(100 - $us_share); ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="m-compact__funding-bar e-reveal e-reveal--clip"
                    role="img"
                    aria-label="<?php echo esc_attr(sprintf('United States %s%%; Kosovo %s%%', $us_share, 100 - $us_share)); ?>"
                >
                    <?php foreach (['mobile' => 25, 'desktop' => 100] as $device => $cell_count) : ?>
                        <?php $red_cells = (int) round($us_share * $cell_count / 100); ?>
                        <?php for ($cell = 0; $cell < $cell_count; $cell++) : ?>
                            <span
                                class="a-compact-cell a-compact-cell--<?php echo esc_attr($device); ?><?php echo $cell >= $red_cells ? ' a-compact-cell--kosovo' : ''; ?>"
                                <?php if ($cell < $red_cells) : ?>
                                    style="opacity: <?php echo esc_attr(.35 + .65 * $cell / max(1, $red_cells - 1)); ?>"
                                <?php endif; ?>
                                aria-hidden="true"
                            ></span>
                        <?php endfor; ?>
                    <?php endforeach; ?>
                </div>

                <div class="m-compact__funding-notes s-d-t-s s-m-t-xs">
                    <p class="a-text a-text--s e-reveal e-reveal--text" data-delay="500">
                        <?php echo esc_html(get_field('compact_cell_note')); ?>
                    </p>
                    <p class="a-text a-text--s e-reveal e-reveal--text" data-delay="650">
                        <?php echo esc_html(get_field('compact_funding_note')); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($end) : ?>
            <div
                class="m-compact__countdown"
                data-compact-end="<?php echo esc_attr($end->format(DATE_ATOM)); ?>"
            >
                <div class="m-compact__countdown-content">
                    <p class="a-text a-text--xs a-compact__live h-semibold s-d-b-xs s-m-b-xs e-reveal e-reveal--text">Compact countdown · Live</p>
                    <h3 class="a-text a-text--xxl e-reveal e-reveal--text" data-delay="100">
                        <?php echo esc_html(get_field('compact_countdown_title')); ?>
                    </h3>
                </div>

                <div class="m-compact__clock" role="timer" aria-label="Time remaining until Compact completion">
                    <?php foreach (['years', 'months', 'days', 'hours', 'minutes', 'seconds'] as $unit_index => $unit) : ?>
                        <div class="m-compact__time e-reveal" data-delay="<?php echo esc_attr(250 + $unit_index * 100); ?>">
                            <span class="a-heading a-heading--h3" data-compact-unit="<?php echo esc_attr($unit); ?>">00</span>
                            <span class="a-text a-text--xs"><?php echo esc_html(ucfirst($unit)); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>