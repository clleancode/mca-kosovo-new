<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-our-office',
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

$label = get_field('our_office_label');
$title = get_field('our_office_title');
$address = get_field('our_office_address');
$map_image = get_field('our_office_map_image');
$map_image_id = is_array($map_image) ? absint($map_image['ID'] ?? $map_image['id'] ?? 0) : absint($map_image);
$link = get_field('our_office_link') ?: [];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-our-office">
            <div class="m-our-office__content">
                <?php if ($label) : ?>
                    <div class="m-our-office__eyebrow">
                        <p class="a-text a-text--s e-reveal e-reveal--text"><?php echo esc_html($label); ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($title) : ?>
                    <h2 class="a-heading a-heading--h3 e-reveal e-reveal--text" data-delay="100"><?php echo wp_kses(nl2br($title), ['br' => []]); ?></h2>
                <?php endif; ?>
                <?php if ($address) : ?>
                    <p class="a-text a-text--xl e-reveal e-reveal--text" data-delay="350"><?php echo nl2br(esc_html($address)); ?></p>
                <?php endif; ?>
                <?php if (!empty($link['url']) && !empty($link['title'])) : ?>
                    <a class="a-btn a-btn--white a-btn--smallest e-reveal" data-delay="500" href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr($link['target'] ?? ''); ?>" <?php if (($link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
                        <span class="a-text a-text--m">
                            <?php echo esc_html($link['title']); ?>
                            <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                        </span>
                    </a>
                <?php endif; ?>
            </div>
            <div class="m-our-office__map e-reveal e-reveal--image" data-delay="250">
                <?php if ($map_image_id) : ?>
                    <?php echo wp_get_attachment_image($map_image_id, 'large', false, ['alt' => '', 'aria-hidden' => 'true', 'loading' => 'lazy']); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
