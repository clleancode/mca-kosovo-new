<?php

use MCA\Helpers;
use MCA\Notices;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-job-role',
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

$id       = get_the_ID();
$deadline = Notices\get_deadline($id);
$closed   = $deadline
    ? $deadline < new DateTimeImmutable('today', wp_timezone())
    : has_term('closed', 'job_status', $id);
$url      = get_permalink($id);

$facts = [
    [
        'Location',
        get_field('jr_location'),
        get_field('jr_location_note'),
    ],
    [
        'Contract',
        get_field('jr_contract'),
        get_field('jr_contract_note'),
    ],
    [
        'Reports to',
        get_field('jr_reports_to'),
        get_field('jr_reports_note'),
    ],
    [
        'Application deadline',
        $deadline ? $deadline->format('d') : '—',
        $deadline ? wp_date('F Y', $deadline->getTimestamp()) : '',
    ],
];

$apply  = get_field('jr_apply_link');
$alerts = get_field('jr_alerts_link');

if ($closed) {
    $application_title = __('This vacancy is closed', 'MCA');
    $application_text  = $deadline
        ? sprintf(
            __('Applications closed on %s. Subscribe to job alerts to hear about new roles.', 'MCA'),
            wp_date('j F Y', $deadline->getTimestamp())
        )
        : __('Subscribe to job alerts to hear about new roles.', 'MCA');
} else {
    $application_title = __('Apply for this role', 'MCA');
    $application_text  = get_field('jr_apply_intro');
}
?>
<section
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
>
    <div class="container">
        <div class="m-job-role">
            <div class="m-job-role__facts">
                <?php foreach ($facts as $index => [$label, $fact, $note]) : ?>
                    <div class="m-job-role__fact<?php echo $index === 3 ? ' is-deadline' : ''; ?>">
                        <p class="a-text a-text--xs h-semibold h-uppercase"><?php echo esc_html($label); ?></p>
                        <p class="h-semibold a-heading a-heading--<?php echo $index === 3 ? 'h3' : 'h6'; ?>"><?php echo esc_html($fact); ?></p>
                        <?php if ($note) : ?>
                            <p class="a-text a-text--m"><?php echo esc_html($note); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="m-job-role__layout">
                <div class="m-job-role__main">
                    <div class="m-job-role__card">
                        <h2 class="a-heading a-heading--h5">
                            <?php echo esc_html(get_field('jr_about_title')); ?>
                        </h2>
                        <?php $intro = get_field('jr_about'); ?>
                        <?php if ($intro) : ?>
                            <p class="a-text a-text--xl"><?php echo nl2br(esc_html($intro)); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php $duties = get_field('jr_duties'); ?>
                    <?php if ($duties) : ?>
                        <div class="m-job-role__card">
                            <h2 class="a-heading a-heading--h5">
                                <?php echo esc_html(get_field('jr_duties_title')); ?>
                            </h2>
                            <ul>
                                <?php foreach ($duties as $duty) : ?>
                                    <li class="a-text a-text--l"><?php echo esc_html($duty['text'] ?? ''); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php if (get_field('jr_required') || get_field('jr_preferred')) : ?>
                        <div class="m-job-role__card">
                            <h2 class="a-heading a-heading--h5">
                                <?php echo esc_html(get_field('jr_qualifications_title')); ?>
                            </h2>
                            <div class="m-job-role__qualifications">
                                <?php
                                foreach (['required' => 'Required', 'preferred' => 'Preferred'] as $key => $label) :
                                    $items = get_field('jr_' . $key);

                                    if (!$items) {
                                        continue;
                                    }
                                    ?>
                                    <div<?php if ($key === 'preferred') : ?> class="is-preferred"<?php endif; ?>>
                                        <h3 class="a-heading a-heading--h6"><?php echo esc_html($label); ?></h3>
                                        <ul>
                                            <?php foreach ($items as $item) : ?>
                                                <li class="a-text a-text--l"><?php echo esc_html($item['text'] ?? ''); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <aside class="m-job-role__aside">
                    <div class="m-job-role__card is-application">
                        <h2 class="h-semibold a-heading a-heading--h6"><?php echo esc_html($application_title); ?></h2>
                        <p class="a-text a-text--m"><?php echo esc_html($application_text); ?></p>
                        <div class="m-job-role__actions">
                            <?php if ($closed) : ?>
                                <button class="a-text a-text--m h-semibold" type="button" disabled>
                                    <?php esc_html_e('Apply now', 'MCA'); ?>
                                </button>
                            <?php elseif (!empty($apply['url'])) : ?>
                                <a
                                    class="a-text a-text--m h-semibold"
                                    href="<?php echo esc_url($apply['url']); ?>"
                                    <?php if (($apply['target'] ?? '') === '_blank') : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                                >
                                    <?php echo esc_html($apply['title'] ?? ''); ?>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($alerts['url'])) : ?>
                                <a
                                    class="a-text a-text--m h-semibold"
                                    href="<?php echo esc_url($alerts['url']); ?>"
                                    <?php if (($alerts['target'] ?? '') === '_blank') : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                                >
                                    <?php echo esc_html($alerts['title'] ?? ''); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php $documents = get_field('jr_documents'); ?>
                    <?php if ($documents) : ?>
                        <div class="m-job-role__card">
                            <h2 class="h-semibold a-heading a-heading--h6"><?php esc_html_e('Documents', 'MCA'); ?></h2>
                            <?php
                            foreach ($documents as $document) :
                                $file = $document['file'] ?? [];

                                if (empty($file['url'])) {
                                    continue;
                                }

                                $format = strtoupper(pathinfo(wp_parse_url($file['url'], PHP_URL_PATH), PATHINFO_EXTENSION));
                                ?>
                                <div class="m-job-role__document">
                                    <div class="m-job-role__file">
                                        <span class="a-text a-text--xs h-uppercase"><?php echo esc_html($format); ?></span>
                                    </div>
                                    <div class="m-job-role__document-copy">
                                        <a
                                            class="a-text a-text--m h-semibold"
                                            href="<?php echo esc_url($file['url']); ?>"
                                            download
                                        ><?php echo esc_html($document['title'] ?: $file['title']); ?></a>
                                        <?php if (!empty($document['description'])) : ?>
                                            <p class="a-text a-text--xs h-uppercase"><?php echo esc_html($document['description']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="m-job-role__card">
                        <h2 class="h-semibold a-heading a-heading--h6"><?php esc_html_e('Share this vacancy', 'MCA'); ?></h2>
                        <div class="m-job-role__share">
                            <a
                                class="a-text a-text--m"
                                href="<?php echo esc_url('https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($url)); ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >LinkedIn</a>
                            <a
                                class="a-text a-text--m"
                                href="<?php echo esc_url('mailto:?subject=' . rawurlencode(get_the_title($id)) . '&body=' . rawurlencode($url)); ?>"
                            >Email</a>
                            <button
                                class="a-text a-text--m"
                                type="button"
                                data-job-copy="<?php echo esc_url($url); ?>"
                            >Copy link</button>
                        </div>
                        <span class="a-text a-text--m" data-copy-status role="status"></span>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
