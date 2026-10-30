<?php
namespace MCA\SearchResults;

function get_types(): array {
    return [
        'all' => [
            'label' => __('All', 'mca'),
            'args' => [
                'post_type' => ['post', 'procurement', 'publication', 'page', 'project', 'attachment'],
                'post_status' => ['publish', 'inherit'],
                'mca_search_all' => true,
            ],
        ],
        'news' => [
            'label' => __('News', 'mca'),
            'args' => ['post_type' => 'post', 'post_status' => 'publish'],
        ],
        'procurement' => [
            'label' => __('Procurement', 'mca'),
            'args' => ['post_type' => 'procurement', 'post_status' => 'publish'],
        ],
        'documents' => [
            'label' => __('Documents', 'mca'),
            'args' => \MCA\Helpers\get_document_search_args(),
        ],
        'pages' => [
            'label' => __('Pages', 'mca'),
            'args' => ['post_type' => ['page', 'project'], 'post_status' => 'publish'],
        ],
    ];
}

function get_results_query(string $term, string $type, int $page): \WP_Query {
    $types = get_types();
    if (!isset($types[$type])) {
        $type = 'all';
    }

    return new \WP_Query(array_merge($types[$type]['args'], [
        's' => $term,
        'posts_per_page' => 4,
        'paged' => max(1, $page),
        'ignore_sticky_posts' => true,
    ]));
}

function ajax_pagination(): void {
    check_ajax_referer('mca_search_pagination', 'nonce');

    $term = isset($_POST['s']) && is_string($_POST['s']) ? sanitize_text_field(wp_unslash($_POST['s'])) : '';
    $type = isset($_POST['search_type']) && is_string($_POST['search_type']) ? sanitize_key(wp_unslash($_POST['search_type'])) : 'all';
    $page = isset($_POST['paged']) && is_scalar($_POST['paged']) ? absint($_POST['paged']) : 1;
    $types = get_types();

    if (!$term || !isset($types[$type])) {
        wp_send_json_error(['message' => __('The requested search page is not available.', 'mca')], 400);
    }

    $query = get_results_query($term, $type, $page);
    if ($page > max(1, (int) $query->max_num_pages)) {
        wp_send_json_error(['message' => __('The requested search page is not available.', 'mca')], 404);
    }

    ob_start();
    get_template_part('template-parts/search/results-content', null, [
        'query' => $query,
        'term' => $term,
        'type' => $type,
        'page' => $page,
    ]);
    $content = ob_get_clean();

    wp_send_json_success([
        'content' => $content,
        'url' => add_query_arg(['s' => $term, 'search_type' => $type, 'paged' => $page], home_url('/')),
    ]);
}

add_action('wp_ajax_mca_search_pagination', __NAMESPACE__ . '\\ajax_pagination');
add_action('wp_ajax_nopriv_mca_search_pagination', __NAMESPACE__ . '\\ajax_pagination');
