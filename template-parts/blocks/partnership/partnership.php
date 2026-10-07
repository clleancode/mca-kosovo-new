<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-partnership',
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

$value = static function ($name) {
    return get_field('partnership_' . $name);
};

[
    'header_tag' => $tag,
    'header_size' => $size,
    'header_color' => $heading_color,
    'header_line_height' => $heading_height,
    'text_type' => $text_size,
    'text_color' => $text_color,
    'text_line_height' => $text_height,
] = Helpers\get_block_typography();

$main_image = $value('main_image');
$side_image = $value('side_image');
$logo = $value('logo');
$partners = $value('partners') ?: [];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-partnership__intro s-d-b-m s-m-b-s">
            <div class="m-partnership__heading">
                <p class="a-badge h-blue"><?php echo esc_html($value('label')); ?></p>
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($heading_color); ?> h--<?php echo esc_attr($heading_height); ?> h-medium">
                    <?php echo nl2br(esc_html($value('title'))); ?>
                </<?php echo tag_escape($tag); ?>>
            </div>
            <div class="m-partnership__description">
                <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                    <?php echo nl2br(esc_html($value('description'))); ?>
                </p>
            </div>
        </div>

        <div class="m-partnership__images">
            <div class="m-partnership__main-image">
                <?php if ($main_image) : ?>
                    <?php echo wp_get_attachment_image($main_image, 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                <?php endif; ?>
                <div class="m-partnership__caption">
                    <h3 class="a-heading a-heading--h5 h-white"><?php echo esc_html($value('image_caption')); ?></h3>
                </div>
            </div>
            <div class="m-partnership__side-image">
                <?php if ($side_image) : ?>
                    <?php echo wp_get_attachment_image($side_image, 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="m-partnership__funding s-d-t-m s-m-t-s s-d-b-m s-m-b-s">
            <div class="m-partnership__partner">
                <p class="a-text a-text--xs"><?php echo esc_html($value('funding_label')); ?></p>
                <h3 class="a-heading a-heading--h5 h-white"><?php echo esc_html($value('funding_name')); ?></h3>
                <p class="a-text a-text--l"><?php echo esc_html($value('funding_description')); ?></p>
                <p class="a-heading a-heading--h3 h-white"><?php echo esc_html($value('funding_amount')); ?></p>
            </div>

            <div class="m-partnership__total">
                <div class="m-partnership__connector" aria-hidden="true"></div>
                <p class="a-text a-text--xs h-blue"><?php echo esc_html($value('total_label')); ?></p>
                <p class="a-heading a-heading--h6 h-white"><?php echo esc_html($value('total_amount')); ?></p>
            </div>

            <div class="m-partnership__partner m-partnership__partner--government">
                <p class="a-text a-text--xs"><?php echo esc_html($value('government_label')); ?></p>
                <h3 class="a-heading a-heading--h5 h-white"><?php echo esc_html($value('government_name')); ?></h3>
                <p class="a-text a-text--l"><?php echo esc_html($value('government_description')); ?></p>
                <p class="a-heading a-heading--h3 h-white"><?php echo esc_html($value('government_amount')); ?></p>
            </div>
        </div>

        <div class="m-partnership__implementation">
            <?php if ($logo) : ?>
                <div class="m-partnership__logo">
                    <?php echo wp_get_attachment_image($logo, 'medium', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                </div>
            <?php endif; ?>
            <div class="m-partnership__institution">
                <p class="a-text a-text--xs"><?php echo esc_html($value('implemented_label')); ?></p>
                <h3 class="a-heading a-heading--h6 h-white"><?php echo esc_html($value('implemented_name')); ?></h3>
                <p class="a-text a-text--l"><?php echo nl2br(esc_html($value('implemented_description'))); ?></p>
            </div>
        </div>

        <div class="m-partnership__partners s-d-t-m s-m-t-s">
            <p class="a-text a-text--xs"><?php echo esc_html($value('partners_label')); ?></p>
            <ul class="m-partnership__partners-list">
                <?php foreach ($partners as $partner) : ?>
                    <li><span class="a-text a-text--xxl"><?php echo esc_html($partner['name'] ?? ''); ?></span></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>