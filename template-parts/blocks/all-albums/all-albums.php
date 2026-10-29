<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-all-albums',
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

$selected = array_filter(array_map('absint', (array) get_field('all_albums_selection')));

$query_args = [
    'post_type'      => 'gallery',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
];

if ($selected) {
    $query_args['post__in'] = $selected;
    $query_args['orderby']  = 'post__in';
}

$albums     = get_posts($query_args);
$categories = [];
$years      = [];
$album_data = [];

foreach ($albums as $album) {
    $terms = get_the_terms($album->ID, 'gallery_category');
    $terms = $terms && !is_wp_error($terms) ? $terms : [];

    foreach ($terms as $term) {
        $categories[$term->term_id] = $term->name;
    }

    $year         = get_the_date('Y', $album);
    $years[$year] = $year;
    $images       = Helpers\mca_get_album_photos($album->ID);

    $album_data[$album->ID] = [
        'images' => $images,
        'terms'  => wp_list_pluck($terms, 'term_id'),
        'year'   => $year,
    ];
}

krsort($years);

$source       = absint(get_field('all_albums_photos_source'));
$photo_pool   = [];
$photo_albums = $source
    ? get_posts([
        'post_type'   => 'gallery',
        'post_status' => 'publish',
        'include'     => [$source],
    ])
    : $albums;

usort($photo_albums, static function ($first, $second) {
    return strcmp($second->post_modified_gmt, $first->post_modified_gmt)
        ?: ($second->ID <=> $first->ID);
});

foreach ($photo_albums as $album) {
    foreach (array_reverse(Helpers\mca_get_album_photos($album->ID)) as $image_id) {
        if ($image_id && !isset($photo_pool[$image_id])) {
            $photo_pool[$image_id] = [
                'id'    => $image_id,
                'url'   => get_permalink($album),
                'album' => get_the_title($album),
            ];
        }
    }
}

$photo_pool = array_values($photo_pool);

$count = max(1, min(15, (int) get_field('all_albums_photo_count')));

$album_category = isset($_GET['album_category']) && is_scalar($_GET['album_category'])
    ? sanitize_text_field(wp_unslash($_GET['album_category']))
    : 'all';

if ($album_category !== 'all' && !isset($categories[absint($album_category)])) {
    $album_category = 'all';
}

$album_base = remove_query_arg('album_category');
$year_id    = wp_unique_id('album-year-');
$icon_url   = get_template_directory_uri() . '/assets/img/pics/icons/album.svg';
?>
<section
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    data-all-albums
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
>
    <div class="container">
        <div class="m-all-albums">
            <div class="m-all-albums__header">
                <nav class="m-all-albums__filters" aria-label="Album categories">
                    <a
                        class="a-text a-text--m h-semibold e-reveal"
                        href="<?php echo esc_url(add_query_arg('album_category', 'all', $album_base)); ?>"
                        data-album-category="all"
                        <?php if ($album_category === 'all') : ?>aria-current="true"<?php endif; ?>
                    >All albums</a>
                    <?php $filter_index = 0; ?>
                    <?php foreach ($categories as $term_id => $name) : ?>
                        <?php $filter_index++; ?>
                        <a
                            class="a-text a-text--m h-semibold e-reveal"
                            data-delay="<?php echo esc_attr($filter_index * 80); ?>"
                            href="<?php echo esc_url(add_query_arg('album_category', $term_id, $album_base)); ?>"
                            data-album-category="<?php echo esc_attr($term_id); ?>"
                            <?php if ((string) $album_category === (string) $term_id) : ?>aria-current="true"<?php endif; ?>
                        >
                            <?php echo esc_html($name); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <div class="m-all-albums__year e-reveal" data-delay="<?php echo esc_attr(min(800, (count($categories) + 1) * 80)); ?>">
                    <label class="a-text a-text--m" for="<?php echo esc_attr($year_id); ?>">Year:</label>
                    <select class="a-text a-text--m" id="<?php echo esc_attr($year_id); ?>" data-album-year>
                        <option value="all">All</option>
                        <?php foreach ($years as $year) : ?>
                            <option value="<?php echo esc_attr($year); ?>"><?php echo esc_html($year); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="m-all-albums__grid">
                <?php
                $visible_albums = 0;

                foreach ($albums as $album_index => $album) :
                    $data             = $album_data[$album->ID];
                    $cover            = get_post_thumbnail_id($album->ID) ?: ($data['images'][0] ?? 0);
                    $matches_category = $album_category === 'all'
                        || in_array(absint($album_category), $data['terms'], true);
                    $featured         = $matches_category && $visible_albums === 0;

                    if ($matches_category) {
                        $visible_albums++;
                    }
                    ?>
                    <article
                        class="m-all-albums__album<?php echo $featured ? ' is-featured' : ''; ?> e-reveal"
                        data-delay="<?php echo esc_attr(150 + ($album_index % 2) * 120); ?>"
                        data-album-card
                        <?php if (!$matches_category) : ?>hidden<?php endif; ?>
                        data-categories="<?php echo esc_attr(implode(' ', $data['terms'])); ?>"
                        data-year="<?php echo esc_attr($data['year']); ?>"
                    >
                        <?php if ($cover) : ?>
                            <div class="m-all-albums__image">
                                <?php echo wp_get_attachment_image($cover, 'large', false, ['loading' => 'lazy']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="m-all-albums__badge">
                            <span class="a-text a-text--xs h-uppercase h-semibold">
                                <span data-album-photo-label <?php if (!$featured) : ?>hidden<?php endif; ?>>
                                    <img
                                        src="<?php echo esc_url($icon_url); ?>"
                                        width="12"
                                        height="10"
                                        alt=""
                                        aria-hidden="true"
                                    >
                                    <?php echo esc_html(count($data['images']) . ' photos'); ?>
                                </span>
                                <span data-album-label <?php if ($featured) : ?>hidden<?php endif; ?>>
                                    <img
                                        src="<?php echo esc_url($icon_url); ?>"
                                        width="12"
                                        height="10"
                                        alt=""
                                        aria-hidden="true"
                                    >
                                    <?php esc_html_e('Album', 'mca'); ?>
                                </span>
                            </span>
                        </div>
                        <div class="m-all-albums__copy">
                            <p class="a-text a-text--xs h-semibold h-white"><?php echo esc_html($data['year']); ?></p>
                            <h2 class="a-heading <?php echo $featured ? 'a-heading--h3' : 'a-heading--h5'; ?> h-white">
                                <a class="h-white" href="<?php echo esc_url(get_permalink($album)); ?>">
                                    <?php echo esc_html(get_the_title($album)); ?>
                                </a>
                            </h2>
                        </div>
                        <div class="m-all-albums__arrow">
                            <a
                                href="<?php echo esc_url(get_permalink($album)); ?>"
                                aria-label="<?php echo esc_attr(sprintf(__('View album: %s', 'mca'), get_the_title($album))); ?>"
                            >
                                <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="a-text a-text--m" data-album-empty <?php echo $visible_albums ? 'hidden' : ''; ?> role="status">No albums found.</p>
            <?php if (get_field('all_albums_show_photos') && $photo_pool) : ?>
                <div class="m-all-albums__latest">
                    <div class="m-all-albums__header">
                        <h2 class="a-heading a-heading--h5 e-reveal e-reveal--text">
                            <?php echo esc_html(get_field('all_albums_photos_title')); ?>
                        </h2>
                        <?php if ($source && $photo_albums) : ?>
                            <p class="a-text a-text--xs h-semibold h-uppercase e-reveal e-reveal--text" data-delay="150">
                                From “<?php echo esc_html(get_the_title($source)); ?>”;
                            </p>
                        <?php endif; ?>
                    </div>
                    <div
                        class="m-all-albums__photos"
                        data-album-photos
                        data-photo-count="<?php echo esc_attr($count); ?>"
                    >
                        <?php foreach ($photo_pool as $index => $photo) : ?>
                            <div class="m-all-albums__photo e-reveal e-reveal--image" data-delay="<?php echo esc_attr(150 + ($index % 5) * 120); ?>" data-album-photo <?php echo $index >= $count ? 'hidden' : ''; ?>>
                                <a
                                    href="<?php echo esc_url($photo['url']); ?>"
                                    aria-label="<?php echo esc_attr('View album: ' . $photo['album']); ?>"
                                >
                                    <?php echo wp_get_attachment_image($photo['id'], 'medium_large', false, ['loading' => 'lazy']); ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>