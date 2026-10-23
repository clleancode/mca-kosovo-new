<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();
if (!$visibility['show']) {
    return;
}

$classes = ['o-how-to-report', $visibility['visibility_classes'], $block['className'] ?? ''];
foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");
        if ($space) {
            $classes[] = "s-{$prefix}-{$suffix}-{$space}";
        }
    }
}

$contacts = get_field('how_to_report_mcc_contacts');
$contacts = is_array($contacts) ? array_values(array_filter($contacts, static function ($row) {
    return !empty($row['label']) || !empty($row['value']) || !empty($row['link']['url']);
})) : [];

$steps = get_field('how_to_report_oig_steps');
$steps = is_array($steps) ? array_values(array_filter($steps, static function ($row) {
    return !empty($row['text']);
})) : [];

$options = get_field('how_to_report_oig_options');
$options = is_array($options) ? array_values(array_filter($options, static function ($row) {
    return !empty($row['link']['url']) && !empty($row['link']['title']);
})) : [];

$resources = get_field('how_to_report_resources');
$resources = is_array($resources) ? array_values(array_filter($resources, static function ($row) {
    return !empty($row['title']) || !empty($row['description']) || !empty($row['file_label']) || !empty($row['link']['url']) || !empty($row['pdf']['url']);
})) : [];

$section_title = get_field('how_to_report_title');
?>
<section <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?> class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <div class="m-how-to-report">
            <?php if ($section_title) : ?>
                <div class="m-how-to-report__heading">
                    <h2 class="a-heading a-heading--h4"><?php echo esc_html($section_title); ?></h2>
                </div>
            <?php endif; ?>

            <div class="m-how-to-report__main-grid">
                <article class="m-how-to-report__card m-how-to-report__card--mcc">
                    <?php if (get_field('how_to_report_mcc_label')) : ?>
                        <div class="m-how-to-report__eyebrow">
                            <span class="m-how-to-report__eyebrow-number"><span class="a-text a-text--m">01</span></span>
                            <span class="a-text a-text--s"><?php echo esc_html(get_field('how_to_report_mcc_label')); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($value = get_field('how_to_report_mcc_title')) : ?><h3 class="a-heading a-heading--h5"><?php echo nl2br(esc_html($value)); ?></h3><?php endif; ?>
                    <?php if ($value = get_field('how_to_report_mcc_description')) : ?><p class="a-text a-text--l"><?php echo nl2br(esc_html($value)); ?></p><?php endif; ?>

                    <?php if ($contacts) : ?>
                        <div class="m-how-to-report__contacts">
                            <?php foreach ($contacts as $contact) : ?>
                                <?php
                                $contact_label  = strtolower(trim((string) ($contact['label'] ?? '')));
                                $contact_value  = trim((string) ($contact['value'] ?? ''));
                                $contact_link   = $contact['link'] ?? [];
                                $contact_url    = '';
                                $contact_target = $contact_link['target'] ?? '_self';

                                if (strpos($contact_label, 'email') !== false && $contact_value) {
                                    $email = sanitize_email($contact_value);
                                    if (is_email($email)) {
                                        $contact_url    = 'mailto:' . $email;
                                        $contact_target = '_self';
                                    }
                                } elseif ((strpos($contact_label, 'whatsapp') !== false || strpos($contact_label, 'whats app') !== false) && $contact_value) {
                                    $phone = preg_replace('/\D+/', '', $contact_value);
                                    if ($phone) {
                                        $contact_url    = 'https://wa.me/' . $phone;
                                        $contact_target = '_blank';
                                    }
                                }

                                if (!$contact_url && !empty($contact_link['url'])) {
                                    $contact_url = $contact_link['url'];
                                }
                                ?>
                                <div class="m-how-to-report__contact">
                                    <div class="m-how-to-report__contact-copy">
                                        <?php if (!empty($contact['label'])) : ?><span class="a-text a-text--xs"><?php echo esc_html($contact['label']); ?></span><?php endif; ?>
                                        <?php if ($contact_value && $contact_url) : ?>
                                            <a class="a-text a-text--xxl" href="<?php echo esc_url($contact_url); ?>" target="<?php echo esc_attr($contact_target); ?>" <?php if ($contact_target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html($contact_value); ?></a>
                                        <?php elseif ($contact_value) : ?>
                                            <span class="a-text a-text--xxl"><?php echo esc_html($contact_value); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($contact_url) : ?>
                                        <a class="m-how-to-report__contact-action" href="<?php echo esc_url($contact_url); ?>" target="<?php echo esc_attr($contact_target); ?>" aria-label="<?php echo esc_attr(($contact_link['title'] ?? '') ?: ($contact['value'] ?? '')); ?>" <?php if ($contact_target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
                                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 17 17 7M7 7h10v10"/></svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>

                <article class="m-how-to-report__card m-how-to-report__card--oig">
                    <?php if (get_field('how_to_report_oig_label')) : ?>
                        <div class="m-how-to-report__eyebrow">
                            <span class="m-how-to-report__eyebrow-number"><span class="a-text a-text--m">02</span></span>
                            <span class="a-text a-text--s"><?php echo esc_html(get_field('how_to_report_oig_label')); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($value = get_field('how_to_report_oig_title')) : ?><h3 class="a-heading a-heading--h5"><?php echo nl2br(esc_html($value)); ?></h3><?php endif; ?>
                    <?php if ($value = get_field('how_to_report_oig_description')) : ?><p class="a-text a-text--l"><?php echo nl2br(esc_html($value)); ?></p><?php endif; ?>

                    <?php if ($steps) : ?>
                        <ol class="m-how-to-report__steps">
                            <?php foreach ($steps as $index => $step) : ?>
                                <li>
                                    <span class="m-how-to-report__step-number"><span class="a-text a-text--s"><?php echo esc_html($index + 1); ?></span></span>
                                    <span class="a-text a-text--l"><?php echo esc_html($step['text']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>

                    <?php if ($value = get_field('how_to_report_oig_note')) : ?><p class="a-text a-text--l"><?php echo nl2br(esc_html($value)); ?></p><?php endif; ?>

                    <?php if ($options) : ?>
                        <div class="m-how-to-report__options">
                            <?php foreach ($options as $row) : $link = $row['link']; ?>
                                <span class="m-how-to-report__option">
                                    <a class="a-text a-text--m" href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr($link['target'] ?? '_self'); ?>" <?php if (($link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html($link['title']); ?></a>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php $cta = get_field('how_to_report_oig_cta'); ?>
                    <?php if (!empty($cta['url']) && !empty($cta['title'])) : ?>
                        <div class="m-how-to-report__cta">
                            <a class="a-text a-text--m h-semibold" href="<?php echo esc_url($cta['url']); ?>" target="<?php echo esc_attr($cta['target'] ?? '_self'); ?>" <?php if (($cta['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
                                <span><?php echo esc_html($cta['title']); ?></span>
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 17 17 7M7 7h10v10"/></svg>
                            </a>
                        </div>
                    <?php endif; ?>
                </article>
            </div>

            <div class="m-how-to-report__footer-grid">
                <?php $anonymous_title = get_field('how_to_report_anonymous_title'); $anonymous_description = get_field('how_to_report_anonymous_description'); ?>
                <?php if ($anonymous_title || $anonymous_description) : ?>
                    <aside class="m-how-to-report__anonymous">
                        <span class="m-how-to-report__alert-icon"><span class="a-text a-text--xxl">!</span></span>
                        <div>
                            <?php if ($anonymous_title) : ?><h3 class="a-heading a-heading--h6"><?php echo esc_html($anonymous_title); ?></h3><?php endif; ?>
                            <?php if ($anonymous_description) : ?><p class="a-text a-text--l"><?php echo nl2br(esc_html($anonymous_description)); ?></p><?php endif; ?>
                        </div>
                    </aside>
                <?php endif; ?>

                <?php foreach ($resources as $resource) : ?>
                    <?php $pdf = $resource['pdf'] ?? []; $link = $resource['link'] ?? []; ?>
                    <article class="m-how-to-report__resource">
                        <?php if (!empty($pdf['url'])) : ?>
                            <a class="m-how-to-report__resource-download" href="<?php echo esc_url($pdf['url']); ?>" download aria-label="<?php echo esc_attr($resource['title'] ?? ''); ?>"></a>
                        <?php endif; ?>
                        <?php if (!empty($resource['title'])) : ?><h3 class="a-heading a-heading--h6"><?php echo esc_html($resource['title']); ?></h3><?php endif; ?>
                        <?php if (!empty($resource['description'])) : ?><p class="a-text a-text--m"><?php echo esc_html($resource['description']); ?></p><?php endif; ?>
                        <?php if (!empty($resource['file_label'])) : ?><span class="a-text a-text--l"><?php echo esc_html($resource['file_label']); ?></span><?php endif; ?>
                        <?php if (!empty($link['url']) && !empty($link['title'])) : ?>
                            <span class="m-how-to-report__resource-link">
                                <a class="a-text a-text--m" href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr($link['target'] ?? '_self'); ?>" <?php if (($link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html($link['title']); ?></a>
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 17 17 7M7 7h10v10"/></svg>
                            </span>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
