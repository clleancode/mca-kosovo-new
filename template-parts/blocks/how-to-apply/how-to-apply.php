<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-how-to-apply',
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
    'header_tag' => 'h2',
    'header_size' => 'h6',
    'header_color' => 'h-dark-blue',
    'header_line_height' => 's',
    'text_type' => 'xs',
    'text_color' => 'h-dark-blue',
    'text_line_height' => 'l',
]);

$steps = array_slice(get_field('how_to_apply_steps') ?: [
    [
        'title'       => 'Find a vacancy',
        'description' => 'Check open positions or subscribe to job alerts.',
    ],
    [
        'title'       => 'Prepare your documents',
        'description' => 'Your resume and the completed Job Application Form (PDF or DOC, max 2MB each).',
    ],
    [
        'title'       => 'Apply online',
        'description' => 'Submit through the application form before the deadline.',
    ],
    [
        'title'       => 'Hear back from us',
        'description' => 'Shortlisted candidates are contacted for the next steps.',
    ],
], 0, 4);
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-how-to-apply">
            <<?php echo tag_escape($tag); ?> class="m-how-to-apply__title a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                <?php echo esc_html(get_field('how_to_apply_title') ?: 'How to apply'); ?>
            </<?php echo tag_escape($tag); ?>>

            <?php if ($steps) : ?>
                <ol class="m-how-to-apply__steps">
                    <?php foreach ($steps as $index => $step) : ?>
                        <li class="m-how-to-apply__card<?php echo $index === 3 ? ' m-how-to-apply__card--last' : ''; ?>">
                            <span class="m-how-to-apply__number a-text a-text--s" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                            <div class="m-how-to-apply__copy">
                                <h3 class="a-heading a-heading--h6 h-semibold h-dark-blue"><?php echo esc_html($step['title'] ?? ''); ?></h3>
                                <?php if (!empty($step['description'])) : ?>
                                    <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                                        <?php echo nl2br(esc_html($step['description'])); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </div>
    </div>
</section>
