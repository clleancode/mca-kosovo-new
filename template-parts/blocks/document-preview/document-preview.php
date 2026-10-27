<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-document-preview',
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

$id       = absint(get_field('dp_publication')) ?: get_the_ID();
$file     = get_field('dp_file') ?: get_field('publication_pdf', $id) ?: [];
$file_url = $file['url'] ?? '';
$format   = $file_url
    ? strtoupper(pathinfo(wp_parse_url($file_url, PHP_URL_PATH), PATHINFO_EXTENSION))
    : '';
$is_pdf   = $format === 'PDF';
$cover    = absint(get_field('dp_cover')) ?: get_post_thumbnail_id($id);
$project  = absint(get_field('publication_project', $id));
$terms    = get_the_terms($id, 'publication_type');
$terms    = $terms && !is_wp_error($terms) ? $terms : [];

$details = [
    'Type'       => implode(' · ', wp_list_pluck($terms, 'name')),
    'Project'    => $project ? get_the_title($project) : '',
    'Activities' => get_field('dp_activities'),
    'Published'  => get_the_date('j F Y', $id),
    'Format'     => $format,
];

$email   = sanitize_email(get_field('dp_email') ?: 'info@mcakosovo.org');
$page_id = wp_unique_id('document-page-');
?>
<section
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
>
    <div class="container">
        <div class="m-document-preview" data-document-preview>
            <div class="m-document-preview__viewer">
                <div class="m-document-preview__toolbar">
                    <h2 class="a-text a-text--m h-semibold e-reveal e-reveal--text">
                        <?php echo esc_html(get_field('dp_title') ?: __('Document preview', 'MCA')); ?>
                    </h2>
                    <?php if ($is_pdf || $cover) : ?>
                        <div class="m-document-preview__controls e-reveal" data-delay="200">
                            <button type="button" data-document-zoom="-1" aria-label="Zoom out">−</button>
                            <output class="a-text a-text--m h-semibold" data-document-scale aria-live="polite">100%</output>
                            <button type="button" data-document-zoom="1" aria-label="Zoom in">+</button>
                            <?php if ($is_pdf) : ?>
                                <label class="a-text a-text--m h-semibold" for="<?php echo esc_attr($page_id); ?>">
                                    Page
                                    <input
                                        id="<?php echo esc_attr($page_id); ?>"
                                        type="number"
                                        min="1"
                                        value="1"
                                        data-document-page
                                    >
                                </label>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="m-document-preview__stage e-reveal" data-delay="300">
                    <?php if ($is_pdf) : ?>
                        <div
                            class="m-document-preview__embed"
                            data-document-embed
                            data-lenis-prevent
                            data-lenis-prevent-touch
                        >
                            <div
                                data-document-pdf="<?php echo esc_url($file_url); ?>"
                                hidden
                                role="img"
                                aria-label="<?php echo esc_attr(sprintf(__('Document preview: %s', 'MCA'), get_the_title($id))); ?>"
                            ></div>
                            <p class="a-text a-text--m" data-document-status role="status">Loading document…</p>
                        </div>
                    <?php elseif ($cover) : ?>
                        <div class="m-document-preview__page" data-document-image>
                            <?php echo wp_get_attachment_image($cover, 'large'); ?>
                        </div>
                    <?php else : ?>
                        <p class="a-text a-text--m"><?php esc_html_e('Preview unavailable.', 'MCA'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <aside class="m-document-preview__aside">
                <div class="m-document-preview__details">
                    <h2 class="a-heading a-heading--h6 h-semibold e-reveal e-reveal--text" data-delay="150"><?php esc_html_e('Details', 'MCA'); ?></h2>
                    <dl>
                        <?php
                        $detail_index = 0;
                        foreach ($details as $label => $value) :
                            if (!$value) {
                                continue;
                            }
                            ?>
                            <div class="e-reveal" data-delay="<?php echo esc_attr(300 + $detail_index * 100); ?>">
                                <dt class="a-text a-text--m h-semibold"><?php echo esc_html($label); ?></dt>
                                <dd class="a-text a-text--m h-semibold"><?php echo esc_html($value); ?></dd>
                            </div>
                            <?php $detail_index++; ?>
                        <?php endforeach; ?>
                    </dl>
                </div>
                <?php if ($email) : ?>
                    <div class="m-document-preview__contact">
                        <h2 class="a-text a-text--xxl h-semibold e-reveal e-reveal--text">
                            <?php echo esc_html(get_field('dp_contact_title') ?: __('Questions about this document?', 'MCA')); ?>
                        </h2>
                        <p class="a-text a-text--m e-reveal e-reveal--text" data-delay="250">
                            Contact <a href="<?php echo esc_url('mailto:' . $email); ?>"><?php echo esc_html($email); ?></a>
                        </p>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>
