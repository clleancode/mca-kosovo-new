<?php

use MCA\Helpers;
use MCA\Notices;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-notices',
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

$compact = get_field('notices_layout') === 'selected';
$header_link = get_field('notices_header_link') ?: [];
$filters = $compact
    ? ['search' => '', 'status' => 'all', 'type' => 'all', 'project' => 0, 'sort' => 'newest', 'page' => 1]
    : Notices\get_filters();
$per_page = max(3, min(24, (int) (get_field('notices_per_page') ?: 8)));
$args     = Notices\query_args($filters, $per_page);
if ($compact) {
    $selected = array_slice(array_filter(array_map('absint', (array) get_field('notices_selected'))), 0, 3);
    $args['posts_per_page'] = 3;
    $args['no_found_rows'] = true;
    if ($selected) {
        $args['post__in'] = $selected;
        $args['orderby'] = 'post__in';
    } else {
        $args['post__not_in'] = [get_the_ID()];
    }
}
$query = new WP_Query($args);

if ($query->max_num_pages && $filters['page'] > $query->max_num_pages) {
    $filters['page'] = (int) $query->max_num_pages;
    $args['paged']   = $filters['page'];
    $query           = new WP_Query($args);
}

$total_pages = max(1, (int) $query->max_num_pages);
$featured = !$compact && get_field('notices_featured');
$base_url    = remove_query_arg('notice_page');

$legend = [
    'open'       => 'Open',
    'closing'    => 'Closing soon',
    'evaluation' => 'In evaluation',
    'award'      => 'Award',
    'general'    => 'General notice',
    'closed'     => 'Closed',
];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    <?php if (!$compact): ?>data-notices<?php endif; ?>
>
    <div class="container">
        <div class="m-notices">
            <div class="m-notices__header s-d-b-s s-m-b-xs">
                <div class="m-notices__heading">
                    <h2 class="a-heading a-heading--h5 h-dark-blue">
                        <?php echo esc_html(get_field('notices_title') ?: ($compact ? 'More procurement notices' : 'Latest notices')); ?>
                    </h2>
                    <?php if (!$compact): ?><span class="a-text a-text--xs">
                        Page <?php echo esc_html($filters['page']); ?> of <?php echo esc_html($total_pages); ?>
                    </span><?php endif; ?>
                </div>

                <?php if (!$compact): ?><ul class="m-notices__legend" aria-label="Notice statuses">
                    <?php foreach ($legend as $state => $label) : ?>
                        <li>
                            <span class="h-semibold a-notices-status a-notices-status--<?php echo esc_attr($state); ?> a-text a-text--xs">
                                <?php echo esc_html($label); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul><?php endif; ?>
                <?php if (!empty($header_link['url'])): ?>
                    <a class="a-btn a-btn--border-blue a-btn--smallest" href="<?php echo esc_url($header_link['url']); ?>"<?php if (($header_link['target'] ?? '') === '_blank'): ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>><span><?php echo esc_html($header_link['title'] ?: 'All procurements'); ?> &rarr;</span></a>
                <?php endif; ?>
            </div>

            <div class="m-notices__results" aria-live="polite">
                <?php if ($query->posts) : ?>
                    <div class="m-notices__grid">
                        <?php foreach ($query->posts as $index => $notice) : ?>
                            <?php
                            [$state, $status_label] = Notices\get_status($notice->ID);

                            $is_featured  = $featured && $index === 0 && $filters['page'] === 1;
                            $url          = get_permalink($notice);
                            $title        = get_the_title($notice);
                            $type         = get_field('procurement_notice_type', $notice->ID);
                            $deadline     = Notices\get_deadline($notice->ID);
                            $project_id   = absint(get_field('procurement_project', $notice->ID));
                            $project_name = $project_id ? get_the_title($project_id) : '';
                            $phase        = get_field('procurement_phase', $notice->ID);
                            ?>
                            <article class="m-notices__card<?php echo $is_featured ? ' m-notices__card--featured' : ''; ?>">
                                <?php if ($is_featured) : ?>
                                    <div class="m-notices__image">
                                        <?php
                                        echo get_the_post_thumbnail($notice, 'large', [
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        ]);
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <div class="m-notices__card-top">
                                    <div class="m-notices__meta">
                                        <span class="h-semibold a-notices-status a-notices-status--<?php echo esc_attr($state); ?> a-text a-text--xs h-semibold">
                                            <?php echo esc_html($status_label); ?>
                                        </span>
                                        <?php if ($type) : ?>
                                            <span class="h-semibold a-text a-text--xs"><?php echo esc_html($type); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!$is_featured) : ?>
                                        <div class="m-notices__date-box">
                                            <?php if ($state === 'award') : ?>
                                                <span class="a-text a-text--xs h-opacity-50">Status</span>
                                                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/red-check.svg')); ?>" alt="" aria-hidden="true">
                                                <span class="a-text a-text--xs h-semibold">Awarded</span>
                                            <?php elseif ($state === 'general') : ?>
                                                <span class="a-text a-text--xs h-opacity-50">Status</span>
                                                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/!.svg')); ?>" alt="" aria-hidden="true">
                                                <span class="a-text a-text--xs h-semibold">Notice</span>
                                            <?php elseif ($deadline) : ?>
                                                <span class="a-text a-text--xs h-opacity-50">Deadline</span>
                                                <span class="a-heading a-heading--h3 h-dark-blue"><?php echo esc_html($deadline->format('d')); ?></span>
                                                <span class="a-text a-text--xs h-semibold"><?php echo esc_html($deadline->format('M Y')); ?></span>
                                            <?php else : ?>
                                                <span class="a-text a-text--xs h-opacity-50">Status</span>
                                                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/!.svg')); ?>" alt="" aria-hidden="true">
                                                <span class="a-text a-text--xs h-semibold"><?php echo esc_html($status_label); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="m-notices__card-heading s-d-t-s s-d-b-s s-m-t-s s-m-b-xs">
                                    <?php if ($project_name) : ?>
                                        <div class="m-notices__project">
                                            <span class="a-text a-text--xs"><?php echo esc_html($project_name); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <h3 class="a-heading a-heading--<?php echo $is_featured ? 'h5' : 'h6'; ?> <?php echo $is_featured ? 'h-white' : 'h-dark-blue'; ?>">
                                        <a class="a-notices-title" href="<?php echo esc_url($url); ?>">
                                            <?php echo esc_html($title); ?>
                                        </a>
                                    </h3>
                                </div>

                                <div class="m-notices__card-footer s-d-t-s s-m-t-xs">
                                    <div class="m-notices__published">
                                        <span class="a-text a-text--xs h-semibold">Published</span>
                                        <time class="a-text a-text--xs h-semibold" datetime="<?php echo esc_attr(get_the_date('c', $notice)); ?>">
                                            <?php echo esc_html(get_the_date('d.m.Y', $notice)); ?>
                                        </time>
                                    </div>

                                    <?php if ($is_featured && $phase) : ?>
                                        <div class="m-notices__phase">
                                            <span class="a-text a-text--xs h-semibold">Stage</span>
                                            <span class="a-text a-text--xs h-semibold "><?php echo esc_html($phase); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($is_featured) : ?>
                                        <a class="a-btn a-btn--white a-btn--smallest h-semibold a-text a-text--m" href="<?php echo esc_url($url); ?>">
                                            <span>
                                                View notice
                                                <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                    <?php else : ?>
                                        <a
                                            class="a-notices-open"
                                            href="<?php echo esc_url($url); ?>"
                                            aria-label="<?php echo esc_attr($title); ?>"
                                        >
                                            <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="m-notices__empty">
                        <p class="a-text a-text--l">No notices match your filters. Try a different search or reset the filters.</p>
                    </div>
                <?php endif; ?>

                <?php if ($total_pages > 1) : ?>
                    <nav class="m-notices__pagination" aria-label="Notice pages">
                        <?php
                        echo paginate_links([
                            'base'      => str_replace('999999999', '%#%', add_query_arg('notice_page', 999999999, $base_url)),
                            'format'    => '',
                            'current'   => $filters['page'],
                            'total'     => $total_pages,
                            'mid_size'  => 1,
                            'end_size'  => 1,
                            'prev_text' => '&larr;',
                            'next_text' => '&rarr;',
                        ]);
                        ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
