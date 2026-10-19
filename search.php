<?php
get_header();

$search_term = get_search_query();
$search_types = \MCA\SearchResults\get_types();
$selected_type = isset($_GET['search_type']) && is_string($_GET['search_type'])
    ? sanitize_key(wp_unslash($_GET['search_type']))
    : 'all';

if (!isset($search_types[$selected_type])) {
    $selected_type = 'all';
}

$count_for_type = static function (array $args) use ($search_term) {
    $query = new WP_Query(array_merge($args, [
        's' => $search_term,
        'posts_per_page' => 1,
        'fields' => 'ids',
        'paged' => 1,
        'no_found_rows' => false,
        'ignore_sticky_posts' => true,
    ]));

    return (int) $query->found_posts;
};

$search_counts = [];
foreach ($search_types as $type_key => $type_data) {
    if ($type_key !== 'all') {
        $search_counts[$type_key] = $count_for_type($type_data['args']);
    }
}
$search_counts['all'] = array_sum($search_counts);

$paged = max(1, (int) get_query_var('paged'));
$results_query = \MCA\SearchResults\get_results_query($search_term, $selected_type, $paged);
?>

<div class="o-search-results s-d-t-xl s-m-t-xl" data-search-results data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('mca_search_pagination')); ?>">
    <section class="m-search-results__hero">
        <div class="container">
            <form class="m-search-results__form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="hidden" name="search_type" value="<?php echo esc_attr($selected_type); ?>">
                <label class="a-text a-text--s" for="search-results-query"><?php esc_html_e('Search', 'mca-kosovo'); ?></label>
                <div class="m-search-results__input-wrap">
                    <input class="a-heading a-heading--h6" id="search-results-query" type="search" name="s" value="<?php echo esc_attr($search_term); ?>" required>
                    <a class="m-search-results__clear" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('Clear search', 'mca-kosovo'); ?>">&times;</a>
                    <button class="a-text a-text--m" type="submit"><?php esc_html_e('Search', 'mca-kosovo'); ?></button>
                </div>
                <?php if ($search_term) : ?>
                    <p class="a-text a-text--l"><?php printf(esc_html__('Showing results for “%s”', 'mca-kosovo'), esc_html($search_term)); ?></p>
                <?php endif; ?>
            </form>
        </div>
    </section>

    <section class="m-search-results">
        <div class="container">
            <nav class="m-search-results__filters" aria-label="<?php esc_attr_e('Filter search results', 'mca-kosovo'); ?>">
                <?php foreach ($search_types as $type_key => $type_data) : ?>
                    <?php $filter_url = add_query_arg(['s' => $search_term, 'search_type' => $type_key], home_url('/')); ?>
                    <a class="m-search-results__filter<?php echo $selected_type === $type_key ? ' is-active' : ''; ?>" href="<?php echo esc_url($filter_url); ?>" <?php echo $selected_type === $type_key ? 'aria-current="page"' : ''; ?>>
                        <span class="a-text a-text--m"><?php echo esc_html($type_data['label']); ?></span>
                        <span class="a-text a-text--xs"><?php echo esc_html(number_format_i18n($search_counts[$type_key])); ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div data-search-content aria-live="polite">
                <?php get_template_part('template-parts/search/results-content', null, [
                    'query' => $results_query,
                    'term' => $search_term,
                    'type' => $selected_type,
                    'page' => $paged,
                ]); ?>
            </div>
            <p class="a-text a-text--s" data-search-error hidden><?php esc_html_e('Search results could not be loaded. Please try again.', 'mca-kosovo'); ?></p>
        </div>
    </section>
</div>

<script>
(() => {
    const root = document.querySelector('[data-search-results]');
    const content = root?.querySelector('[data-search-content]');
    const errorMessage = root?.querySelector('[data-search-error]');
    if (!root || !content || !errorMessage) return;

    const loadPage = async (url, updateHistory = true) => {
        const target = new URL(url, window.location.href);
        const body = new URLSearchParams({
            action: 'mca_search_pagination',
            nonce: root.dataset.nonce,
            s: target.searchParams.get('s') || '',
            search_type: target.searchParams.get('search_type') || 'all',
            paged: target.searchParams.get('paged') || '1',
        });

        root.classList.add('is-loading');
        errorMessage.hidden = true;
        try {
            const response = await fetch(root.dataset.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: body.toString(),
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error('Search pagination failed');

            content.innerHTML = result.data.content;
            if (updateHistory) window.history.pushState({}, '', result.data.url);
            content.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (error) {
            errorMessage.hidden = false;
        } finally {
            root.classList.remove('is-loading');
        }
    };

    root.addEventListener('click', event => {
        const link = event.target.closest?.('.m-search-results__pagination a');
        if (!link || root.classList.contains('is-loading') || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        loadPage(link.href);
    });

    window.addEventListener('popstate', () => loadPage(window.location.href, false));
})();
</script>

<?php get_footer(); ?>
