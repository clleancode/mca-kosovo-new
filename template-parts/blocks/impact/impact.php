<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();
if (!$visibility['show']) return;

$classes = ['o-impact', $visibility['visibility_classes'], $block['className'] ?? ''];
foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");
        if ($space) $classes[] = "s-{$prefix}-{$suffix}-{$space}";
    }
}

$label = get_field('impact_label') ?: 'Impact';
$title = get_field('impact_title') ?: '';
$description = get_field('impact_description') ?: '';
$items = array_slice(get_field('impact_items') ?: [], 0, 4);

$setting = static function ($field, $allowed, $default) {
    $value = get_field($field);
    return in_array($value, $allowed, true) ? $value : $default;
};

$colors = ['h-white', 'h-dark-blue', 'h-blue', 'h-purple', 'h-dark-green'];

$heading_tag = $setting('header_tag', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'], 'h2');
$heading_size = $setting('header_size', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], 'h4');
$heading_color = $setting('header_color', $colors, 'h-dark-blue');
$heading_height = $setting('header_line_height', ['3xs', '2xs', 'xs', 's', 'm', 'ls', 'l', 'xl'], 's');

$text_type = $setting('text_type', ['xs', 's', 'm', 'l', 'xl', 'xxl'], 'm');
$text_color = $setting('text_color', $colors, 'h-dark-blue');
$text_height = $setting('text_line_height', ['3xs', '2xs', 'xs', 's', 'm', 'l', 'xl'], 'l');
?>
<section <?php if (!empty($block['anchor'])): ?>id="<?php echo esc_attr($block['anchor']); ?>" <?php endif; ?>class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <div class="m-impact">

            <div class="m-impact__intro s-d-b-l s-m-b-s">
                <div class="m-impact__heading">
                    <p class="a-badge h-dark-blue"><?php echo esc_html($label); ?></p>
                    <<?php echo tag_escape($heading_tag); ?> class="a-heading a-heading--<?php echo esc_attr($heading_size); ?> <?php echo esc_attr($heading_color); ?> h--<?php echo esc_attr($heading_height); ?>"><?php echo nl2br(esc_html($title)); ?></<?php echo tag_escape($heading_tag); ?>>
                </div>
                <div class="m-impact__description">
                    <p class="a-text a-text--<?php echo esc_attr($text_type); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>"><?php echo nl2br(esc_html($description)); ?></p>
                </div>
            </div>

            <?php if ($items): ?>
                <div class="m-impact__grid">
                    <?php foreach ($items as $index => $item): ?>
                        <article class="m-impact__card">
                            <div class="m-impact__image">
                                <?php if (!empty($item['image'])) echo wp_get_attachment_image($item['image'], 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                            </div>

                            <div class="m-impact__number s-d-t-xs s-d-b-xs s-m-t-xs s-m-b-xs">
                                <span class="a-text a-text--xs h-semibold"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                            </div>

                            <div class="m-impact__card-title">
                                <h3 class="a-heading a-heading--h6 h-dark-blue h-semibold"><?php echo esc_html($item['title'] ?? ''); ?></h3>
                            </div>

                            <?php if (!empty($item['description'])): ?>
                                <div class="m-impact__card-description s-d-t-s s-m-t-xs">
                                    <p class="a-text a-text--<?php echo esc_attr($text_type); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>"><?php echo nl2br(esc_html($item['description'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>