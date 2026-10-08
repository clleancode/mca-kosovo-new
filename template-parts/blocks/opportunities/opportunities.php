<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-opportunities',
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
    'header_tag'         => $tag,
    'header_size'        => $size,
    'header_color'       => $heading_color,
    'header_line_height' => $heading_height,
    'text_type'          => $text_size,
    'text_color'         => $text_color,
    'text_line_height'   => $text_height,
] = Helpers\get_block_typography();

$cards = array_slice(get_field('opportunities_cards') ?: [], 0, 4);

$query = new WP_Query([
    'post_type'      => 'procurement',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'no_found_rows'  => true,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

$archive     = get_field('opportunities_procurement_link') ?: [];
$archive_url = $archive['url'] ?? '';
$email_link  = get_field('opportunities_email_link') ?: [];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-opportunities__intro">
            <div class="m-opportunities__heading">
                <p class="a-badge h-dark-blue"><?php echo esc_html(get_field('opportunities_label')); ?></p>
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($heading_color); ?> h--<?php echo esc_attr($heading_height); ?>">
                    <?php echo esc_html(get_field('opportunities_title')); ?>
                </<?php echo tag_escape($tag); ?>>
            </div>
            <div class="m-opportunities__description">
                <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                    <?php echo nl2br(esc_html(get_field('opportunities_description'))); ?>
                </p>
            </div>
        </div>

        <div class="m-opportunities__layout">
            <div class="m-opportunities__cards">
                <?php foreach ($cards as $index => $card) : ?>
                    <?php
                    $link   = $card['link'] ?? [];
                    $url    = $link['url'] ?? '';
                    $label  = $card['label'] ?? '';
                    $target = $link['target'] ?? '';
                    ?>
                    <article class="m-opportunities__card">
                        <?php if (!empty($card['image'])) : ?>
                            <?php
                            echo wp_get_attachment_image($card['image'], 'large', false, [
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                            ]);
                            ?>
                        <?php endif; ?>

                        <div class="m-opportunities__card-top">
                            <?php if ($label) : ?>
                                <span class="a-text a-text--xs h-semibold"><?php echo esc_html($label); ?></span>
                            <?php endif; ?>
                            <span class="a-text a-text--s h-semibold"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                        </div>
                        <div class="m-opportunities__card-content">
                            <h3 class="a-heading a-heading--h5 h-white"><?php echo esc_html($card['title'] ?? ''); ?></h3>
                            <?php if (!empty($card['description'])) : ?>
                                <p class="a-text a-text--l h-white"><?php echo esc_html($card['description']); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if ($url) : ?>
                            <a
                                class="m-opportunities__card-link"
                                href="<?php echo esc_url($url); ?>"
                                target="<?php echo esc_attr($target); ?>"
                                aria-label="<?php echo esc_attr($card['title'] ?? ''); ?>"
                                <?php if ($target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                            >
                                <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="m-opportunities__procurements">
                <div class="m-opportunities__panel-header">
                    <div class="m-opportunities__panel-icon">
                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/document.svg')); ?>" alt="" aria-hidden="true">
                    </div>

                    <div class="m-opportunities__panel-heading">
                        <span class="a-text a-text--xs h-semibold">Latest procurement</span>
                        <h3 class="a-text a-text--xxl h-dark-blue h-semibold">
                            <?php echo esc_html(get_field('opportunities_procurement_label')); ?>
                        </h3>
                    </div>

                    <a class="a-text a-text--s h-semibold" href="<?php echo esc_url($archive_url); ?>">
                        <?php echo esc_html($archive['title'] ?? ''); ?>
                        <i class="h-semibold icon-arrow-right a-icon" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="m-opportunities__notice-list">
                    <?php foreach ($query->posts as $index => $procurement) : ?>
                        <?php
                        $terms = get_the_terms($procurement->ID, 'procurement_status');
                        $terms = $terms && !is_wp_error($terms) ? $terms : [];

                        $closed       = in_array('closed', wp_list_pluck($terms, 'slug'), true);
                        $status_label = $terms[0]->name ?? '';

                        foreach ($terms as $term) {
                            if (in_array($term->slug, ['closed', 'ongoing', 'in-evaluation'], true)) {
                                $status_label = $term->name;
                            }
                        }

                        $deadline      = get_field('deadline', $procurement->ID);
                        $deadline_date = $deadline
                            ? DateTimeImmutable::createFromFormat('!d.m.Y', $deadline, wp_timezone())
                            : false;

                        $phase      = get_field('procurement_phase', $procurement->ID);
                        $notice     = get_field('procurement_notice_type', $procurement->ID);
                        $notice_url = get_permalink($procurement);
                        $title      = get_the_title($procurement);
                        $published  = get_the_date('d.m.Y', $procurement);
                        ?>

                        <?php if ($index === 0) : ?>
                            <?php
                            $award      = get_field('procurement_award_phase', $procurement->ID);
                            $decoration = file_exists(get_theme_file_path('/assets/img/pics/icons/testimonial.svg'))
                                ? 'testimonial.svg'
                                : 'timeline.svg';
                            ?>
                            <article class="m-opportunities__featured">
                                <img
                                    class="m-opportunities__featured-decoration"
                                    src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/' . $decoration)); ?>"
                                    alt=""
                                    aria-hidden="true"
                                >

                                <div class="m-opportunities__featured-meta">
                                    <div>
                                        <span class="a-opportunities-status a-text a-text--xs h-semibold"><?php echo esc_html($status_label); ?></span>
                                        <?php if ($notice) : ?>
                                            <span class="a-text a-text--xs"><?php echo esc_html($notice); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="a-text a-text--xs h-pink h-semibold">Featured</span>
                                </div>

                                <div class="m-opportunities__featured-title">
                                    <h3 class="a-text a-text--xxl h-white">
                                        <a class="a-opportunities-title" href="<?php echo esc_url($notice_url); ?>">
                                            <?php echo esc_html($title); ?>
                                        </a>
                                    </h3>
                                </div>

                                <div class="m-opportunities__stages s-d-b-xs s-m-b-xs">
                                    <div>
                                        <span class="a-text a-text--xs">Published</span>
                                        <span class="a-text a-text--m h-regular"><?php echo esc_html($published); ?></span>
                                    </div>

                                    <?php if ($phase) : ?>
                                        <div class="m-opportunities__stage-current">
                                            <span class="a-text a-text--xs h-semibold">Evaluation</span>
                                            <span class="a-text a-text--m h-regular"><?php echo esc_html($phase); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($award) : ?>
                                        <div>
                                            <span class="a-text a-text--xs h-semibold">Award</span>
                                            <span class="a-text a-text--m h-regular"><?php echo esc_html($award); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="m-opportunities__featured-action">
                                    <a class="a-btn a-btn--white a-btn--icon-red" href="<?php echo esc_url($notice_url); ?>">
                                        <span>View notice</span>
                                        <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </article>
                        <?php else : ?>
                            <article class="m-opportunities__notice">
                                <?php if ($deadline_date) : ?>
                                    <div class="m-opportunities__deadline">
                                        <span class="a-text a-text--xs">Deadline</span>
                                        <strong class="a-heading a-heading--h5 h-medium"><?php echo esc_html($deadline_date->format('d')); ?></strong>
                                        <span class="a-text a-text--xs h-semibold"><?php echo esc_html($deadline_date->format('M Y')); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="m-opportunities__notice-copy">
                                    <div class="m-opportunities__notice-meta">
                                        <span class="h-semibold a-opportunities-status<?php echo $closed ? ' a-opportunities-status--closed' : ''; ?> a-text a-text--xs">
                                            <?php echo esc_html($status_label); ?>
                                        </span>
                                        <?php if ($notice) : ?>
                                            <span class="a-text a-text--xs"><?php echo esc_html($notice); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="m-opportunities__notice-title">
                                        <h3 class="a-text a-text--xxl h-dark-blue">
                                            <a class="a-opportunities-title" href="<?php echo esc_url($notice_url); ?>">
                                                <?php echo esc_html($title); ?>
                                            </a>
                                        </h3>
                                    </div>

                                    <span class="a-text a-text--m">Published <?php echo esc_html($published); ?></span>
                                </div>
                                <a
                                    class="a-opportunities-notice-link h-semibold"
                                    href="<?php echo esc_url($notice_url); ?>"
                                    aria-label="<?php echo esc_attr($title); ?>"
                                >
                                    <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                </a>
                            </article>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (!$query->posts) : ?>
                        <p class="a-text a-text--m">No procurement notices have been published yet.</p>
                    <?php endif; ?>
                </div>
                <a class="a-opportunities-email a-text a-text--s" href="<?php echo esc_url($email_link['url'] ?? ''); ?>">
                    <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/mail.svg')); ?>" alt="" aria-hidden="true">
                    <span class="h-semibold"><?php echo esc_html($email_link['title'] ?? ''); ?></span>
                    <i class="icon-arrow-right a-icon" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</section>