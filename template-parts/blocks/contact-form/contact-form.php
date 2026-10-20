<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-contact-form',
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

$address = get_field('contact_address', 'option');
$email = get_field('contact_email', 'option');
$visit_label = get_field('contact_form_visit_label');
$email_label = get_field('contact_form_email_label');
$map_link = get_field('contact_form_directions_link') ?: get_field('footer_address_link', 'option') ?: [];
$social_links = get_field('contact_form_social_links') ?: get_field('social_media_links', 'option') ?: [];
$social_title = get_field('contact_form_social_title');
$fraud_link = get_field('contact_form_fraud_link') ?: get_field('footer_button', 'option') ?: [];
$form_shortcode = get_field('contact_form_shortcode');
$icon_files = [
    'icon-facebook' => 'Social · Facebook.png',
    'icon-linkedin' => 'Social · LinkedIn.png',
    'icon-instagram' => 'Social · Instagram.png',
    'icon-youtube' => 'Social · YouTube.png',
];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-contact-form">
            <div class="m-contact-form__main">
                <div class="m-contact-form__heading">
                    <h2 class="a-heading a-heading--h3">
                        <?php echo esc_html(get_field('contact_form_title')); ?>
                    </h2>
                    <?php if (get_field('contact_form_note')) : ?>
                        <p class="a-text a-text--m"><?php echo esc_html(get_field('contact_form_note')); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($form_shortcode) : ?>
                    <div class="m-contact-form__form m-form">
                        <?php echo do_shortcode($form_shortcode); ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="m-contact-form__sidebar">
                <?php if ($address) : ?>
                    <article class="m-contact-form__panel m-contact-form__visit">
                        <span class="m-contact-form__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        </span>
                        <div class="m-contact-form__panel-copy">
                            <?php if ($visit_label) : ?>
                                <?php if (!empty($map_link['url'])) : ?>
                                    <a class="a-text a-text--xs" href="<?php echo esc_url($map_link['url']); ?>" target="<?php echo esc_attr($map_link['target'] ?? '_self'); ?>" <?php if (($map_link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html($visit_label); ?></a>
                                <?php else : ?>
                                    <span class="a-text a-text--xs"><?php echo esc_html($visit_label); ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                            <p class="a-text a-text--xxl"><?php echo nl2br(esc_html($address)); ?></p>
                            <?php if (!empty($map_link['url']) && !empty($map_link['title'])) : ?>
                                <div class="m-contact-form__directions">
                                    <a class="a-text a-text--m" href="<?php echo esc_url($map_link['url']); ?>" target="<?php echo esc_attr($map_link['target'] ?? ''); ?>" <?php if (($map_link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
                                        <span><?php echo esc_html($map_link['title']); ?></span>
                                        <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endif; ?>

                <?php if ($email) : ?>
                    <article class="m-contact-form__panel m-contact-form__email">
                        <span class="m-contact-form__icon" aria-hidden="true">
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/mail.svg')); ?>" alt="">
                        </span>
                        <div class="m-contact-form__panel-copy">
                            <?php if ($email_label) : ?>
                                <span class="a-text a-text--xs"><?php echo esc_html($email_label); ?></span>
                            <?php endif; ?>
                            <a class="a-text a-text--xxl" href="mailto:<?php echo esc_attr(sanitize_email($email)); ?>"><?php echo esc_html($email); ?></a>
                        </div>
                    </article>
                <?php endif; ?>

                <?php if ($social_links) : ?>
                    <article class="m-contact-form__panel m-contact-form__social">
                        <?php if ($social_title) : ?>
                            <h3 class="a-heading a-heading--h6"><?php echo esc_html($social_title); ?></h3>
                        <?php endif; ?>
                        <ul>
                            <?php foreach ($social_links as $social) : ?>
                                <?php
                                $social_icon = $social['icon'] ?? '';
                                $social_url = $social['url'] ?? '';
                                $social_label = $social['label'] ?? ucfirst(str_replace('icon-', '', $social_icon));
                                if (empty($icon_files[$social_icon]) || empty($social_url)) continue;
                                ?>
                                <li>
                                    <a href="<?php echo esc_url($social_url); ?>" aria-label="<?php echo esc_attr($social_label); ?>" target="_blank" rel="noopener noreferrer">
                                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/contact/' . $icon_files[$social_icon])); ?>" alt="" aria-hidden="true">
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </article>
                <?php endif; ?>

                <?php if (!empty($fraud_link['url'])) : ?>
                    <article class="m-contact-form__panel m-contact-form__fraud">
                        <h3 class="a-heading a-heading--h6"><?php echo esc_html(get_field('contact_form_fraud_title')); ?></h3>
                        <?php if (get_field('contact_form_fraud_description')) : ?>
                            <p class="a-text a-text--m"><?php echo esc_html(get_field('contact_form_fraud_description')); ?></p>
                        <?php endif; ?>
                        <a class="a-btn a-btn--white a-btn--smallest" href="<?php echo esc_url($fraud_link['url']); ?>" target="<?php echo esc_attr($fraud_link['target'] ?? ''); ?>" <?php if (($fraud_link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
                            <span class="a-text a-text--m"><?php echo esc_html($fraud_link['title'] ?? ''); ?> <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i></span>
                        </a>
                    </article>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>
