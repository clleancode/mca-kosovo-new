<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-category',
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

$instance   = sanitize_html_class($block['id'] ?? 'resource-category');
$filter_key = 'resource_type_' . $instance;
$page_key   = 'resource_page_' . $instance;
$cards      = get_field('resource_category_cards') ?: [];

$valid_terms = [];

foreach ($cards as &$card) {
    $card_term = !empty($card['term']) ? get_term(absint($card['term']), 'publication_type') : null;

    if ($card_term && !is_wp_error($card_term)) {
        $card['term_object'] = $card_term;
        $valid_terms[]       = (int) $card_term->term_id;
    }
}

unset($card);

$selected = isset($_GET[$filter_key]) && is_scalar($_GET[$filter_key])
    ? absint($_GET[$filter_key])
    : ($valid_terms[0] ?? 0);

if (!in_array($selected, $valid_terms, true)) {
    $selected = $valid_terms[0] ?? 0;
}

$paged = isset($_GET[$page_key]) && is_scalar($_GET[$page_key])
    ? max(1, min(10000, absint($_GET[$page_key])))
    : 1;

$args = [
    'post_type'      => 'publication',
    'post_status'    => 'publish',
    'posts_per_page' => max(1, min(12, (int) get_field('resource_category_per_page'))),
    'paged'          => $paged,
    'orderby'        => ['date' => 'DESC', 'ID' => 'DESC'],
];

if ($selected) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'publication_type',
            'field'    => 'term_id',
            'terms'    => [$selected],
        ],
    ];
}

$query = new WP_Query($args);

if ($query->max_num_pages && $paged > $query->max_num_pages) {
    $paged         = (int) $query->max_num_pages;
    $args['paged'] = $paged;
    $query         = new WP_Query($args);
}

$base = remove_query_arg($page_key);
?>
<section
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    data-resource-category="<?php echo esc_attr($instance); ?>"
>
    <div class="container">
        <div class="m-category">
            <div class="m-category__heading s-d-b-s s-m-b-xs">
                <h2 class="a-heading a-heading--h5 h-dark-blue">
                    <?php echo esc_html(get_field('resource_category_title')); ?>
                </h2>
            </div>
            <nav class="m-category__cards" aria-label="Publication categories">
                <?php foreach ($cards as $card) :
                    $term = $card['term_object'] ?? null;
                    $link = $card['link'] ?? [];
                    $url  = $term ? add_query_arg($filter_key, $term->term_id, $base) : ($link['url'] ?? '');

                    if (!$url) {
                        continue;
                    }

                    $tone  = in_array($card['tone'] ?? '', ['blue', 'purple', 'pink', 'green', 'white'], true) ? $card['tone'] : 'blue';
                    $dark  = $tone !== 'white';
                    $title = ($card['title'] ?? '') ?: ($term->name ?? ($link['title'] ?? ''));
                    ?>
                    <div class="m-category__card m-category__card--<?php echo esc_attr($tone); ?>">
                        <div class="m-category__card-top">
                            <div class="m-category__icon">
                                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/' . ($dark ? 'rectangle.svg' : 'rectangle-blue.svg'))); ?>" alt="" aria-hidden="true">
                            </div>
                            <?php if ($term) : ?>
                                <span class="a-heading a-heading--h3 <?php echo $dark ? 'h-white' : 'h-dark-blue'; ?>">
                                    <?php echo esc_html($term->count); ?>
                                </span>
                            <?php else : ?>
                                <img class="m-category__arrow<?php echo $dark ? ' m-category__arrow--light' : ''; ?>" src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/category-arrow.svg')); ?>" alt="" aria-hidden="true">
                            <?php endif; ?>
                        </div>
                        <div class="m-category__card-copy">
                            <h3 class="a-heading a-heading--h6 <?php echo $dark ? 'h-white' : 'h-dark-blue'; ?>">
                                <?php echo esc_html($title); ?>
                            </h3>
                            <?php if (!empty($card['description'])) : ?>
                                <p class="a-text a-text--m"><?php echo esc_html($card['description']); ?></p>
                            <?php endif; ?>
                        </div>
                        <a
                            class="a-category-select"
                            href="<?php echo esc_url($url); ?>"
                            aria-label="<?php echo esc_attr($title); ?>"
                            <?php if ($term) : ?>
                                data-category-filter
                                <?php if ($selected === (int) $term->term_id) : ?>
                                    aria-current="true"
                                <?php endif; ?>
                            <?php elseif (($link['target'] ?? '') === '_blank') : ?>
                                target="_blank"
                                rel="noopener noreferrer"
                            <?php endif; ?>
                        ></a>
                    </div>
                <?php endforeach; ?>
            </nav>
            <div class="m-category__results s-d-t-m s-m-t-s" aria-live="polite">
                <?php foreach ($query->posts as $publication) :
                    $pdf     = get_field('publication_pdf', $publication->ID) ?: [];
                    $project = absint(get_field('publication_project', $publication->ID));
                    $terms   = get_the_terms($publication->ID, 'publication_type');
                    $terms   = $terms && !is_wp_error($terms) ? $terms : [];
                    ?>
                    <article class="m-category__publication">
                        <div class="m-category__cover">
                            <?php if (has_post_thumbnail($publication)) : ?>
                                <?php echo get_the_post_thumbnail($publication, 'large', ['loading' => 'lazy', 'decoding' => 'async']); ?>
                            <?php else : ?>
                                <div class="m-category__cover-placeholder">
                                    <i class="icon-pdf a-icon" aria-hidden="true"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="m-category__publication-copy">
                            <div class="m-category__meta">
                                <?php foreach ($terms as $term) : ?>
                                    <span class="a-text a-text--xs h-semibold"><?php echo esc_html($term->name); ?></span>
                                <?php endforeach; ?>
                                <?php if ($project) : ?>
                                    <span class="a-text a-text--xs h-semibold"><?php echo esc_html(get_the_title($project)); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($pdf['url'])) : ?>
                                    <span class="a-text a-text--xs h-semibold">PDF</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="a-heading a-heading--h4 h-dark-blue s-d-t-xs s-m-t-xs s-d-b-xs s-m-b-xs">
                                <?php echo esc_html(get_the_title($publication)); ?>
                            </h3>
                            <p class="a-text a-text--l">
                                <?php echo esc_html(wp_strip_all_tags(get_the_excerpt($publication))); ?>
                            </p>
                            <div class="m-category__actions s-d-t-s s-m-t-xs">
                                <?php if (!empty($pdf['url'])) : ?>
                                    <a class="a-btn a-btn--blue" href="<?php echo esc_url($pdf['url']); ?>" download>
                                        <span>Download PDF <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/white-download.svg')); ?>" alt="" aria-hidden="true"></span>
                                    </a>
                                <?php endif; ?>
                                <a class="a-btn a-btn--border-blue" href="<?php echo esc_url(get_permalink($publication)); ?>">
                                    <span>View publication</span>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
                <?php if (!$query->posts) : ?>
                    <p class="a-text a-text--l">No publications in this category yet.</p>
                <?php endif; ?>
                <?php if ($query->max_num_pages > 1) : ?>
                    <nav class="m-category__pagination" aria-label="Publication pages">
                        <?php
                        echo paginate_links([
                            'base'      => str_replace('999999999', '%#%', add_query_arg($page_key, 999999999, $base)),
                            'format'    => '',
                            'current'   => $paged,
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
        </div>
    </div>
</section>