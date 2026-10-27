<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-more-albums',
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

$current_album = is_singular('gallery') ? get_queried_object_id() : 0;
$selection     = array_values(array_diff(
    array_filter(array_map('absint', (array) get_field('more_albums_selection'))),
    [$current_album]
));

$args = [
    'post_type'      => 'gallery',
    'post_status'    => 'publish',
    'posts_per_page' => 2,
    'post__not_in'   => $current_album ? [$current_album] : [],
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
];

if ($selection) {
    $args['post__in'] = $selection;
    $args['orderby']  = 'post__in';
}

$albums = get_posts($args);

if (!$albums && empty($is_preview)) {
    return;
}

$title    = (string) get_field('more_albums_title');
$icon_url = get_template_directory_uri() . '/assets/img/pics/icons/album.svg';
?>
<section
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
>
    <div class="container">
        <div class="m-more-albums">
            <h2 class="a-heading a-heading--h5 h-dark-blue e-reveal e-reveal--text"><?php echo esc_html($title); ?></h2>
            <div class="m-more-albums__grid">
                <?php
                foreach ($albums as $album_index => $album) :
                    $photos = Helpers\mca_get_album_photos($album->ID);
                    $cover  = get_post_thumbnail_id($album->ID) ?: ($photos[0] ?? 0);
                    ?>
                    <div class="m-more-albums__card e-reveal e-reveal--image" data-delay="<?php echo esc_attr(150 + ($album_index % 2) * 120); ?>">
                        <a
                            href="<?php echo esc_url(get_permalink($album)); ?>"
                            aria-label="<?php echo esc_attr(sprintf(__('View album: %s', 'MCA'), get_the_title($album))); ?>"
                        >
                            <?php
                            if ($cover) {
                                echo wp_get_attachment_image($cover, 'large', false, ['loading' => 'lazy']);
                            }
                            ?>
                            <span class="a-text a-text--xs h-uppercase h-semibold">
                                <img
                                    src="<?php echo esc_url($icon_url); ?>"
                                    width="12"
                                    height="10"
                                    alt=""
                                    aria-hidden="true"
                                >
                                <?php esc_html_e('Album', 'MCA'); ?>
                            </span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>