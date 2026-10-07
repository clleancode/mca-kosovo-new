<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-activities',
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
] = Helpers\get_block_typography();

$layout = get_field('activities_layout') === 'two' ? 'two' : 'three';
$show_arrows = get_field('activities_arrows') === 'show';
$items = array_slice(get_field('activities_items') ?: [], 0, $layout === 'two' ? 2 : 3);
$title = get_field('activities_title');
$description = get_field('activities_description');
?>
<section id="activities" class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <div class="m-activities__intro s-d-b-m s-m-b-s">
            <div class="m-activities__heading">
                <p class="a-badge h-blue"><?php echo esc_html(get_field('activities_label')); ?></p>
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                    <?php echo nl2br(esc_html($title)); ?>
                </<?php echo tag_escape($tag); ?>>
            </div>

            <?php if ($description) : ?>
                <div class="m-activities__description">
                    <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                        <?php echo nl2br(esc_html($description)); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($items) : ?>
            <div class="m-activities__grid m-activities__grid--<?php echo esc_attr($layout); ?><?php echo $show_arrows ? ' m-activities__grid--arrows' : ''; ?>">
                <?php foreach ($items as $index => $item) : ?>
                    <?php
                    $tone = in_array($item['tone'] ?? '', ['blue', 'green', 'red', 'purple', 'white'], true) ? $item['tone'] : 'blue';
                    $unit = $item['unit'] ?? '';
                    $label = $item['label'] ?? '';
                    ?>
                    <article class="m-activities__card">
                        <div class="m-activities__image">
                            <?php if ($show_arrows && $index < count($items) - 1): ?>
                                <span class="a-activities-arrow" aria-hidden="true"><img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/right.svg')); ?>" alt=""></span>
                            <?php endif; ?>
                            <?php if (!empty($item['image'])) : ?>
                                <?php echo wp_get_attachment_image($item['image'], 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                            <?php endif; ?>
                            <?php if ($label) : ?>
                                <span class="h-semibold a-activities-pill a-activities-pill--<?php echo esc_attr($tone); ?> a-text a-text--xs"><?php echo esc_html($label); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="m-activities__card-content">
                            <div class="m-activities__value">
                                <span class="a-heading a-heading--<?php echo $unit ? 'h1' : 'h5'; ?> h-white"><?php echo esc_html($item['value'] ?? ''); ?></span>
                                <?php if ($unit) : ?>
                                    <span class="a-heading a-heading--h6 h-medium h-white"><?php echo esc_html($unit); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="m-activities__card-title">
                                <h3 class="a-heading a-heading--h6 h-white"><?php echo esc_html($item['title'] ?? ''); ?></h3>
                            </div>

                            <?php if (!empty($item['description'])) : ?>
                                <div class="m-activities__card-description">
                                    <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                                        <?php echo nl2br(esc_html($item['description'])); ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($item['tags'])) : ?>
                                <ul class="m-activities__tags">
                                    <?php foreach (array_slice($item['tags'], 0, 3) as $row) : ?>
                                        <?php
                                        if (empty($row['tag'])) {
                                            continue;
                                        }
                                        ?>
                                        <li><span class="a-text a-text--xs"><?php echo esc_html($row['tag']); ?></span></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
