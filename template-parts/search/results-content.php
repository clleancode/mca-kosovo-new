<?php
$query = $args['query'] ?? null;
$term = $args['term'] ?? '';
$type = $args['type'] ?? 'all';
$page = max(1, (int) ($args['page'] ?? 1));

if (!$query instanceof WP_Query) {
    return;
}
?>
<?php if ($query->have_posts()) : ?>
    <div class="m-search-results__list">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
            <?php
            $result_id = get_the_ID();
            $result_type = get_post_type($result_id);
            $is_attachment = $result_type === 'attachment';
            $mime_type = $is_attachment ? get_post_mime_type($result_id) : '';
            $result_url = $is_attachment ? wp_get_attachment_url($result_id) : get_permalink($result_id);
            $type_labels = [
                'post' => __('News', 'mca-kosovo'),
                'procurement' => __('Procurement', 'mca-kosovo'),
                'publication' => __('Document', 'mca-kosovo'),
                'attachment' => __('Document', 'mca-kosovo'),
                'page' => __('Page', 'mca-kosovo'),
                'project' => __('Page', 'mca-kosovo'),
            ];
            $post_type_object = get_post_type_object($result_type);
            $type_label = $type_labels[$result_type] ?? ($post_type_object ? $post_type_object->labels->singular_name : '');
            $metadata = '';

            if ($result_type === 'post') {
                $news_terms = get_the_terms($result_id, 'category');
                $metadata = is_array($news_terms) ? implode(' · ', wp_list_pluck($news_terms, 'name')) : get_the_date('M Y');
            } elseif ($result_type === 'procurement') {
                $metadata = get_field('procurement_notice_type', $result_id) ?: get_the_date('M Y', $result_id);
            } elseif ($result_type === 'publication' || $is_attachment) {
                $metadata = $is_attachment && $mime_type === 'application/pdf'
                    ? __('Publication · PDF', 'mca-kosovo')
                    : __('Publication', 'mca-kosovo');
            } else {
                $metadata = wp_parse_url(get_permalink($result_id), PHP_URL_PATH);
                $metadata = trim((string) $metadata, '/');
            }

            $excerpt = get_the_excerpt($result_id);
            if (!$excerpt && !$is_attachment) {
                $excerpt = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $result_id)), 22);
            }

            $placeholder = $result_type === 'procurement'
                ? __('Notice', 'mca-kosovo')
                : ($is_attachment && $mime_type === 'application/pdf' ? __('PDF', 'mca-kosovo') : $type_label);
            ?>
            <article class="m-search-results__item">
                <a class="m-search-results__item-link" href="<?php echo esc_url($result_url); ?>">
                    <span class="m-search-results__media">
                        <?php if (has_post_thumbnail($result_id)) : ?>
                            <?php echo get_the_post_thumbnail($result_id, 'thumbnail', ['loading' => 'lazy', 'decoding' => 'async']); ?>
                        <?php else : ?>
                            <span class="a-text a-text--m"><?php echo esc_html($placeholder); ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="m-search-results__copy">
                        <span class="m-search-results__meta">
                            <span class="a-text a-text--xs"><?php echo esc_html($type_label); ?></span>
                            <?php if ($metadata) : ?><span class="a-text a-text--xs"><?php echo esc_html($metadata); ?></span><?php endif; ?>
                        </span>
                        <span class="a-heading a-heading--h6"><?php echo esc_html(get_the_title($result_id)); ?></span>
                        <?php if ($excerpt) : ?><span class="a-text a-text--m"><?php echo esc_html($excerpt); ?></span><?php endif; ?>
                    </span>
                    <span class="m-search-results__arrow" aria-hidden="true"><i class="icon-arrow-right-up a-icon a-icon--xs"></i></span>
                </a>
            </article>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>

    <?php if ($query->max_num_pages > 1) : ?>
        <nav class="m-search-results__pagination" aria-label="<?php esc_attr_e('Search results pages', 'mca-kosovo'); ?>">
            <ul>
                <?php if ($page > 1) : ?>
                    <?php $previous_url = add_query_arg(['s' => $term, 'search_type' => $type, 'paged' => $page - 1], home_url('/')); ?>
                    <li><a href="<?php echo esc_url($previous_url); ?>" aria-label="<?php esc_attr_e('Previous page', 'mca-kosovo'); ?>">&laquo;</a></li>
                <?php endif; ?>
                <?php for ($page_number = 1; $page_number <= (int) $query->max_num_pages; $page_number++) : ?>
                    <?php $page_url = add_query_arg(['s' => $term, 'search_type' => $type, 'paged' => $page_number], home_url('/')); ?>
                    <li>
                        <?php if ($page_number === $page) : ?>
                            <span class="current" aria-current="page"><?php echo esc_html(number_format_i18n($page_number)); ?></span>
                        <?php else : ?>
                            <a href="<?php echo esc_url($page_url); ?>" aria-label="<?php echo esc_attr(sprintf(__('Page %s', 'mca-kosovo'), $page_number)); ?>"><?php echo esc_html(number_format_i18n($page_number)); ?></a>
                        <?php endif; ?>
                    </li>
                <?php endfor; ?>
                <?php if ($page < (int) $query->max_num_pages) : ?>
                    <?php $next_url = add_query_arg(['s' => $term, 'search_type' => $type, 'paged' => $page + 1], home_url('/')); ?>
                    <li><a href="<?php echo esc_url($next_url); ?>" aria-label="<?php esc_attr_e('Next page', 'mca-kosovo'); ?>"><?php esc_html_e('Next', 'mca-kosovo'); ?> &raquo;</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php else : ?>
    <p class="a-text a-text--m"><?php esc_html_e('No results found. Please try a different search.', 'mca-kosovo'); ?></p>
<?php endif; ?>
