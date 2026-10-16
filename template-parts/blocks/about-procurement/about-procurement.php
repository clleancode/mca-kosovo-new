<?php

use MCA\Helpers;
use MCA\Notices;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-about-procurement',
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

$id        = get_the_ID();
$deadline  = Notices\get_deadline($id);
$published = get_the_date('d M Y', $id);

$value = static function ($name, $fallback = '') {
    return get_field($name) ?: $fallback;
};

$facts = [
    [
        'Reference No.',
        get_field('procurement_reference', $id),
        'Procurement ref. number',
    ],
    [
        'Procurement method',
        get_field('procurement_method', $id),
        $value('ap_method_note'),
    ],
    [
        'Published',
        $published,
        get_the_date('l', $id),
    ],
    [
        'Submission deadline',
        $deadline ? $deadline->format('d') : '—',
        $deadline ? trim($deadline->format('F Y') . ' ' . $value('ap_deadline_time')) : 'To be announced',
    ],
    [
        'Submission',
        $value('ap_submission', 'See notice'),
        $value('ap_submission_note'),
    ],
];

$notice_status = Notices\get_status($id);
$default_stage = ($notice_status[0] ?? '') === 'award' ? 4 : ((($notice_status[0] ?? '') === 'evaluation') ? 3 : 2);
$stage_value = $value('ap_process_stage', $default_stage);
if (is_array($stage_value)) {
    $stage_value = $stage_value['value'] ?? 2;
}
$stage_choices = [
    'Published' => 1,
    'Submission deadline' => 2,
    'Evaluation' => 3,
    'Award notice' => 4,
];
$stage = $stage_choices[$stage_value] ?? max(1, min(4, (int) $stage_value));

$process = [
    ['Published', $published],
    ['Submission deadline', $deadline ? $deadline->format('d M Y') : 'To be announced'],
    ['Evaluation', $value('ap_evaluation_note')],
    ['Award notice', $value('ap_award_note')],
];

$has_about = $value('ap_intro') || $value('ap_description') || $value('ap_content');
$has_participation = true;
$has_process = true;
$has_main = $has_about || $has_participation || $has_process;
$steps = get_field('ap_steps') ?: [
    ['title' => 'Register', 'description' => 'Consult the official notice for registration instructions and the complete set of procurement documents.'],
    ['title' => 'Prepare your offer', 'description' => 'Prepare your offer in accordance with the requirements in the procurement documents.'],
    ['title' => 'Submit your offer', 'description' => 'Follow the submission method and deadline specified in the official notice.'],
];
$url = get_permalink($id);
?>
<section
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
>
    <div class="container">
        <div class="m-about-procurement">
            <div class="m-about-procurement__facts">
                <?php foreach ($facts as $index => [$label, $fact, $note]) : ?>
                    <div class="m-about-procurement__fact<?php echo $index === 3 ? ' is-deadline' : ''; ?>">
                        <p class="a-text a-text--xs h-semibold h-uppercase"><?php echo esc_html($label); ?></p>
                        <p class="h-semibold a-heading a-heading--<?php echo $index === 3 ? 'h3' : 'h6'; ?>"><?php echo esc_html($fact); ?></p>
                        <?php if ($note) : ?>
                            <p class="a-text a-text--m "><?php echo esc_html($note); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="m-about-procurement__layout<?php echo $has_main ? '' : ' is-documents-only'; ?>">
                <?php if ($has_main) : ?>
                <div class="m-about-procurement__main">
                    <?php if ($has_about) : ?>
                    <div class="m-about-procurement__card">
                        <h2 class="a-heading a-heading--h5">
                            <?php echo esc_html($value('ap_about_title', 'About this procurement')); ?>
                        </h2>
                        <?php if ($intro = $value('ap_intro')) : ?>
                            <p class="a-text a-text--xl"><?php echo nl2br(esc_html($intro)); ?></p>
                        <?php endif; ?>
                        <?php if ($description = $value('ap_description')) : ?>
                            <p class="a-text a-text--m"><?php echo nl2br(esc_html($description)); ?></p>
                        <?php endif; ?>
                    <?php if ($content = $value('ap_content')) : ?>
                        <div class="a-text a-text--l"><?php echo wp_kses_post($content); ?></div>
                    <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($has_participation) : ?>
                    <div class="m-about-procurement__card">
                        <h2 class="a-heading a-heading--h5">
                            <?php echo esc_html($value('ap_participation_title', 'How to take part')); ?>
                        </h2>
                        <?php if ($steps) : ?>
                            <div class="m-about-procurement__steps">
                                <?php foreach ($steps as $index => $step) : ?>
                                    <div class="m-about-procurement__step">
                                        <div class="m-about-procurement__number">
                                            <span class="a-text a-text--xs"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                                        </div>
                                        <h3 class="a-text a-text--xxl h-semibold"><?php echo esc_html($step['title'] ?? ''); ?></h3>
                                        <p class="a-text a-text--m"><?php echo nl2br(esc_html($step['description'] ?? '')); ?></p>
                                        <?php
                                        $link = $step['link'] ?? [];

                                        if (!empty($link['url'])) :
                                            ?>
                                            <div>
                                                <a
                                                    class="a-underline a-text a-text--l"
                                                    href="<?php echo esc_url($link['url']); ?>"
                                                    <?php if (($link['target'] ?? '') === '_blank') : ?>
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                    <?php endif; ?>
                                                >
                                                    <span><?php echo esc_html($link['title'] ?: 'Register for documents'); ?></span>
                                                    <i class="icon-arrow-right a-icon" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($warning = $value('ap_warning')) : ?>
                            <div class="m-about-procurement__warning">
                                <p class="a-text a-text--m h-semibold"><?php echo esc_html($warning); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($has_process) : ?>
                    <div class="m-about-procurement__card">
                        <h2 class="a-heading a-heading--h5">
                            <?php echo esc_html($value('ap_process_title', 'Procurement process')); ?>
                        </h2>
                        <ol class="m-about-procurement__process">
                            <?php foreach ($process as $index => [$label, $note]) : ?>
                                <li
                                    <?php echo $index + 1 <= $stage ? 'class="is-complete' . ($index + 1 < $stage ? ' is-connected' : '') . '"' : ''; ?>
                                    <?php echo $index + 1 === $stage ? 'aria-current="step"' : ''; ?>
                                >
                                    <h3 class="a-text a-text--xxl h-semibold"><?php echo esc_html($label); ?></h3>
                                    <p class="a-text a-text--xs h-semibold h-uppercase"><?php echo esc_html($note); ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <aside class="m-about-procurement__aside">
                    <?php if ($documents = get_field('ap_documents')) : ?>
                        <div class="m-about-procurement__card">
                            <h2 class="a-heading a-heading--h6 h-semibold">Documents</h2>
                            <?php foreach ($documents as $document) :
                                $file         = $document['file'] ?? [];
                                $link         = $document['link'] ?? [];
                                $document_url = $file['url'] ?? ($link['url'] ?? '');

                                if (!$document_url) {
                                    continue;
                                }

                                $format = !empty($file['url'])
                                    ? strtoupper(pathinfo(parse_url($file['url'], PHP_URL_PATH), PATHINFO_EXTENSION))
                                    : 'LINK';
                                ?>
                                <div class="m-about-procurement__document">
                                    <div class="m-about-procurement__file">
                                        <span class="a-text a-text--xs h-semibold"><?php echo esc_html($format); ?></span>
                                    </div>
                                    <div class="m-about-procurement__document-copy">
                                        <a
                                            class="a-text a-text--m h-semibold"
                                            href="<?php echo esc_url($document_url); ?>"
                                            <?php if ($file) : ?>
                                                download
                                            <?php elseif (($link['target'] ?? '') === '_blank') : ?>
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            <?php endif; ?>
                                        >
                                            <?php echo esc_html($document['title'] ?? 'Document'); ?>
                                        </a>
                                        <p class="a-text a-text--xs h-semibold h-uppercase"><?php echo esc_html($document['description'] ?? ''); ?></p>
                                    </div>
                                    <a class="m-about-procurement__arrow" href="<?php echo esc_url($document_url); ?>" aria-label="<?php echo esc_attr('Download ' . ($document['title'] ?? 'document')); ?>"<?php echo $file ? ' download' : ''; ?>>
                                        <?php if ($file) : ?>
                                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/white-download.svg')); ?>" alt="">
                                        <?php endif; ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="m-about-procurement__card is-contact">
                        <h2 class="a-heading a-heading--h6">Questions?</h2>
                        <p class="a-text a-text--m">
                            <?php echo esc_html($value('ap_contact_intro', 'Contact the MCA Kosovo Procurement Agent.')); ?>
                        </p>
                        <?php if ($email = $value('ap_email', 'MCAKosovoPA@adt-global.com')) : ?>
                            <div class="m-about-procurement__contact-detail">
                                <p class="a-text a-text--xs h-uppercase">Email</p>
                                <a class="a-text a-text--m" href="<?php echo esc_url('mailto:' . sanitize_email($email)); ?>">
                                    <?php echo esc_html($email); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ($address = $value('ap_address', 'Migjeni Str. No. 21, 10000 Pristina, Republic of Kosovo')) : ?>
                            <div class="m-about-procurement__contact-detail">
                                <p class="a-text a-text--xs h-uppercase">Address</p>
                                <p class="a-text a-text--m"><?php echo nl2br(esc_html($address)); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="m-about-procurement__card is-share">
                        <h2 class="a-text a-text--xxl h-semibold">Share this notice</h2>
                        <div class="m-about-procurement__share s-d-t-xs s-m-t-xs">
                            <button type="button" class="a-text a-text--m" data-procurement-copy="<?php echo esc_url($url); ?>">Copy link</button>
                            <a class="a-text a-text--m" href="<?php echo esc_url('https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($url)); ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
                            <a class="a-text a-text--m" href="<?php echo esc_url('mailto:?subject=' . rawurlencode(get_the_title($id)) . '&body=' . rawurlencode($url)); ?>">Email</a>
                        </div>
                        <span class="a-text a-text--m" data-copy-status role="status"></span>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>