<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-timeline',
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
] = Helpers\get_block_typography();

$items        = array_slice(get_field('timeline_milestones') ?: [], 0, 4);
$active_index = null;
$progress     = 0;

foreach ($items as $index => $item) {
    if (($item['status'] ?? '') === 'active') {
        $active_index = $index;
        break;
    }

    if (($item['status'] ?? '') === 'complete') {
        $progress = ($index + 1) * 25;
    }
}

if ($active_index !== null) {
    $progress = $active_index * 25;
}

$label        = get_field('timeline_label');
$title        = get_field('timeline_title');
$description  = get_field('timeline_description');
$active_label = get_field('timeline_active_label');
?>
<section
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-timeline">
            <div class="m-timeline__intro">
                <div class="m-timeline__heading">
                    <?php if ($label) : ?>
                        <p class="a-badge h-white"><?php echo esc_html($label); ?></p>
                    <?php endif; ?>
                    <?php if ($title) : ?>
                        <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                            <?php echo esc_html($title); ?>
                        </<?php echo tag_escape($tag); ?>>
                    <?php endif; ?>
                    <?php if ($description) : ?>
                        <div class="m-timeline__description s-d-t-xs s-m-t-xs">
                            <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                                <?php echo nl2br(esc_html($description)); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
                <ul class="m-timeline__legend s-m-t-xs" aria-label="Milestone statuses">
                    <?php foreach (['complete' => 'Completed', 'active' => 'In progress', 'upcoming' => 'Upcoming'] as $state => $legend_label) : ?>
                        <li class="m-timeline__legend-item m-timeline__legend-item--<?php echo esc_attr($state); ?>">
                            <span class="a-text a-text--xs"><?php echo esc_html($legend_label); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php if ($items) : ?>
                <div
                    class="m-timeline__board<?php echo $active_index !== null ? ' m-timeline__board--active' : ''; ?>"
                    style="--timeline-progress: <?php echo esc_attr($progress); ?>%;"
                >
                    <img
                        class="m-timeline__decoration"
                        src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/timeline.svg')); ?>"
                        alt=""
                        aria-hidden="true"
                    >
                    <div class="m-timeline__line" aria-hidden="true">
                        <span><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.16406 10H15.8307M10.8307 15L15.8307 10L10.8307 5" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    </div>
                    <ol class="m-timeline__milestones">
                        <?php foreach ($items as $item) : ?>
                            <?php
                            $status = in_array($item['status'] ?? '', ['complete', 'active', 'upcoming'], true)
                                ? $item['status']
                                : 'upcoming';
                            ?>
                            <li class="m-timeline__milestone m-timeline__milestone--<?php echo esc_attr($status); ?>">
                                <div class="m-timeline__copy">
                                    <div class="m-timeline__year">
                                        <span class="a-heading a-heading--h2 <?php echo $status === 'active' ? 'h-pink' : 'h-white'; ?>">
                                            <?php echo esc_html($item['year'] ?? ''); ?>
                                        </span>
                                    </div>
                                    <?php if ($status === 'active' && $active_label) : ?>
                                        <div class="m-timeline__pill">
                                            <span class="a-text a-text--xs"><?php echo esc_html($active_label); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['date'])) : ?>
                                        <div class="m-timeline__date">
                                            <span class="a-text a-text--xs"><?php echo esc_html($item['date']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <h3 class="a-heading a-heading--h6 h-white">
                                        <?php echo esc_html($item['title'] ?? ''); ?>
                                    </h3>
                                    <?php if (!empty($item['description'])) : ?>
                                        <p class="a-text a-text--l <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                                            <?php echo nl2br(esc_html($item['description'])); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <span
                                    class="m-timeline__marker"
                                    aria-label="<?php echo esc_attr(['complete' => 'Completed', 'active' => 'In progress', 'upcoming' => 'Upcoming'][$status]); ?>"
                                >
                                    <?php if ($status === 'complete') : ?>
                                        <svg width="12" height="12" viewBox="0 0 12 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.875 4.54167L3.875 7.54167L10.2083 0.875" stroke="#000D29" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <?php endif; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>