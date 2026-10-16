<?php
namespace MCA\Notices;

function find_join_block(array $blocks, array $visited = []) {
    foreach ($blocks as $block) {
        if (($block['blockName'] ?? '') === 'acf/join') return $block;
        if (($block['blockName'] ?? '') === 'core/block' && !empty($block['attrs']['ref'])) {
            $ref = absint($block['attrs']['ref']);
            if (!in_array($ref, $visited, true)) {
                $referenced = find_join_block(parse_blocks(get_post_field('post_content', $ref)), array_merge($visited, [$ref]));
                if ($referenced) return $referenced;
            }
        }
        $nested = find_join_block($block['innerBlocks'] ?? [], $visited);
        if ($nested) return $nested;
    }
    return null;
}

function get_default_procurement_join(int $post_id) {
    if (get_post_type($post_id) !== 'procurement' || find_join_block(parse_blocks(get_post_field('post_content', $post_id)))) return null;
    $source_id = absint(get_field('procurement_join_source', 'option'));
    if (!$source_id || get_post_status($source_id) !== 'publish') return null;
    $block = find_join_block(parse_blocks(get_post_field('post_content', $source_id)));
    if ($block) unset($block['attrs']['anchor']);
    return $block;
}

function get_default_procurement_notices(): array {
    $page = get_page_by_path('procurement');
    $values = [
        'notices_layout' => 'selected',
        'notices_title' => 'Latest notices',
        'notices_selected' => [],
        'notices_header_link' => ['url' => $page ? get_permalink($page) : get_post_type_archive_link('procurement'), 'title' => 'All procurements', 'target' => ''],
        'space_desktop_top' => 's', 'space_desktop_bottom' => 'm',
        'space_mobile_top' => 's', 'space_mobile_bottom' => 's',
    ];
    $data = [];
    foreach ($values as $name => $value) {
        $data[$name] = $value;
        $data['_' . $name] = 'field_notices_fields_' . $name;
    }
    return ['blockName' => 'acf/notices', 'attrs' => ['name' => 'acf/notices', 'data' => $data, 'mode' => 'preview'], 'innerBlocks' => [], 'innerHTML' => '', 'innerContent' => []];
}

function get_filters() {
    $read = static function ($name) {
        return isset($_GET[$name]) && is_scalar($_GET[$name]) ? sanitize_text_field(wp_unslash($_GET[$name])) : '';
    };
    $status = $read('notice_status');
    $type = $read('notice_type');
    return [
        'search' => mb_substr($read('notice_search'), 0, 200),
        'status' => in_array($status, ['ongoing', 'closed'], true) ? $status : 'all',
        'type' => in_array($type, ['general', 'specific', 'award'], true) ? $type : 'all',
        'project' => absint($read('notice_project')),
        'sort' => $read('notice_sort') === 'oldest' ? 'oldest' : 'newest',
        'page' => max(1, min(10000, absint($read('notice_page')))),
    ];
}

function query_args(array $filters, $per_page) {
    $args = ['post_type' => 'procurement', 'post_status' => 'publish', 'posts_per_page' => $per_page, 'paged' => $filters['page'], 'orderby' => ['date' => $filters['sort'] === 'oldest' ? 'ASC' : 'DESC', 'ID' => 'DESC']];
    if ($filters['search'] !== '') {
        $args['s'] = $filters['search'];
        $args['mca_notice_search'] = $filters['search'];
    }
    $tax = ['relation' => 'AND'];
    if ($filters['status'] !== 'all') $tax[] = ['taxonomy' => 'procurement_status', 'field' => 'slug', 'terms' => $filters['status'] === 'closed' ? ['closed'] : ['ongoing', 'open', 'closing-soon', 'in-evaluation']];
    $meta = ['relation' => 'AND'];
    if ($filters['type'] !== 'all') {
        $term = ['general' => 'general-procurement-notice', 'specific' => 'specific-procurement-notice', 'award' => 'award-notice'][$filters['type']];
        // Notice types are stored using the existing procurement taxonomy.
        $tax[] = ['taxonomy' => 'procurement_status', 'field' => 'slug', 'terms' => [$term]];
    }
    if ($filters['project']) $meta[] = ['key' => 'procurement_project', 'value' => $filters['project'], 'compare' => '=', 'type' => 'NUMERIC'];
    if (count($tax) > 1) $args['tax_query'] = $tax;
    if (count($meta) > 1) $args['meta_query'] = $meta;
    return $args;
}

add_filter('posts_search', function ($search, $query) {
    $term = $query->get('mca_notice_search');
    if (!$term) return $search;
    global $wpdb;
    $like = '%' . $wpdb->esc_like($term) . '%';
    return $wpdb->prepare(" AND ({$wpdb->posts}.post_title LIKE %s OR EXISTS (SELECT 1 FROM {$wpdb->postmeta} notice_reference WHERE notice_reference.post_id = {$wpdb->posts}.ID AND notice_reference.meta_key = 'procurement_reference' AND notice_reference.meta_value LIKE %s)) ", $like, $like);
}, 10, 2);

function get_status($post_id) {
    $terms = get_the_terms($post_id, 'procurement_status');
    $slugs = $terms && !is_wp_error($terms) ? wp_list_pluck($terms, 'slug') : [];
    foreach (['award-notice' => ['award', 'Award'], 'closed' => ['closed', 'Closed'], 'in-evaluation' => ['evaluation', 'In evaluation'], 'closing-soon' => ['closing', 'Closing soon'], 'ongoing' => ['open', 'Open'], 'open' => ['open', 'Open'], 'general-procurement-notice' => ['general', 'General notice']] as $slug => $status) {
        if (in_array($slug, $slugs, true)) return $status;
    }
    return ['general', 'Notice'];
}

function get_deadline($post_id) {
    $value = get_field('deadline', $post_id);
    if (!$value || !is_string($value)) return false;
    foreach (['!d.m.Y', '!Ymd', '!Y-m-d'] as $format) {
        $date = \DateTimeImmutable::createFromFormat($format, $value, wp_timezone());
        $errors = \DateTimeImmutable::getLastErrors();
        if ($date && (!$errors || (!$errors['warning_count'] && !$errors['error_count']))) return $date;
    }
    return false;
}
