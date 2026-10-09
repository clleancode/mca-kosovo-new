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

$style = Helpers\get_field_choice('how_to_apply_style', ['standard', 'blue'], 'standard');
$classes[] = 'o-how-to-apply--' . $style;

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

$steps = array_slice(get_field('how_to_apply_steps') ?: [], 0, 4);

$contact_image = get_field('how_to_apply_contact_image');
$contact_label = get_field('how_to_apply_contact_label');
$contact_name = get_field('how_to_apply_contact_name');
$contact_role = get_field('how_to_apply_contact_role');
$contact_email = get_field('how_to_apply_contact_email');
$newsletter_title = get_field('how_to_apply_newsletter_title');
$newsletter_placeholder = get_field('how_to_apply_newsletter_placeholder');
$newsletter_shortcode = get_field('how_to_apply_newsletter_shortcode');
$newsletter_link = get_field('how_to_apply_newsletter_link') ?: [];
$newsletter_action = $newsletter_link['url'] ?? '';
$newsletter_button = $newsletter_link['title'] ?? '';
$section_title = get_field('how_to_apply_title');
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-how-to-apply">
            <?php if ($section_title) : ?>
                <<?php echo tag_escape($tag); ?> class="m-how-to-apply__title a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                    <?php echo esc_html($section_title); ?>
                </<?php echo tag_escape($tag); ?>>
            <?php endif; ?>

            <?php if ($steps) : ?>
                <ol class="m-how-to-apply__steps">
                    <?php foreach ($steps as $index => $step) : ?>
                        <li class="m-how-to-apply__card<?php echo $index === 3 ? ' m-how-to-apply__card--last' : ''; ?><?php echo $style === 'blue' && $index === 3 ? ' m-how-to-apply__card--blue' : ''; ?>">
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

            <?php if ($style === 'blue') : ?>
                <div class="m-how-to-apply__bottom">
                    <div class="m-how-to-apply__contact">
                        <?php if ($contact_image) : ?>
                            <?php echo wp_get_attachment_image($contact_image, 'thumbnail', false, ['class' => 'm-how-to-apply__contact-image', 'loading' => 'lazy', 'decoding' => 'async']); ?>
                        <?php endif; ?>
                        <div class="m-how-to-apply__contact-copy">
                            <?php if ($contact_label) : ?><p class="m-how-to-apply__contact-label a-text a-text--xs h-semibold"><?php echo esc_html($contact_label); ?></p><?php endif; ?>
                            <?php if ($contact_name) : ?><h3 class="a-heading a-heading--h6 h-semibold h-dark-blue"><?php echo esc_html($contact_name); ?></h3><?php endif; ?>
                            <?php if ($contact_role || $contact_email) : ?>
                                <p class="a-text a-text--m h-dark-blue">
                                    <?php echo esc_html($contact_role); ?><?php if ($contact_role && $contact_email) : ?> · <?php endif; ?><?php if ($contact_email) : ?><a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a><?php endif; ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <aside class="m-how-to-apply__newsletter">
                        <?php if ($newsletter_title) : ?><h3 class="a-heading a-heading--h6 h-white h-semibold"><?php echo esc_html($newsletter_title); ?></h3><?php endif; ?>
                        <?php if ($newsletter_shortcode) : ?>
                            <div class="m-how-to-apply__newsletter-form"><?php echo do_shortcode($newsletter_shortcode); ?></div>
                        <?php else : ?>
                            <form class="m-how-to-apply__signup" action="<?php echo esc_url($newsletter_action); ?>" method="get">
                                <input type="email" name="email" placeholder="<?php echo esc_attr($newsletter_placeholder); ?>" aria-label="Email address" autocomplete="email" required>
                                <?php if ($newsletter_button) : ?><button class="a-text a-text--m h-dark-blue" type="submit"><?php echo esc_html($newsletter_button); ?></button><?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </aside>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
