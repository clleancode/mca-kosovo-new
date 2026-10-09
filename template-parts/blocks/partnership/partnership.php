<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

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
        <div class="m-partnership__intro s-d-b-2xl s-m-b-s">
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

        <div class="m-partnership__funding s-d-t-l s-m-t-s s-d-b-l s-m-b-s">
            <div class="m-partnership__funding-top">
                <div class="m-partnership__funding-headline">
                    <p class="m-partnership__funding-label a-badge"><?php echo esc_html($value('total_label')); ?></p>
                    <p class="m-partnership__funding-total a-heading a-heading--h1 h-white"><?php echo esc_html($value('total_amount')); ?></p>
                </div>
                <p class="m-partnership__funding-desc a-text a-text--l h-white"><?php echo nl2br(esc_html($value('funding_description'))); ?></p>
            </div>

            <div class="m-partnership__bar" aria-hidden="true">
                <div class="m-partnership__bar-fill m-partnership__bar-fill--red" style="width:<?php echo esc_attr($value('funding_percent') ?: '85'); ?>%"></div>
                <div class="m-partnership__bar-fill m-partnership__bar-fill--blue" style="width:<?php echo esc_attr($value('government_percent') ?: '15'); ?>%"></div>
            </div>

            <div class="m-partnership__partners-row">
                <div class="m-partnership__partner">
                    <p class="m-partnership__partner-label a-text a-text--xs"><?php echo esc_html($value('funding_label')); ?></p>
                    <div class="m-partnership__partner-amount">
                        <span class="a-heading a-heading--h3 h-white"><?php echo esc_html($value('funding_amount')); ?></span>
                        <span class="m-partnership__badge m-partnership__badge--red a-text a-text--s"><?php echo esc_html($value('funding_percent') ?: '85'); ?>%</span>
                    </div>
                    <p class="m-partnership__partner-name a-heading a-heading--h6 h-white"><?php echo esc_html($value('funding_name')); ?></p>
                    <p class="m-partnership__partner-sub a-text a-text--m"><?php echo esc_html($value('funding_sub')); ?></p>
                </div>

                <div class="m-partnership__partner m-partnership__partner--government">
                    <p class="m-partnership__partner-label a-text a-text--xs"><?php echo esc_html($value('government_label')); ?></p>
                    <div class="m-partnership__partner-amount m-partnership__partner-amount--right">
                        <span class="m-partnership__badge m-partnership__badge--blue a-text a-text--s"><?php echo esc_html($value('government_percent') ?: '15'); ?>%</span>
                        <span class="a-heading a-heading--h3 h-white"><?php echo esc_html($value('government_amount')); ?></span>
                    </div>
                    <p class="m-partnership__partner-name a-heading a-heading--h6 h-white"><?php echo esc_html($value('government_name')); ?></p>
                    <p class="m-partnership__partner-sub a-text a-text--m"><?php echo esc_html($value('government_sub')); ?></p>
                </div>
            </div>
        </div>

        <div class="m-partnership__implementation">
            <div class="m-partnership__implementation-body">
                <?php if ($logo) : ?>
                    <div class="m-partnership__logo">
                        <?php echo wp_get_attachment_image($logo, 'medium', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                    </div>
                <?php endif; ?>
                <div class="m-partnership__institution">
                    <p class="m-partnership__implementation-label a-badge"><?php echo esc_html($value('implemented_label')); ?></p>
                    <h3 class="a-heading a-heading--h6 h-white"><?php echo esc_html($value('implemented_name')); ?></h3>
                    <p class="a-text a-text--l"><?php echo nl2br(esc_html($value('implemented_description'))); ?></p>
                </div>
                <?php $impl_link = $value('implemented_link'); ?>
                <?php if ($impl_link) : ?>
                    <a class="m-partnership__implementation-link" href="<?php echo esc_url($impl_link['url'] ?? ''); ?>" <?php echo !empty($impl_link['target']) ? 'target="' . esc_attr($impl_link['target']) . '"' : ''; ?> aria-label="<?php echo esc_attr($value('implemented_name')); ?>">
                        <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                    </a>
                <?php else : ?>
                    <div class="m-partnership__implementation-link" aria-hidden="true">
                        <i class="icon-arrow-right-up a-icon"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="m-partnership__partners s-d-t-m s-m-t-s">
            <div class="m-partnership__partners-header">
                <p class="a-badge"><?php echo esc_html($value('partners_label')); ?></p>
                <span class="m-partnership__partners-sep" aria-hidden="true"></span>
                <?php $partners_count = $value('partners_count'); if ($partners_count) : ?>
                    <span class="m-partnership__partners-count a-text a-text--s"><?php echo esc_html($partners_count); ?></span>
                <?php endif; ?>
            </div>
            <div class="m-partnership__partners-grid">
                <?php foreach ($partners as $partner) : ?>
                    <div class="m-partnership__partner-card">
                        <div class="m-partnership__partner-card-top">
                            <div class="m-partnership__partner-card-logo<?php echo !empty($partner['logo_wide']) ? ' m-partnership__partner-card-logo--wide' : ''; ?>">
                                <?php if (!empty($partner['image'])) : ?>
                                    <?php echo wp_get_attachment_image($partner['image'], 'thumbnail', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                                <?php elseif (!empty($partner['image_url'])) : ?>
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/' . ltrim($partner['image_url'], '/')); ?>" alt="<?php echo esc_attr($partner['name'] ?? ''); ?>" loading="lazy" decoding="async">
                                <?php elseif (!empty($partner['name'])) : ?>
                                    <span class="m-partnership__partner-card-initials a-text a-text--m h-semibold"><?php
                                        $words = explode(' ', $partner['name']);
                                        echo esc_html(strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : '')));
                                    ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($partner['link'])) : ?>
                                <a class="m-partnership__partner-card-link" href="<?php echo esc_url($partner['link']['url'] ?? ''); ?>" <?php echo !empty($partner['link']['target']) ? 'target="' . esc_attr($partner['link']['target']) . '"' : ''; ?> aria-label="<?php echo esc_attr($partner['name'] ?? ''); ?>">
                                    <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                </a>
                            <?php else : ?>
                                <div class="m-partnership__partner-card-link" aria-hidden="true">
                                    <i class="icon-arrow-right-up a-icon"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <p class="m-partnership__partner-card-label a-text a-text--xs"><?php echo esc_html($partner['partner_label'] ?? ''); ?></p>
                        <h4 class="m-partnership__partner-card-name a-heading a-heading--h6 h-white"><?php echo esc_html($partner['name'] ?? ''); ?></h4>
                        <?php if (!empty($partner['description'])) : ?>
                            <p class="m-partnership__partner-card-desc a-text a-text--m"><?php echo esc_html($partner['description']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>