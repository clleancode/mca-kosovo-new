<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-opportunities',
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

$setting = static function ($name, $allowed, $default) {
    $value = get_field($name);

    return in_array($value, $allowed, true) ? $value : $default;
};

$colors = ['h-white', 'h-dark-blue', 'h-blue', 'h-purple', 'h-dark-green'];

$tag = $setting('header_tag', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'], 'h2');
$size = $setting('header_size', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], 'h4');
$heading_color = $setting('header_color', $colors, 'h-dark-blue');
$heading_height = $setting('header_line_height', ['3xs', '2xs', 'xs', 's', 'm', 'ls', 'l', 'xl'], 's');
$text_size = $setting('text_type', ['xs', 's', 'm', 'l', 'xl', 'xxl'], 'm');
$text_color = $setting('text_color', $colors, 'h-dark-blue');
$text_height = $setting('text_line_height', ['3xs', '2xs', 'xs', 's', 'm', 'l', 'xl'], 'l');

$cards = array_slice(get_field('opportunities_cards') ?: [], 0, 4);

$query = new WP_Query([
    'post_type'      => 'procurement',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'no_found_rows'  => true,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

$archive = get_field('opportunities_procurement_link') ?: [];
$archive_url = $archive['url'] ?? '';
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-opportunities__intro">
            <div class="m-opportunities__heading">
                <p class="a-badge h-dark-blue"><?php echo esc_html(get_field('opportunities_label')); ?></p>
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($heading_color); ?> h--<?php echo esc_attr($heading_height); ?>">
                    <?php echo esc_html(get_field('opportunities_title')); ?>
                </<?php echo tag_escape($tag); ?>>
            </div>
            <div class="m-opportunities__description">
                <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                    <?php echo nl2br(esc_html(get_field('opportunities_description'))); ?>
                </p>
            </div>
        </div>

        <div class="m-opportunities__layout">
            <div class="m-opportunities__cards">
                <?php foreach ($cards as $index => $card) : ?>
                    <?php
                    $link = $card['link'] ?? [];
                    $url = $link['url'] ?? '';
                    $label = $card['label'] ?? '';
                    $target = $link['target'] ?? '';
                    ?>
                    <article class="m-opportunities__card">
                        <?php if (!empty($card['image'])) : ?>
                            <?php echo wp_get_attachment_image($card['image'], 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                        <?php endif; ?>

                        <div class="m-opportunities__card-top">
                            <?php if ($label) : ?>
                                <span class="a-text a-text--xs h-semibold"><?php echo esc_html($label); ?></span>
                            <?php endif; ?>
                            <span class="a-text a-text--s h-semibold"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                        </div>

                        <div class="m-opportunities__card-content">
                            <h3 class="a-heading a-heading--h5 h-white"><?php echo esc_html($card['title'] ?? ''); ?></h3>
                            <?php if (!empty($card['description'])) : ?>
                                <p class="a-text a-text--l h-white"><?php echo esc_html($card['description']); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if ($url) : ?>
                            <a
                                class="m-opportunities__card-link"
                                href="<?php echo esc_url($url); ?>"
                                target="<?php echo esc_attr($target); ?>"
                                aria-label="<?php echo esc_attr($card['title'] ?? ''); ?>"
                                <?php if ($target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                            >
                                <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="m-opportunities__procurements">
                <div class="m-opportunities__panel-header">
                    <p class="a-text a-text--xs h-semibold"><?php echo esc_html(get_field('opportunities_procurement_label')); ?></p>
                    <a class="a-text a-text--xs h-semibold" href="<?php echo esc_url($archive_url); ?>">
                        <?php echo esc_html($archive['title'] ?? ''); ?>
                        <span aria-hidden="true" class="a-text a-text--m">→</span>
                    </a>
                </div>

                <?php foreach ($query->posts as $procurement) : ?>
                    <?php
                    $terms = get_the_terms($procurement->ID, 'procurement_status');
                    $terms = $terms && !is_wp_error($terms) ? $terms : [];
                    $closed = in_array('closed', wp_list_pluck($terms, 'slug'), true);
                    $deadline = get_field('deadline', $procurement->ID);
                    $phase = get_field('procurement_phase', $procurement->ID);
                    $notice = get_field('procurement_notice_type', $procurement->ID);
                    ?>
                    <article class="m-opportunities__notice">
                        <div class="m-opportunities__notice-meta<?php echo $closed ? ' m-opportunities__notice-meta--closed' : ''; ?>">
                            <?php foreach ($terms as $term) : ?>
                                <span class="a-text a-text--xs h-semibold"><?php echo esc_html($term->name); ?></span>
                            <?php endforeach; ?>
                            <?php if ($notice) : ?>
                                <span class="a-text a-text--xs h-semibold"><?php echo esc_html($notice); ?></span>
                            <?php endif; ?>
                        </div>

                        <h3 class="a-text a-text--xxl h-dark-blue">
                            <a href="<?php echo esc_url(get_permalink($procurement)); ?>"><?php echo esc_html(get_the_title($procurement)); ?></a>
                        </h3>

                        <div class="m-opportunities__notice-dates">
                            <span class="a-text a-text--xs h-semibold">
                                Published
                                <time datetime="<?php echo esc_attr(get_the_date('c', $procurement)); ?>"><?php echo esc_html(get_the_date('d.m.Y', $procurement)); ?></time>
                            </span>
                            <?php if ($deadline) : ?>
                                <span class="a-text a-text--xs h-semibold">Deadline <?php echo esc_html($deadline); ?></span>
                            <?php endif; ?>
                            <?php if ($phase) : ?>
                                <span class="a-text a-text--xs h-semibold"><?php echo esc_html($phase); ?></span>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if (!$query->posts) : ?>
                    <p class="a-text a-text--m">No procurement notices have been published yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>