<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-application',
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
    'header_tag'   => $tag,
    'header_size'  => $size,
    'header_color' => $color,
] = Helpers\get_block_typography();

$section_id      = sanitize_title(get_field('application_section_id') ?: ($block['anchor'] ?? ''));
$main_image      = get_field('application_main_image');
$secondary_image = get_field('application_secondary_image');
$shortcode       = get_field('application_shortcode');
$form_file       = get_field('application_form_file');
$contact_email   = get_field('application_contact_email');
$privacy_link    = get_field('application_privacy_policy_link');
$privacy_url     = $privacy_link['url'] ?? get_privacy_policy_url();

$jobs = $shortcode ? [] : get_posts([
    'post_type'      => 'job',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'tax_query'      => [
        [
            'taxonomy' => 'job_status',
            'field'    => 'slug',
            'terms'    => ['ongoing'],
        ],
    ],
]);

$upload_fields = [
    'resume' => 'Upload Resume',
    'form'   => 'Upload Job Application Form',
];
?>
<section
    <?php if ($section_id) : ?>
        id="<?php echo esc_attr($section_id); ?>"
    <?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    data-application
    data-application-error="<?php echo esc_attr__('The application could not be sent because the connection or server failed. Please try again. If the problem continues, contact us using the email beside this form.', 'mca'); ?>"
>
    <div class="container">
        <div class="m-application">
            <div class="m-application__tile">
                <div class="m-application__heading">
                    <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> e-reveal e-reveal--text">
                        <?php echo esc_html(get_field('application_title')); ?>
                    </<?php echo tag_escape($tag); ?>>
                    <p class="a-text a-text--m e-reveal e-reveal--text" data-delay="150">
                        <?php echo esc_html(get_field('application_note')); ?>
                    </p>
                </div>
                <div class="m-application__form e-reveal" data-delay="300">
                    <?php if ($shortcode) : ?>
                        <?php echo do_shortcode($shortcode); ?>
                    <?php else : ?>
                        <form data-application-preview>
                            <div class="m-application__field">
                                <label class="a-text a-text--m h-semibold">
                                    Position
                                    <span class="m-application__select">
                                        <select class="a-text a-text--l" name="application-position" required>
                                            <option value="">Select a position</option>
                                            <option>Open application</option>
                                            <?php foreach ($jobs as $job) : ?>
                                                <option><?php echo esc_html(get_the_title($job)); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/Vector.svg')); ?>" alt="" aria-hidden="true">
                                    </span>
                                </label>
                            </div>
                            <div class="m-application__row">
                                <div class="m-application__field">
                                    <label class="a-text a-text--m h-semibold">
                                        Full name
                                        <input class="a-text a-text--l" type="text" name="application-name" placeholder="Your full name" autocomplete="name" required>
                                    </label>
                                </div>
                                <div class="m-application__field">
                                    <label class="a-text a-text--m h-semibold">
                                        Email address
                                        <input class="a-text a-text--l" type="email" name="application-email" placeholder="name@example.com" autocomplete="email" required>
                                    </label>
                                </div>
                            </div>
                            <div class="m-application__field">
                                <label class="a-text a-text--m h-semibold">
                                    Phone number
                                    <input class="a-text a-text--l" type="tel" name="application-phone" placeholder="+383" autocomplete="tel" required>
                                </label>
                            </div>
                            <div class="m-application__field">
                                <label class="a-text a-text--m h-semibold">
                                    Why do you want to work with us?
                                    <textarea class="a-text a-text--l" name="application-motivation" placeholder="Tell us briefly about your motivation" required></textarea>
                                </label>
                            </div>
                            <div class="m-application__row">
                                <?php foreach ($upload_fields as $key => $label) : ?>
                                    <div class="m-application__upload">
                                        <label>
                                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/row.svg')); ?>" alt="" aria-hidden="true">
                                            <span class="a-text a-text--m h-semibold"><?php echo esc_html($label); ?></span>
                                            <span class="a-text a-text--xs h-semibold">PDF or DOC · Max 2MB</span>
                                            <input
                                                type="file"
                                                name="application-<?php echo esc_attr($key); ?>"
                                                accept=".pdf,.doc,.docx"
                                                aria-label="<?php echo esc_attr($label); ?>"
                                                required
                                            >
                                        </label>
                                        <span class="a-text a-text--xs" data-upload-name></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="m-application__consent">
                                <label class="a-text a-text--m">
                                    <input type="checkbox" required>
                                    I agree to the
                                    <?php if ($privacy_url) : ?>
                                        <a class="a-text a-text--m" href="<?php echo esc_url($privacy_url); ?>" target="<?php echo esc_attr(!empty($privacy_link['target']) ? $privacy_link['target'] : '_blank'); ?>" rel="noopener noreferrer">Privacy Policy</a>
                                    <?php else : ?>
                                        Privacy Policy
                                    <?php endif; ?>
                                </label>
                            </div>
                            <div class="m-application__submit">
                                <button class="a-btn a-btn--blue a-btn--smallest" type="submit" disabled>
                                    <span class="a-text a-text--m">Submit application</span>
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <aside class="m-application__sidebar">
                <div class="m-application__photos">
                    <?php if ($main_image) : ?>
                        <div class="m-application__main-photo e-reveal e-reveal--image" data-delay="150">
                            <?php
                            echo wp_get_attachment_image($main_image, 'large', false, [
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                            ]);
                            ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($secondary_image) : ?>
                        <div class="m-application__secondary-photo e-reveal e-reveal--image" data-delay="300">
                            <?php
                            echo wp_get_attachment_image($secondary_image, 'large', false, [
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                            ]);
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="m-application__contact">
                    <h2 class="a-heading a-heading--h6 h-white h-semibold e-reveal e-reveal--text">
                        <?php echo esc_html(get_field('application_contact_title')); ?>
                    </h2>
                    <p class="a-text a-text--m h-white s-d-t-xs s-m-t-xs e-reveal e-reveal--text" data-delay="150">
                        <?php echo esc_html(get_field('application_contact_description')); ?>
                        <?php if ($contact_email) : ?>
                            <a class="a-text a-text--s h-white" href="<?php echo esc_url('mailto:' . sanitize_email($contact_email)); ?>">
                                <?php echo esc_html($contact_email); ?>
                            </a>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($form_file['url'])) : ?>
                        <div class="m-application__download s-d-t-xs s-m-t-xs e-reveal" data-delay="300">
                            <a class="a-btn a-btn--white a-btn--smallest" href="<?php echo esc_url($form_file['url']); ?>" download>
                                <span class="a-text a-text--m h-dark-blue">
                                    <?php echo esc_html(get_field('application_download_label')); ?>
                                    <span aria-hidden="true">↓</span>
                                </span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</section>
