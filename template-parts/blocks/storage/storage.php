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

[
    'header_tag' => $heading_tag,
    'header_size' => $heading_size,
    'header_color' => $heading_color,
    'header_line_height' => $heading_line_height,
    'text_type' => $text_type,
    'text_color' => $text_color,
    'text_line_height' => $text_line_height,
] = Helpers\get_block_typography([
    'header_size' => 'h3',
    'header_color' => 'h-dark-blue',
    'text_type' => 's',
    'text_color' => 'h-dark-blue',
]);
?>

<section
    id="sites"
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-storage">
            <div class="m-storage__content">
                <?php if ($label): ?>
                    <p class="a-badge h-dark-blue h-semibold"><?php echo esc_html($label); ?></p>
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

                <?php foreach ($locations as $index => $location):
                    $location_capacity = trim((string) ($location['capacity'] ?? ''));
                    preg_match('/^([\d.,]+)\s*(.*)$/u', $location_capacity, $capacity_parts);
                    $location_value = $capacity_parts[1] ?? $location_capacity;
                    $location_unit = ($capacity_parts[2] ?? '') ?: ($unit ?: 'MWh');
                    $numeric_capacity = (float) str_replace(',', '', $capacity_parts[1] ?? '0');
                    $total_capacity = (float) str_replace(',', '', (string) $capacity);
                    $percentage = $total_capacity > 0 ? max(0, min(100, round($numeric_capacity / $total_capacity * 100))) : 0;
                    $filled_cells = (int) round($percentage * 34 / 100);
                    ?>
                    <div class="m-storage__location">
                        <div class="m-storage__location-heading">
                            <div class="m-storage__site">
                                <span class="a-text a-text--xs h-pink h-semibold">Site <?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                                <h3 class="a-heading a-heading--h6 h-dark-blue"><?php echo esc_html($location['name'] ?? ''); ?></h3>
                            </div>
                            <div class="m-storage__location-capacity">
                                <span class="a-heading a-heading--h4 h-dark-blue"><?php echo esc_html($location_value); ?></span>
                                <span class="a-text a-text--m h-blue h-semibold"><?php echo esc_html($location_unit); ?></span>
                            </div>
                        </div>
                        <div class="m-storage__capacity-bar" role="img" aria-label="<?php echo esc_attr($percentage . '% of total storage capacity'); ?>">
                            <?php for ($cell = 0; $cell < 34; $cell++): ?><span<?php if ($cell < $filled_cells): ?> class="a-storage-cell--filled" style="opacity: <?php echo esc_attr(.55 + .45 * $cell / max(1, $filled_cells - 1)); ?>;"<?php endif; ?> aria-hidden="true"></span><?php endfor; ?>
                        </div>
                        <div class="m-storage__location-notes">
                            <?php if (!empty($location['note'])): ?><p class="a-text a-text--m"><?php echo esc_html($location['note']); ?></p><?php endif; ?>
                            <?php if ($total_capacity > 0): ?><span class="a-text a-text--xs h-semibold"><?php echo esc_html($percentage . '% of ' . $capacity . ' ' . ($unit ?: 'MWh')); ?></span><?php endif; ?>
                        </div>
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