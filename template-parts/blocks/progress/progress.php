<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-progress',
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

$milestones = array_slice(get_field('progress_milestones') ?: [], 0, 6);
$percentage = max(0, min(100, (float) get_field('progress_percentage')));
$phase = get_field('progress_phase');
$active_label = get_field('progress_active_label');

$status_labels = [
    'complete' => 'Completed',
    'active'   => 'In progress',
    'upcoming' => 'Upcoming',
];
?>
<section id="progress" class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <div class="m-progress__intro s-d-b-m s-m-b-s">
            <div class="m-progress__heading">
                <p class="a-badge h-dark-blue h-semibold s-d-b-xs"><?php echo esc_html(get_field('progress_label')); ?></p>
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                    <?php echo esc_html(get_field('progress_title')); ?>
                </<?php echo tag_escape($tag); ?>>
            </div>

            <div class="m-progress__phase">
                <div class="m-progress__phase-labels">
                    <span class="a-text a-text--xs h-semibold"><?php echo esc_html(get_field('progress_phase_label')); ?></span>
                    <span class="a-text a-text--xs h-semibold h-dark-blue"><?php echo esc_html($phase); ?></span>
                </div>
                <progress class="a-progress-bar" max="100" value="<?php echo esc_attr($percentage); ?>" aria-label="<?php echo esc_attr($phase); ?>">
                    <?php echo esc_html($percentage); ?>%
                </progress>
            </div>
        </div>

        <?php if ($milestones) : ?>
            <ol class="m-progress__milestones">
                <?php foreach ($milestones as $milestone) : ?>
                    <?php
                    $status = in_array($milestone['status'] ?? '', ['complete', 'active', 'upcoming'], true) ? $milestone['status'] : 'upcoming';
                    $description_color = $status === 'active' ? 'h-white' : $text_color;
                    ?>
                    <li class="m-progress__milestone m-progress__milestone--<?php echo esc_attr($status); ?>">
                        <div class="m-progress__card-top">
                            <span class="a-text a-text--xs h-semibold"><?php echo esc_html($milestone['date'] ?? ''); ?></span>
                            <span class="a-progress-status" aria-label="<?php echo esc_attr($status_labels[$status]); ?>">
                                <?php if ($status === 'complete') : ?>
                                    <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/tick.svg')); ?>" alt="" aria-hidden="true">
                                <?php endif; ?>
                            </span>
                        </div>

                        <h3 class="a-text a-text--xl s-d-t-xs s-d-b-xs s-m-t-xs s-m-b-xs h-semibold <?php echo $status === 'active' ? 'h-white' : 'h-dark-blue'; ?>">
                            <?php echo esc_html($milestone['title'] ?? ''); ?>
                        </h3>

                        <?php if (!empty($milestone['description'])) : ?>
                            <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($description_color); ?> h--<?php echo esc_attr($text_height); ?>">
                                <?php echo nl2br(esc_html($milestone['description'])); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($status === 'active' && $active_label) : ?>
                            <div class="m-progress__active-label s-d-t-xs s-m-t-xs">
                                <span class="a-progress-pill a-text a-text--xs"><?php echo esc_html($active_label); ?></span>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </div>
</section>