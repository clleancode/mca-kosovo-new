<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();
if (!$visibility['show']) {
    return;
}

$classes = ['o-newsletter-subscribe', $visibility['visibility_classes'], $block['className'] ?? ''];
foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");
        if ($space) {
            $classes[] = "s-{$prefix}-{$suffix}-{$space}";
        }
    }
}

$benefits = get_field('newsletter_subscribe_benefits');
$benefits = is_array($benefits) ? array_values(array_filter($benefits, static function ($row) {
    return !empty($row['text']);
})) : [];

$form_shortcode = get_field('newsletter_subscribe_shortcode');
$form_action = get_field('newsletter_subscribe_action') ?: [];
$preview_background = absint(get_field('newsletter_subscribe_background'));
$preview_image = absint(get_field('newsletter_subscribe_preview_image'));
$preview_link = get_field('newsletter_subscribe_preview_link') ?: [];
$success_title = get_field('newsletter_subscribe_success_title');
$success_description = get_field('newsletter_subscribe_success_description');
$section_title = get_field('newsletter_subscribe_title');
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    data-newsletter-subscribe
>
    <div class="container">
        <div class="m-newsletter-subscribe">
            <div class="m-newsletter-subscribe__panel">
                <div class="m-newsletter-subscribe__content">
                    <?php if ($label = get_field('newsletter_subscribe_label')) : ?>
                        <div class="m-newsletter-subscribe__label e-reveal"><span class="a-text a-text--xs"><?php echo esc_html($label); ?></span></div>
                    <?php endif; ?>
                    <?php if ($section_title) : ?><h1 class="a-heading a-heading--h3 e-reveal e-reveal--text" data-delay="100"><?php echo nl2br(esc_html($section_title)); ?></h1><?php endif; ?>
                    <?php if ($description = get_field('newsletter_subscribe_description')) : ?><p class="a-text a-text--xl e-reveal e-reveal--text" data-delay="350"><?php echo nl2br(esc_html($description)); ?></p><?php endif; ?>

                    <?php if ($benefits) : ?>
                        <ul class="m-newsletter-subscribe__benefits">
                            <?php foreach ($benefits as $benefit_index => $benefit) : ?>
                                <li class="e-reveal" data-delay="<?php echo esc_attr(450 + $benefit_index * 100); ?>">
                                    <span class="m-newsletter-subscribe__check" aria-hidden="true"><svg viewBox="0 0 16 16"><path d="m4 8 2.5 2.5L12 5"/></svg></span>
                                    <span class="a-text a-text--l"><?php echo esc_html($benefit['text']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ($form_shortcode) : ?>
                        <div class="m-newsletter-subscribe__form e-reveal" data-delay="<?php echo esc_attr($benefits ? 450 + count($benefits) * 100 : 600); ?>">
                            <?php echo do_shortcode($form_shortcode); ?>
                            <?php if ($error_message = get_field('newsletter_subscribe_error_message')) : ?>
                                <div class="m-newsletter-subscribe__error" data-newsletter-error hidden role="alert" aria-live="assertive">
                                    <span class="m-newsletter-subscribe__error-icon" aria-hidden="true">!</span>
                                    <p class="a-text a-text--xs"><?php echo esc_html($error_message); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php elseif (!empty($form_action['url']) && !empty($form_action['title'])) : ?>
                        <form class="m-newsletter-subscribe__signup e-reveal" data-delay="<?php echo esc_attr($benefits ? 450 + count($benefits) * 100 : 600); ?>" action="<?php echo esc_url($form_action['url']); ?>" method="get">
                            <div class="m-newsletter-subscribe__signup-row">
                                <input class="a-text a-text--s" type="email" name="email" placeholder="<?php echo esc_attr(get_field('newsletter_subscribe_placeholder')); ?>" aria-label="<?php echo esc_attr(get_field('newsletter_subscribe_placeholder')); ?>" autocomplete="email" required>
                                <button class="a-text a-text--xs" type="submit"><?php echo esc_html($form_action['title']); ?></button>
                            </div>
                            <?php if ($consent = get_field('newsletter_subscribe_consent')) : ?>
                                <label class="m-newsletter-subscribe__consent">
                                    <input type="checkbox" name="newsletter_consent" value="1" required>
                                    <span class="a-text a-text--xs"><?php echo esc_html($consent); ?></span>
                                </label>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="m-newsletter-subscribe__preview">
                    <?php if ($preview_background) : ?>
                        <div class="m-newsletter-subscribe__background e-reveal e-reveal--image" data-delay="150">
                            <?php echo wp_get_attachment_image($preview_background, 'large', false, ['alt' => '', 'aria-hidden' => 'true', 'loading' => 'lazy', 'decoding' => 'async']); ?>
                        </div>
                    <?php endif; ?>
                    <article class="m-newsletter-subscribe__story e-reveal" data-delay="500">
                        <?php if ($label = get_field('newsletter_subscribe_preview_label')) : ?><p class="a-text a-text--xs"><?php echo esc_html($label); ?></p><?php endif; ?>
                        <?php if ($preview_image) : ?>
                            <div class="m-newsletter-subscribe__story-image">
                                <?php echo wp_get_attachment_image($preview_image, 'medium_large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($value = get_field('newsletter_subscribe_preview_title')) : ?><h3 class="a-text a-text--xxl h-semibold"><?php echo nl2br(esc_html($value)); ?></h3><?php endif; ?>
                        <?php if ($preview_description = get_field('newsletter_subscribe_preview_description')) : ?><p class="a-text a-text--s"><?php echo nl2br(esc_html($preview_description)); ?></p><?php endif; ?>
                        <?php if (!empty($preview_link['url']) && !empty($preview_link['title'])) : ?>
                            <div class="m-newsletter-subscribe__story-link">
                                <a class="a-text a-text--m" href="<?php echo esc_url($preview_link['url']); ?>" target="<?php echo esc_attr($preview_link['target'] ?? '_self'); ?>" <?php if (($preview_link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html($preview_link['title']); ?></a>
                            </div>
                        <?php endif; ?>
                    </article>
                </div>
            </div>

            <?php if ($success_title || $success_description) : ?>
                <div class="m-newsletter-subscribe__success" data-newsletter-success hidden role="status" aria-live="polite">
                    <span class="m-newsletter-subscribe__success-icon" aria-hidden="true"><svg viewBox="0 0 20 20"><path d="m5 10 3.2 3.2L15 6.5"/></svg></span>
                    <div>
                        <?php if ($success_title) : ?><h3 class="a-heading a-heading--h6"><?php echo esc_html($success_title); ?></h3><?php endif; ?>
                        <?php if ($success_description) : ?><p class="a-text a-text--m"><?php echo esc_html($success_description); ?></p><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
