<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

    get_header();

    $term = get_queried_object();
    $term_slug = isset($term->slug) ? $term->slug : '';
    $term_name = isset($term->name) ? $term->name : '';
    $is_tag = is_tag();
    $is_taxonomy = is_tax();

    $post_types = ['publications', 'congress-materials', 'events'];

    $args = [
        'post_type'      => $post_types,
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if ($is_taxonomy) {
        $args['tax_query'] = [
            [
                'taxonomy' => $term->taxonomy,
                'field'    => 'slug',
                'terms'    => $term_slug,
            ]
        ];
    }

    if ($is_tag) {
        $args['tag'] = $term_slug;
    }

    $query = new WP_Query($args);

    $posts_by_type = [];
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $posts_by_type[get_post_type()][] = get_the_ID();
        }
        wp_reset_postdata();
    }

    $first_post_type = null;
    foreach ($post_types as $post_type) {
        if (!empty($posts_by_type[$post_type])) {
            $first_post_type = $post_type;
            break;
        }
    }

    $total_posts = array_sum(array_map('count', $posts_by_type));
?>
    <div class="o-content s-d-t-xl s-m-t-xl" data-is-taxonomy="<?php echo esc_attr( $is_taxonomy ? '1' : '0' ); ?>" data-is-tag="<?php echo esc_attr( $is_tag ? '1' : '0' ); ?>" data-term-slug="<?php echo esc_attr( $term_slug ); ?>" data-term-taxonomy="<?php echo esc_attr( $term->taxonomy ?? '' ); ?>">
		<div class="container">
            <div class="flex grid--span-3 s-m-b-s s-d-b-s">
                <div>
                    <h1 class="a-heading a-heading--h2 h-green">
                        <?php echo $is_tag ? 'Tag: ' . esc_html($term_name) : 'Category: ' . esc_html($term_name); ?>
                    </h1>
                    <p class="a-text a-text--l">
                        <?php echo esc_html($total_posts); ?> result<?php echo $total_posts === 1 ? '' : 's'; ?> found
                    </p>
                </div>
                <?php if ($post_type === $first_post_type) : ?>
                    <div class="o-filter flex">
                        <p class="a-p h-dark-blue">Sort by</p>
                        <div class="m-filter">
                            <select name="sorting" id="archive-sort" class="a-dropdown">
                                <option value="most_recent" selected>Most recent</option>
                                <option value="less_recent">Less recent</option>
                                <option value="alphabetical_order">Alphabetical order</option>
                            </select>
                            <i class="icon-filter a-icon" aria-hidden="true"></i>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="m-separator s-m-t-xs s-m-b-s s-d-t-xs s-d-b-m">
                <div class="a-separator"></div>
            </div>
            <?php 
                if (!empty($posts_by_type)) :
                    foreach ($post_types as $post_type) :
                        if (!empty($posts_by_type[$post_type])) : 
            ?>
            <div class="flex grid--span-3">
                <h2 class="a-heading a-heading--h2 h-blue"><?php echo esc_html(ucwords(str_replace('-', ' ', $post_type))); ?></h2>
            </div>
            <div class="o-cards--inner row--1-3 s-m-t-m s-d-t-m" data-post-type="<?php echo esc_attr($post_type); ?>">                            <?php foreach ($posts_by_type[$post_type] as $post_id) : ?>
                                <?php
                                    if ($post_type === 'publications') {
                                        echo render_publication_card($post_id);
                                    } elseif ($post_type === 'congress-materials') {
                                        echo render_congress_materials_card($post_id);
                                    } elseif ($post_type === 'events') {
                                        echo render_event_card($post_id);
                                    }
                                ?>
                            <?php endforeach; ?>
                        </div>
                        <div class="m-separator s-m-t-l s-m-b-l s-d-t-l s-d-b-l">
                            <div class="a-separator"></div>
                        </div>
            <?php 
                        endif;
                    endforeach;
                else : 
            ?>
                <p class="no-results">No posts found for this <?php echo $is_tag ? 'tag' : 'category'; ?>.</p>
            <?php endif; ?>
        </div>
	</div>
<?php get_footer(); ?>