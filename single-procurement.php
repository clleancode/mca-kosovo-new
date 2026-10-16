<?php
get_header();
while (have_posts()) : the_post();
    $procurement_id = get_the_ID();
    if (!has_block('acf/small-hero', get_the_ID())) {
        get_template_part('template-parts/blocks/small-hero/procurement-hero', null, ['post_id' => get_the_ID()]);
    }
    $notices_block = null;
    $join_block = null;
    // Render these once at the end, including blocks placed manually or in patterns.
    $collect_tail = static function ($pre_render, $parsed_block) use (&$notices_block, &$join_block) {
        if (($parsed_block['blockName'] ?? '') === 'acf/notices') {
            $notices_block = $notices_block ?: $parsed_block;
            return '';
        }
        if (($parsed_block['blockName'] ?? '') === 'acf/join') {
            $join_block = $join_block ?: $parsed_block;
            return '';
        }
        return $pre_render;
    };
    add_filter('pre_render_block', $collect_tail, 10, 2);
    try {
        the_content();
    } finally {
        remove_filter('pre_render_block', $collect_tail, 10);
    }
    $default_notices = \MCA\Notices\get_default_procurement_notices();
    $notices_block = $notices_block ?: $default_notices;
    foreach (['notices_layout', 'notices_selected', 'notices_title'] as $field) {
        $notices_block['attrs']['data'][$field] = $default_notices['attrs']['data'][$field];
        $notices_block['attrs']['data']['_' . $field] = $default_notices['attrs']['data']['_' . $field];
    }
    if (empty($notices_block['attrs']['data']['notices_header_link']['url']) || $notices_block['attrs']['data']['notices_header_link']['url'] === '#') {
        $notices_block['attrs']['data']['notices_header_link'] = $default_notices['attrs']['data']['notices_header_link'];
        $notices_block['attrs']['data']['_notices_header_link'] = $default_notices['attrs']['data']['_notices_header_link'];
    }
    echo render_block($notices_block);
    $join_block = $join_block ?: \MCA\Notices\get_default_procurement_join($procurement_id);
    if ($join_block) echo render_block($join_block);
endwhile;
get_footer();
