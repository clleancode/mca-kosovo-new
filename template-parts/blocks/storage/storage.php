<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();
if (!$visibility['show']) return;

$classes = ['o-storage', $visibility['visibility_classes'], $block['className'] ?? ''];

foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");
        if ($space) {
            $classes[] = "s-{$prefix}-{$suffix}-{$space}";
        }
    }
}

$label               = get_field('storage_label');
$capacity            = get_field('storage_capacity');
$unit                = get_field('storage_unit');
$heading             = get_field('storage_heading');
$description         = get_field('storage_description');
$map_image           = get_field('storage_map_image');
$locations           = get_field('storage_locations') ?: [];
$locations           = array_slice($locations, 0, 2);

$heading_tag         = get_field('header_tag');
if (!in_array($heading_tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'], true)) {
    $heading_tag = 'h2';
}

$heading_size        = get_field('header_size');
if (!in_array($heading_size, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
    $heading_size = 'h3';
}

$heading_color       = get_field('header_color');
if (!in_array($heading_color, ['h-white', 'h-dark-blue', 'h-blue', 'h-purple', 'h-dark-green'], true)) {
    $heading_color = 'h-dark-blue';
}

$heading_line_height = get_field('header_line_height');
if (!in_array($heading_line_height, ['3xs', '2xs', 'xs', 's', 'm', 'ls', 'l', 'xl'], true)) {
    $heading_line_height = 's';
}

$text_type           = get_field('text_type');
if (!in_array($text_type, ['xs', 's', 'm', 'l', 'xl', 'xxl'], true)) {
    $text_type = 's';
}

$text_color          = get_field('text_color');
if (!in_array($text_color, ['h-white', 'h-dark-blue', 'h-blue', 'h-purple', 'h-dark-green'], true)) {
    $text_color = 'h-dark-blue';
}

$text_line_height    = get_field('text_line_height');
if (!in_array($text_line_height, ['3xs', '2xs', 'xs', 's', 'm', 'l', 'xl'], true)) {
    $text_line_height = 'l';
}
?>

<section
    <?php if (!empty($block['anchor'])): ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-storage">
            <div class="m-storage__content">
                <?php if ($label): ?>
                    <p class="a-text a-text--s"><?php echo esc_html($label); ?></p>
                <?php endif; ?>

                <?php if ($capacity || $unit): ?>
                    <div class="m-storage__capacity s-d-b-s s-m-b-xs s-d-t-s s-m-t-xs">
                        <?php if ($capacity): ?>
                            <span class="h-dark-blue"><?php echo esc_html($capacity); ?></span>
                        <?php endif; ?>
                        <?php if ($unit): ?>
                            <span><?php echo esc_html($unit); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($heading): ?>
                    <<?php echo tag_escape($heading_tag); ?>
                        class="a-heading a-heading--<?php echo esc_attr($heading_size); ?> <?php echo esc_attr($heading_color); ?> h-text-l h--<?php echo esc_attr($heading_line_height); ?>"
                    >
                        <?php echo nl2br(esc_html($heading)); ?>
                    </<?php echo tag_escape($heading_tag); ?>>
                <?php endif; ?>

                <?php if ($description): ?>
                    <p class="a-text a-text--<?php echo esc_attr($text_type); ?> <?php echo esc_attr($text_color); ?> s-d-t-s s-m-t-xs s-d-b-s s-m-b-xs h-text-l h--<?php echo esc_attr($text_line_height); ?>">
                        <?php echo nl2br(esc_html($description)); ?>
                    </p>
                <?php endif; ?>

                <?php foreach ($locations as $index => $location): ?>
                    <div class="m-storage__location">
                        <div class="m-storage__location-heading">
                            <p class="a-text a-text--xl"><?php echo esc_html($location['name'] ?? ''); ?></p>
                            <p class="a-text a-text--xl"><?php echo esc_html($location['capacity'] ?? ''); ?></p>
                        </div>
                        <div class="m-storage__capacity-bar" aria-hidden="true">
                            <span style="--storage-capacity: <?php echo esc_attr($index === 0 ? '74%' : '28%'); ?>"></span>
                        </div>
                        <?php if (!empty($location['note'])): ?>
                            <p class="a-text a-text--m s-d-b-xs"><?php echo esc_html($location['note']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="m-storage__map">
                <?php if ($map_image): ?>
                    <?php echo wp_get_attachment_image($map_image, 'large', false, [
                        'class'    => 'm-storage__map-image',
                        'loading'  => 'lazy',
                        'decoding' => 'async',
                    ]); ?>
                <?php endif; ?>

                <ul class="m-storage__legend" aria-label="Map legend">
                    <li class="a-storage__legend-site a-text--xs h-semibold">Storage site</li>
                    <li class="a-storage__legend-capacity a-text--xs h-semibold">Ring sized by capacity</li>
                </ul>
            </div>
        </div>
    </div>
</section>