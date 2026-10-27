<?php

use MCA\Helpers;
use MCA\Notices;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-vacancies',
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
] = Helpers\get_block_typography(['header_color' => 'h-dark-blue']);

$instance   = sanitize_html_class($block['id'] ?? wp_unique_id('vacancies-'));
$status_key = 'vacancy_status_' . $instance;
$page_key   = 'vacancy_page_' . $instance;

$selected = isset($_GET[$status_key]) && is_scalar($_GET[$status_key]) && $_GET[$status_key] === 'closed'
    ? 'closed'
    : 'ongoing';

$page = isset($_GET[$page_key]) && is_scalar($_GET[$page_key])
    ? max(1, min(10000, absint($_GET[$page_key])))
    : 1;

$per_page = max(3, min(24, (int) (get_field('vacancies_per_page') ?: 6)));

$queries = [];
$other_positions = (bool) get_field('vacancies_other_positions');
$archive_search = !$other_positions && (bool) get_field('vacancies_archive_search');
$other_jobs = [];
if ($other_positions) {
    $current_job = is_singular('job') ? get_queried_object_id() : 0;
    $job_selection = array_values(array_diff(array_filter(array_map('absint', (array) get_field('vacancies_other_selection'))), [$current_job]));
    $other_args = [
        'post_type' => 'job',
        'post_status' => 'publish',
        'posts_per_page' => 3,
        'post__not_in' => $current_job ? [$current_job] : [],
        'no_found_rows' => true,
    ];
    if ($job_selection) {
        $other_args['post__in'] = $job_selection;
        $other_args['orderby'] = 'post__in';
    } else {
        $other_args['orderby'] = ['date' => 'DESC', 'ID' => 'DESC'];
    }
    $other_jobs = get_posts($other_args);
}

foreach ($other_positions || $archive_search ? [] : ['ongoing', 'closed'] as $state) {
    $args = [
        'post_type'      => 'job',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $selected === $state ? $page : 1,
        'orderby'        => ['date' => 'DESC', 'ID' => 'DESC'],
        'tax_query'      => [
            [
                'taxonomy' => 'job_status',
                'field'    => 'slug',
                'terms'    => [$state],
            ],
        ],
    ];

    $queries[$state] = new WP_Query($args);

    if ($queries[$state]->max_num_pages && $args['paged'] > $queries[$state]->max_num_pages) {
        $args['paged']   = $queries[$state]->max_num_pages;
        $queries[$state] = new WP_Query($args);
    }
}

$section_id = sanitize_title(get_field('vacancies_section_id') ?: ($block['anchor'] ?? ''));
$base       = remove_query_arg([$status_key, $page_key]);

$get_job_state = static function ($job_id) {
    $deadline = Notices\get_deadline($job_id);
    $is_closed = $deadline
        ? $deadline < new DateTimeImmutable('today', wp_timezone())
        : has_term('closed', 'job_status', $job_id);
    return $is_closed ? 'closed' : 'ongoing';
};

$render_cards = static function ($jobs, $state) use ($get_job_state) {
    ?>
    <div class="m-vacancies__grid">
        <?php foreach (array_values($jobs) as $job_index => $job) : ?>
            <?php
            $deadline   = Notices\get_deadline($job->ID);
            $card_state = $state;
            if ($card_state === null) {
                $card_state = $get_job_state($job->ID);
            }
            $department = get_field('job_department', $job->ID);
            $tone       = get_field('job_department_color', $job->ID);
            $tone       = in_array($tone, ['blue', 'purple', 'green'], true) ? $tone : 'blue';
            $url        = get_permalink($job);
            ?>
            <article class="m-vacancies__card e-reveal" data-delay="<?php echo esc_attr(150 + ($job_index % 3) * 120); ?>">
                <div class="m-vacancies__card-top">
                    <div class="m-vacancies__meta">
                        <div class="m-vacancies__status m-vacancies__status--<?php echo esc_attr($card_state); ?>">
                            <span class="a-text a-text--xs">
                                <?php echo $card_state === 'closed' ? 'Closed' : 'Ongoing'; ?>
                            </span>
                        </div>

                        <?php if ($department) : ?>
                            <div class="m-vacancies__department m-vacancies__department--<?php echo esc_attr($tone); ?>">
                                <span class="a-text a-text--xs h-semibold"><?php echo esc_html($department); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($deadline) : ?>
                        <div class="m-vacancies__deadline">
                            <span class="a-text a-text--xs h-semibold">Deadline</span>
                            <span class="a-heading a-heading--h5 h-dark-blue"><?php echo esc_html($deadline->format('d')); ?></span>
                            <span class="a-text a-text--xs h-dark-blue h-semibold"><?php echo esc_html($deadline->format('M Y')); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="m-vacancies__card-title">
                    <h3 class="a-heading a-heading--h6 h-dark-blue h-semibold">
                        <a class="a-vacancies-title" href="<?php echo esc_url($url); ?>">
                            <?php echo esc_html(get_the_title($job)); ?>
                        </a>
                    </h3>
                </div>

                <div class="m-vacancies__card-footer s-d-t-xs s-m-t-xs">
                    <span class="a-text a-text--xs h-semibold">
                        <?php echo esc_html(get_field('job_employer', $job->ID)); ?>
                    </span>

                    <a
                        class="a-vacancies-open"
                        href="<?php echo esc_url($url); ?>"
                        aria-label="<?php echo esc_attr(get_the_title($job)); ?>"
                    >
                        <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php
};
if ($archive_search) {
    require __DIR__ . '/vacancies-archive.php';
    return;
}
?>
<section
    <?php if ($section_id) : ?>
        id="<?php echo esc_attr($section_id); ?>"
    <?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    data-vacancies
>
    <div class="container">
        <div class="m-vacancies">

            <div class="m-vacancies__header">
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> e-reveal e-reveal--text">
                    <?php echo esc_html(get_field('vacancies_title') ?: ($other_positions ? __('Other positions', 'MCA') : '')); ?>
                </<?php echo tag_escape($tag); ?>>

                <?php if (!$other_positions): ?>
                <nav class="m-vacancies__tabs e-reveal" data-delay="150" aria-label="Vacancy status">
                    <?php foreach (['ongoing' => 'Ongoing', 'closed' => 'Closed'] as $state => $label) : ?>
                        <a
                            class="a-text a-text--m h-semibold"
                            href="<?php echo esc_url(add_query_arg($status_key, $state, $base)); ?>"
                            data-vacancy-tab="<?php echo esc_attr($state); ?>"
                            <?php if ($selected === $state) : ?>
                                aria-current="true"
                            <?php endif; ?>
                        >
                            <?php echo esc_html($label); ?>
                            <span class="a-text a-text--xs h-semibold h-dark-blue"><?php echo esc_html($queries[$state]->found_posts); ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <?php endif; ?>
            </div>

            <?php if ($other_positions): ?>
                <?php if ($other_jobs): $render_cards($other_jobs, null); else: ?>
                    <p class="a-text a-text--l e-reveal e-reveal--text"><?php esc_html_e('No other positions found.', 'MCA'); ?></p>
                <?php endif; ?>
            <?php endif; ?>
            <?php foreach ($other_positions ? [] : ['ongoing', 'closed'] as $state) : ?>
                <?php $query = $queries[$state]; ?>
                <div
                    class="m-vacancies__panel s-m-t-s"
                    data-vacancy-panel="<?php echo esc_attr($state); ?>"
                    <?php if ($state !== $selected) : ?>
                        hidden
                    <?php endif; ?>
                >
                    <?php if ($query->posts) : ?>
                        <?php $render_cards($query->posts, $state); ?>

                    <?php elseif ($state === 'ongoing') : ?>
                        <?php
                        $action_links = [
                            'alerts'      => get_field('vacancies_alerts_link'),
                            'application' => get_field('vacancies_application_link'),
                        ];
                        ?>
                        <div class="m-vacancies__empty">
                            <div class="m-vacancies__empty-icon e-reveal e-reveal--scale">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                    <rect x="3" y="7" width="18" height="14" rx="2"/>
                                    <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </div>

                            <div class="m-vacancies__empty-copy">
                                <h3 class="a-heading a-heading--h5 h-dark-blue e-reveal e-reveal--text" data-delay="120">
                                    <?php echo esc_html(get_field('vacancies_empty_title')); ?>
                                </h3>
                                <p class="a-text a-text--l e-reveal e-reveal--text" data-delay="350">
                                    <?php echo esc_html(get_field('vacancies_empty_description')); ?>
                                </p>
                            </div>

                            <div class="m-vacancies__actions">
                                <?php foreach ($action_links as $type => $link) : ?>
                                    <?php
                                    if (empty($link['url'])) {
                                        continue;
                                    }

                                    $target = ($link['target'] ?? '') ?: '_self';
                                    $title  = $link['title'] ?? '';
                                    $btn    = $type === 'alerts' ? 'a-btn--blue' : 'a-btn--border-blue';
                                    ?>
                                    <a
                                        class="a-btn a-btn--smallest <?php echo esc_attr($btn); ?> e-reveal"
                                        data-delay="<?php echo esc_attr($type === 'alerts' ? 500 : 600); ?>"
                                        href="<?php echo esc_url($link['url']); ?>"
                                        target="<?php echo esc_attr($target); ?>"
                                        <?php if ($target === '_blank') : ?>
                                            rel="noopener noreferrer"
                                        <?php endif; ?>
                                    >
                                        <span><?php echo esc_html($title); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php if ($queries['closed']->posts) : ?>
                            <div class="m-vacancies__closed-heading">
                                <h3 class="a-heading a-heading--h6 h-dark-blue e-reveal e-reveal--text">
                                    <?php echo esc_html(get_field('vacancies_closed_title')); ?>
                                </h3>
                                <a
                                    class="a-text a-text--m h-semibold e-reveal"
                                    data-delay="120"
                                    href="<?php echo esc_url(add_query_arg($status_key, 'closed', $base)); ?>"
                                    data-vacancy-switch="closed"
                                >
                                    View all closed <span aria-hidden="true">→</span>
                                </a>
                            </div>

                            <?php $render_cards($queries['closed']->posts, 'closed'); ?>
                        <?php endif; ?>

                    <?php else : ?>
                        <p class="a-text a-text--l e-reveal e-reveal--text">No closed positions yet.</p>
                    <?php endif; ?>

                    <?php if ($query->max_num_pages > 1) : ?>
                        <nav class="m-vacancies__pagination e-reveal" data-delay="150" aria-label="Vacancy pages">
                            <?php
                            echo paginate_links([
                                'base'      => str_replace('999999999', '%#%', add_query_arg([$status_key => $state, $page_key => 999999999], $base)),
                                'format'    => '',
                                'current'   => $query->query_vars['paged'],
                                'total'     => $query->max_num_pages,
                                'mid_size'  => 1,
                                'end_size'  => 1,
                                'prev_text' => '&larr;',
                                'next_text' => '&rarr;',
                            ]);
                            ?>
                        </nav>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</section>
