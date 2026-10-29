<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
global $wp_query;
$total = (int) $wp_query->found_posts;
$object = get_queried_object();
$title = is_post_type_archive() && $object instanceof WP_Post_Type ? $object->labels->name : get_the_archive_title();
$sort = isset($_GET['sorting']) && is_string($_GET['sorting']) ? sanitize_key(wp_unslash($_GET['sorting'])) : 'most_recent';
$sort_options = [
    'most_recent' => __('Most recent', 'MCA'),
    'less_recent' => __('Less recent', 'MCA'),
    'alphabetical_order' => __('Alphabetical order', 'MCA'),
];
if (!isset($sort_options[$sort])) { $sort = 'most_recent'; }
?>
<div class="o-content s-d-t-xl s-m-t-xl">
    <div class="container">
        <div class="flex grid--span-3 s-m-b-s s-d-b-s">
            <div>
                <h1 class="a-heading a-heading--h2 h-green e-reveal e-reveal--text" data-delay="100"><?php echo esc_html(wp_strip_all_tags($title)); ?></h1>
                <p class="a-text a-text--l e-reveal e-reveal--text" data-delay="350"><?php printf(esc_html(_n('%s result found', '%s results found', $total, 'MCA')), esc_html(number_format_i18n($total))); ?></p>
            </div>
            <?php if ($total > 1): ?>
                <form class="o-filter flex e-reveal" data-delay="500" method="get" action="<?php echo esc_url(get_pagenum_link(1, false)); ?>">
                    <?php
                    foreach (['post_type', 'taxonomy', 'term', 'tag', 'tag_id', 'author', 'author_name', 'm', 'year', 'monthnum', 'day'] as $key) {
                        $value = get_query_var($key);
                        if (is_scalar($value) && '' !== (string) $value && false !== $value) {
                            printf('<input type="hidden" name="%s" value="%s">', esc_attr($key), esc_attr((string) $value));
                        }
                    }
                    ?>
                    <label class="a-text" for="archive-sort"><?php esc_html_e('Sort by', 'MCA'); ?></label>
                    <select name="sorting" id="archive-sort" class="a-dropdown">
                        <?php foreach ($sort_options as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($sort, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="a-btn"><?php esc_html_e('Apply', 'MCA'); ?></button>
                </form>
            <?php endif; ?>
        </div>
        <div class="m-separator s-m-t-xs s-m-b-s s-d-t-xs s-d-b-m"><div class="a-separator e-reveal e-reveal--clip"></div></div>
        <?php if (have_posts()): ?>
            <div class="o-cards--inner row--1-3 s-m-t-m s-d-t-m">
                <?php while (have_posts()): the_post(); ?>
                    <article <?php post_class('m-archive-card e-reveal'); ?> data-delay="150" data-post-type="<?php echo esc_attr(get_post_type()); ?>">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('medium_large', ['loading' => 'lazy', 'decoding' => 'async']); ?>
                            <?php endif; ?>
                            <h2 class="a-heading a-heading--h4"><?php echo esc_html(get_the_title()); ?></h2>
                        </a>
                        <?php if (has_excerpt()): ?><p class="a-text a-text--m"><?php echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></p><?php endif; ?>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination([
                'mid_size' => 2,
                'prev_text' => __('Previous', 'MCA'),
                'next_text' => __('Next', 'MCA'),
                'add_args' => isset($_GET['sorting']) ? ['sorting' => $sort] : false,
            ]); ?>
        <?php else: ?>
            <p class="no-results a-text a-text--m e-reveal e-reveal--text"><?php esc_html_e('No records found in this archive.', 'MCA'); ?></p>
        <?php endif; ?>
    </div>
</div>
<?php get_footer(); ?>
