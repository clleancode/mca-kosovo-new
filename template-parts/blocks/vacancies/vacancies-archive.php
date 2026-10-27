<?php
/** Searchable vacancy archive, rendered by vacancies.php. */
$search_key = 'vacancy_search_' . $instance;
$team_key = 'vacancy_team_' . $instance;
$read_filter = static function ($key) {
    return isset($_GET[$key]) && is_scalar($_GET[$key]) ? sanitize_text_field(wp_unslash($_GET[$key])) : '';
};
$search = $read_filter($search_key);
$team = $read_filter($team_key);
$selected = $read_filter($status_key);
$selected = in_array($selected, ['ongoing', 'closed'], true) ? $selected : 'all';
$archive_base = remove_query_arg([$page_key]);
$teams = [];
$status_ids = ['ongoing' => [], 'closed' => []];
foreach (get_posts(['post_type' => 'job', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids']) as $job_id) {
    $status_ids[$get_job_state($job_id)][] = $job_id;
    $department = trim((string) get_field('job_department', $job_id));
    if ($department !== '') $teams[$department] = $department;
}
natcasesort($teams);
$archive_args = [
    'post_type' => 'job', 'post_status' => 'publish',
    's' => $search, 'search_columns' => ['post_title'],
    'orderby' => ['date' => 'DESC', 'ID' => 'DESC'],
];
if ($team !== '') $archive_args['meta_query'] = [['key' => 'job_department', 'value' => $team, 'compare' => '=']];
$status_args = static function ($state) use ($status_ids) {
    return $state === 'all' ? [] : ['post__in' => $status_ids[$state] ?: [0]];
};
$counts = [];
foreach (['all', 'ongoing', 'closed'] as $state) {
    $count_query = new WP_Query(array_merge($archive_args, $status_args($state), ['posts_per_page' => 1, 'fields' => 'ids']));
    $counts[$state] = (int) $count_query->found_posts;
}
$query_args = array_merge($archive_args, $status_args($selected), ['posts_per_page' => $per_page, 'paged' => $page]);
$archive_query = new WP_Query($query_args);
if ($archive_query->max_num_pages && $page > $archive_query->max_num_pages) {
    $page = (int) $archive_query->max_num_pages;
    $query_args['paged'] = $page;
    $archive_query = new WP_Query($query_args);
}
$alerts_link = get_field('vacancies_alerts_link') ?: [];
$archive_title = get_field('vacancies_title');
?>
<section class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?> o-vacancies--archive" <?php if ($section_id) : ?>id="<?php echo esc_attr($section_id); ?>"<?php endif; ?> data-vacancies-archive="<?php echo esc_attr($instance); ?>">
    <div class="container">
        <div class="m-vacancies">
            <?php if ($archive_title) : ?>
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> s-d-b-s s-m-b-xs e-reveal e-reveal--text"><?php echo esc_html($archive_title); ?></<?php echo tag_escape($tag); ?>>
            <?php endif; ?>
            <div class="m-vacancies__filters">
                <div class="m-vacancies__filter-top">
                    <form class="m-vacancies__search e-reveal" data-delay="150" method="get" action="<?php echo esc_url(remove_query_arg([$search_key, $status_key, $team_key, $page_key])); ?>" data-vacancy-search>
                        <?php foreach ($_GET as $key => $value) : if (!is_scalar($value) || in_array($key, [$search_key, $status_key, $team_key, $page_key], true)) continue; ?>
                            <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr(wp_unslash($value)); ?>">
                        <?php endforeach; ?>
                        <input type="hidden" name="<?php echo esc_attr($status_key); ?>" value="<?php echo esc_attr($selected); ?>">
                        <input type="hidden" name="<?php echo esc_attr($team_key); ?>" value="<?php echo esc_attr($team); ?>">
                        <input class="a-text a-text--m" type="search" name="<?php echo esc_attr($search_key); ?>" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Search by job title', 'MCA'); ?>" aria-label="<?php esc_attr_e('Search by job title', 'MCA'); ?>">
                        <button class="a-btn a-btn--blue a-btn--smallest" type="submit"><span><?php esc_html_e('Search', 'MCA'); ?></span></button>
                    </form>
                    <nav class="m-vacancies__tabs e-reveal" data-delay="270" aria-label="<?php esc_attr_e('Vacancy status', 'MCA'); ?>">
                        <?php foreach (['all' => 'All', 'ongoing' => 'Ongoing', 'closed' => 'Closed'] as $state => $label) : ?>
                            <a class="a-text a-text--m" data-vacancy-filter href="<?php echo esc_url(add_query_arg($status_key, $state, $archive_base)); ?>"<?php if ($selected === $state) : ?> aria-current="true"<?php endif; ?>><?php echo esc_html($label); ?> <span class="a-text a-text--xs"><?php echo esc_html($counts[$state]); ?></span></a>
                        <?php endforeach; ?>
                    </nav>
                </div>
                <nav class="m-vacancies__teams" aria-label="<?php esc_attr_e('Filter by team', 'MCA'); ?>">
                    <?php $team_index = 0; ?>
                    <?php foreach (['' => __('All teams', 'MCA')] + $teams as $value => $label) : ?>
                        <a class="a-text a-text--m e-reveal" data-delay="<?php echo esc_attr(350 + $team_index++ * 80); ?>" data-vacancy-filter href="<?php echo esc_url(add_query_arg($team_key, $value, $archive_base)); ?>"<?php if ($team === (string) $value) : ?> aria-current="true"<?php endif; ?>><?php echo esc_html($label); ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>
            <?php if (!$status_ids['ongoing'] && $selected !== 'ongoing') : ?>
                <div class="m-vacancies__notice e-reveal" data-delay="300">
                    <p class="a-text a-text--m"><?php echo esc_html(get_field('vacancies_archive_notice')); ?></p>
                    <?php if (!empty($alerts_link['url'])) : ?>
                        <a class="a-text a-text--m" href="<?php echo esc_url($alerts_link['url']); ?>"<?php if (($alerts_link['target'] ?? '') === '_blank') : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html($alerts_link['title'] ?: __('Get job alerts', 'MCA')); ?> <i class="icon-arrow-right-up a-icon a-icon--xs" aria-hidden="true"></i></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="m-vacancies__results" aria-live="polite">
                <?php if ($archive_query->posts) : $render_cards($archive_query->posts, $selected === 'all' ? null : $selected); else : ?>
                    <p class="a-text a-text--l e-reveal e-reveal--text"><?php esc_html_e('No positions match your search and filters.', 'MCA'); ?></p>
                <?php endif; ?>
            </div>
            <?php if ($archive_query->max_num_pages > 1) : ?>
                <nav class="m-vacancies__pagination e-reveal" data-delay="150" aria-label="<?php esc_attr_e('Vacancy pages', 'MCA'); ?>">
                    <?php echo paginate_links(['base' => str_replace('999999999', '%#%', add_query_arg($page_key, 999999999, $archive_base)), 'format' => '', 'current' => $page, 'total' => $archive_query->max_num_pages, 'prev_text' => '&larr;', 'next_text' => '&rarr;']); ?>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</section>
