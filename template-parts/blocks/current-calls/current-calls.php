<?php

use MCA\Helpers;
use MCA\Notices;

require_once get_template_directory() . '/inc/procurement-notices.php';

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-current-calls',
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
] = Helpers\get_block_typography([
    'header_color' => 'h-dark-blue',
]);

$statuses = [
    'open'     => ['label' => 'Open', 'terms' => ['ongoing', 'open', 'closing-soon', 'in-evaluation']],
    'upcoming' => ['label' => 'Upcoming', 'terms' => ['upcoming']],
    'closed'   => ['label' => 'Closed', 'terms' => ['closed']],
];

$requested_status = isset($_GET['call_status']) && is_scalar($_GET['call_status'])
    ? sanitize_key(wp_unslash($_GET['call_status']))
    : 'open';
$active_status = isset($statuses[$requested_status]) ? $requested_status : 'open';
$base_url = remove_query_arg('call_status');
$counts = [];

foreach ($statuses as $status => $config) {
    $count_query = new WP_Query([
        'post_type'      => 'procurement',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => false,
        'tax_query'      => [[
            'taxonomy'         => 'procurement_status',
            'field'            => 'slug',
            'terms'            => $config['terms'],
            'operator'         => 'IN',
            'include_children' => false,
        ]],
    ]);
    $counts[$status] = (int) $count_query->found_posts;
    wp_reset_postdata();
}

$query = new WP_Query([
    'post_type'      => 'procurement',
    'post_status'    => 'publish',
    'posts_per_page' => 1,
    'no_found_rows'  => true,
    'orderby'        => ['date' => 'DESC', 'ID' => 'DESC'],
    'tax_query'      => [[
        'taxonomy'         => 'procurement_status',
        'field'            => 'slug',
        'terms'            => $statuses[$active_status]['terms'],
        'operator'         => 'IN',
        'include_children' => false,
    ]],
]);

$notify_link = get_field('current_calls_notify_link') ?: [];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    data-current-calls
    aria-live="polite"
>
    <div class="container">
        <div class="m-current-calls">
            <div class="m-current-calls__header">
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($heading_color); ?> h--<?php echo esc_attr($heading_height); ?> e-reveal e-reveal--text">
                    <?php echo esc_html(get_field('current_calls_title')); ?>
                </<?php echo tag_escape($tag); ?>>

                <nav class="m-current-calls__tabs" aria-label="Call status">
                    <?php $tab_index = 0; ?>
                    <?php foreach ($statuses as $status => $config) : ?>
                        <?php $tab_url = add_query_arg('call_status', $status, $base_url); ?>
                        <a
                            class="m-current-calls__tab<?php echo $active_status === $status ? ' is-active' : ''; ?> e-reveal"
                            data-delay="<?php echo esc_attr(150 + $tab_index * 90); ?>"
                            href="<?php echo esc_url($tab_url); ?>"
                            <?php if ($active_status === $status) : ?>aria-current="page"<?php endif; ?>
                        >
                            <span><?php echo esc_html($config['label']); ?></span>
                                <span class="m-current-calls__count"><?php echo esc_html($counts[$status]); ?></span>
                        </a>
                        <?php $tab_index++; ?>
                    <?php endforeach; ?>
                </nav>
            </div>

            <div class="m-current-calls__layout">
                <div class="m-current-calls__message e-reveal">
                    <?php
                    $has_calls = $counts[$active_status] > 0;
                    $message_label = [
                        'open' => $has_calls ? 'Open' : (get_field('current_calls_empty_label') ?: 'Coming soon'),
                        'upcoming' => 'Coming soon',
                        'closed' => 'Closed',
                    ][$active_status];
                    if ($active_status === 'open') {
                        $message_title = $has_calls
                            ? get_field('current_calls_open_title')
                            : get_field('current_calls_empty_title');
                        $message_description = $has_calls
                            ? get_field('current_calls_open_description')
                            : get_field('current_calls_empty_description');
                        $message_link = $has_calls
                            ? (get_field('current_calls_open_link') ?: [])
                            : $notify_link;
                    } else {
                        $message_title = get_field('current_calls_' . $active_status . '_title');
                        $message_description = get_field('current_calls_' . $active_status . '_description');
                        $message_link = $notify_link;
                    }
                    if (!$message_title) {
                        $message_title = $has_calls
                            ? $statuses[$active_status]['label'] . ' calls'
                            : 'No ' . strtolower($statuses[$active_status]['label']) . ' calls at the moment';
                    }
                    ?>
                    <?php if ($message_label) : ?>
                        <span class="m-current-calls__eyebrow a-text a-text--xs h-semibold">
                            <?php echo esc_html($message_label); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($message_title) : ?>
                        <h2 class="a-heading a-heading--h4 h-dark-blue">
                            <?php echo esc_html($message_title); ?>
                        </h2>
                    <?php endif; ?>
                    <?php if ($message_description) : ?>
                        <p class="a-text a-text--l">
                            <?php echo nl2br(esc_html($message_description)); ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($message_link['url'])) : ?>
                        <a
                            class="a-btn a-btn--blue a-btn--smallest h-semibold a-text a-text--m"
                            href="<?php echo esc_url($message_link['url']); ?>"
                            target="<?php echo esc_attr($message_link['target'] ?? ''); ?>"
                            <?php if (($message_link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                        >
                            <span><?php echo esc_html($message_link['title'] ?? ''); ?></span>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($query->have_posts()) : ?>
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <?php
                        $post_id = get_the_ID();
                        [$state, $state_label] = Notices\get_status($post_id);
                        if ($active_status === 'upcoming') {
                            $state = 'evaluation';
                            $state_label = 'Upcoming';
                        }
                        $category = get_field('procurement_call_category', $post_id);
                        $summary = get_field('procurement_call_summary', $post_id);
                        $deadline = Notices\get_deadline($post_id);
                        $project_id = absint(get_field('procurement_project', $post_id));
                        $project_name = $project_id ? get_the_title($project_id) : '';
                        $category_label = $category ?: trim(implode(' · ', array_filter([$project_name, get_field('procurement_notice_type', $post_id)])));
                        ?>
                        <article class="m-current-calls__card e-reveal" data-delay="250">
                            <div class="m-current-calls__card-top">
                                <div class="m-current-calls__meta">
                                    <span class="a-notices-status a-notices-status--<?php echo esc_attr($state); ?> a-text a-text--xs h-semibold">
                                        <?php echo esc_html($state_label); ?>
                                    </span>
                                    <?php if ($category_label) : ?>
                                        <span class="m-current-calls__category a-text a-text--xs h-semibold">
                                            <?php echo esc_html($category_label); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($deadline) : ?>
                                    <div class="m-current-calls__deadline">
                                        <span class="a-text a-text--xs h-semibold">Deadline</span>
                                        <strong class="a-heading a-heading--h4 h-dark-blue"><?php echo esc_html($deadline->format('d')); ?></strong>
                                        <span class="a-text a-text--xs h-semibold"><?php echo esc_html($deadline->format('M Y')); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="m-current-calls__card-copy">
                                <h3 class="a-heading a-heading--h6 h-dark-blue">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <?php if ($summary) : ?>
                                    <p class="a-text a-text--s"><?php echo esc_html($summary); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="m-current-calls__card-footer">
                                <span class="a-text a-text--xs h-semibold">Call details</span>
                                <a class="m-current-calls__open" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                                    <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else : ?>
                    <article class="m-current-calls__card m-current-calls__card--empty e-reveal" data-delay="250">
                        <span class="a-text a-text--xs h-semibold">
                            <?php echo esc_html($statuses[$active_status]['label']); ?>
                        </span>
                        <p class="a-text a-text--m">No calls are available in this category.</p>
                    </article>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>
    </div>
</section>
