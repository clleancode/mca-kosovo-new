<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-all-documents',
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

$section_id = sanitize_title(get_field('all_documents_section_id') ?: ($block['anchor'] ?? ''));
$instance   = sanitize_html_class($block['id'] ?? 'all-documents');
$filter_key = 'document_filter_' . $instance;
$page_key   = 'document_page_' . $instance;
$sort_key   = 'document_sort_' . $instance;

$read = static function ($key) {
    return isset($_GET[$key]) && is_scalar($_GET[$key])
        ? sanitize_text_field(wp_unslash($_GET[$key]))
        : '';
};

$selected     = $read($filter_key) ?: 'all';
$sort         = $read($sort_key) === 'oldest' ? 'oldest' : 'newest';
$paged        = max(1, min(10000, absint($read($page_key))));
$category_ids = array_filter(array_map('absint', (array) get_field('all_documents_categories')));

$terms = get_terms([
    'taxonomy'   => 'publication_type',
    'hide_empty' => false,
    'include'    => $category_ids,
]);

if (is_wp_error($terms)) {
    $terms = [];
}

$projects = get_posts([
    'post_type'      => 'project',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
]);

$filters = ['all' => 'All'];

foreach ($terms as $term) {
    $filters['term-' . $term->term_id] = $term->name;
}

foreach ($projects as $project) {
    $filters['project-' . $project->ID] = get_the_title($project);
}

if (!isset($filters[$selected])) {
    $selected = 'all';
}

$args = [
    'post_type'      => 'publication',
    'post_status'    => 'publish',
    'has_password'   => false,
    'posts_per_page' => max(4, min(48, (int) (get_field('all_documents_per_page') ?: 16))),
    'paged'          => $paged,
    'orderby'        => [
        'date' => $sort === 'oldest' ? 'ASC' : 'DESC',
        'ID'   => 'DESC',
    ],
    'meta_query'     => [
        [
            'key'     => 'publication_pdf',
            'value'   => 0,
            'compare' => '>',
            'type'    => 'NUMERIC',
        ],
    ],
];

if (strpos($selected, 'term-') === 0) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'publication_type',
            'field'    => 'term_id',
            'terms'    => [absint(substr($selected, 5))],
        ],
    ];
}

if (strpos($selected, 'project-') === 0) {
    $args['meta_query'][] = [
        'key'     => 'publication_project',
        'value'   => absint(substr($selected, 8)),
        'compare' => '=',
        'type'    => 'NUMERIC',
    ];
}

$query = new WP_Query($args);

if ($query->max_num_pages && $paged > $query->max_num_pages) {
    $paged         = $query->max_num_pages;
    $args['paged'] = $paged;
    $query         = new WP_Query($args);
}

$base = remove_query_arg($page_key);
?>
<section
    <?php if ($section_id) : ?>
        id="<?php echo esc_attr($section_id); ?>"
    <?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    data-all-documents="<?php echo esc_attr($instance); ?>"
    data-filter-key="<?php echo esc_attr($filter_key); ?>"
    data-sort-key="<?php echo esc_attr($sort_key); ?>"
    data-page-key="<?php echo esc_attr($page_key); ?>"
    data-selected-filter="<?php echo esc_attr($selected); ?>"
>
    <div class="container">
        <div class="m-all-documents">
            <div class="m-all-documents__header s-d-b-s s-m-b-xs">
                <h2 class="a-heading a-heading--h4 h-dark-blue">
                    <?php echo esc_html(get_field('all_documents_title') ?: 'All documents'); ?>
                </h2>
                <label class="a-text a-text--m h-semibold">
                    Sort:
                    <select class="a-text a-text--m h-semibold" data-documents-sort aria-label="Sort documents">
                        <option value="newest"<?php selected($sort, 'newest'); ?>>Newest first</option>
                        <option value="oldest"<?php selected($sort, 'oldest'); ?>>Oldest first</option>
                    </select>
                </label>
            </div>
            <nav class="m-all-documents__filters" aria-label="Document filters">
                <?php foreach ($filters as $value => $label) : ?>
                    <a
                        class="a-text a-text--m h-semibold"
                        href="<?php echo esc_url(add_query_arg($filter_key, $value, $base)); ?>"
                        data-document-filter="<?php echo esc_attr($value); ?>"
                        <?php if ($selected === $value) : ?>
                            aria-current="true"
                        <?php endif; ?>
                    >
                        <?php echo esc_html($label); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="m-all-documents__results" aria-live="polite">
                <div class="m-all-documents__grid">
                    <?php foreach ($query->posts as $publication) :
                        $file = get_field('publication_pdf', $publication->ID) ?: [];

                        if (empty($file['url'])) {
                            continue;
                        }

                        $extension         = strtolower(pathinfo(parse_url($file['url'], PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));
                        $spreadsheet       = in_array($extension, ['xls', 'xlsx'], true);
                        $file_type         = $spreadsheet ? strtoupper($extension) : 'PDF';
                        $publication_terms = get_the_terms($publication->ID, 'publication_type');
                        $publication_terms = $publication_terms && !is_wp_error($publication_terms) ? $publication_terms : [];
                        $term              = $publication_terms[0] ?? null;
                        $project_id        = absint(get_field('publication_project', $publication->ID));
                        $caption = get_field('publication_document_caption', $publication->ID) ?: ($project_id ? get_the_title($project_id) : 'MCA Kosovo');
                        $tone              = $term
                            ? (['policies' => 'pink', 'board-records' => 'purple', 'legal-framework' => 'navy'][$term->slug] ?? 'blue')
                            : 'blue';
                        ?>
                        <article class="m-all-documents__card m-all-documents__card--<?php echo esc_attr($tone); ?>" data-document-id="<?php echo esc_attr($publication->ID); ?>">
                            <div class="m-all-documents__card-top">
                                <div class="m-all-documents__icon">
                                    <svg width="52" height="64" viewBox="0 0 52 64" role="img" aria-label="<?php echo esc_attr($file_type); ?>">
                                        <rect width="52" height="64" rx="10" fill="#F4F5F8"/>
                                        <path d="M38 0h4a10 10 0 0 1 10 10v4h-8a6 6 0 0 1-6-6V0Z" fill="currentColor"/>
                                        <text class="h-semibold" x="26" y="53" text-anchor="middle" font-family="Arial, sans-serif" font-size="12" fill="#CF1E4B"><?php echo esc_html($file_type); ?></text>
                                    </svg>
                                </div>
                                <?php if ($term) : ?>
                                    <div class="m-all-documents__type">
                                        <span class="a-text a-text--xs h-semibold"><?php echo esc_html($term->name); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="m-all-documents__copy">
                                <h3 class="a-text a-text--xxl h-semibold h-dark-blue">
                                    <a class="a-document-title" href="<?php echo esc_url(get_permalink($publication)); ?>">
                                        <?php echo esc_html(get_the_title($publication)); ?>
                                    </a>
                                </h3>
                                <div class="m-all-documents__caption">
                                    <span class="a-text a-text--xs h-semibold"><?php echo esc_html($caption); ?></span>
                                </div>
                            </div>
                            <div class="m-all-documents__card-footer s-d-t-xs s-m-t-xs">
                                <span class="a-text a-text--m h-semibold">Download</span>
                                <a class="a-document-download" href="<?php echo esc_url($file['url']); ?>" download aria-label="<?php echo esc_attr('Download ' . get_the_title($publication)); ?>">
                                    <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/white-download.svg')); ?>" alt="" aria-hidden="true">
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php if (!$query->posts) : ?>
                    <div class="m-all-documents__empty">
                        <p class="a-text a-text--l">No documents found for this filter.</p>
                    </div>
                <?php endif; ?>
                <div class="m-all-documents__more s-d-t-s s-m-t-xs">
                    <?php if ($paged < $query->max_num_pages) : ?>
                        <a class="a-btn a-btn--border-blue" href="<?php echo esc_url(add_query_arg([$filter_key => $selected, $sort_key => $sort, $page_key => $paged + 1], $base)); ?>" data-documents-more>
                            <span><?php echo esc_html(get_field('all_documents_more_label') ?: 'Load more documents'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="m-all-documents__message" role="status" data-documents-message></div>
        </div>
    </div>
</section>