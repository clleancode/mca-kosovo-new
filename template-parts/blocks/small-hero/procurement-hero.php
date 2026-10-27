<?php
use MCA\Notices;
$post_id = $args['post_id'] ?? get_the_ID();
$classes = $args['classes'] ?? ['o-small-hero'];
$classes[] = 'm-small-hero--procurement';
$anchor = $args['anchor'] ?? '';
$image = ($args['image'] ?? 0) ?: get_post_thumbnail_id($post_id);
$tag = $args['tag'] ?? 'h1';
$size = $args['size'] ?? 'h4';
$page = get_page_by_path('procurement');
$archive = $page ? get_permalink($page) : get_post_type_archive_link('procurement');
[$status, $status_label] = Notices\get_status($post_id);
$notice_type = get_field('procurement_notice_type', $post_id);
if (!$notice_type) {
    $terms = get_the_terms($post_id, 'procurement_status');
    if ($terms && !is_wp_error($terms)) foreach ($terms as $term) {
        if (in_array($term->slug, ['specific-procurement-notice', 'general-procurement-notice', 'award-notice'], true)) { $notice_type = $term->name; break; }
    }
}
$method = get_field('procurement_method', $post_id);
$project_id = absint(get_field('procurement_project', $post_id));
$project = $project_id ? get_the_title($project_id) : '';
?>
<section <?php if ($anchor): ?>id="<?php echo esc_attr($anchor); ?>" <?php endif; ?>class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <?php if ($image): ?><div class="m-small-hero__image" data-parallax><?php echo wp_get_attachment_image($image, 'full', false, ['loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async']); ?></div><?php endif; ?>
    <div class="container">
        <div class="m-small-hero__procurement">
            <div class="m-small-hero__back s-d-b-s s-m-b-xs e-reveal" data-offset="0"><a class="a-text a-text--s h-white" href="<?php echo esc_url($archive); ?>"><img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/left-arrow.svg')); ?>" alt="" aria-hidden="true"> All procurements</a></div>
            <div class="m-small-hero__procurement-badges s-d-b-xs s-m-b-xs">
                <div class="m-small-hero__status m-small-hero__status--<?php echo esc_attr($status); ?> e-reveal" data-offset="0" data-delay="100"><span class="a-text a-text--xs"><?php echo esc_html($status_label); ?></span></div>
                <?php if ($notice_type): ?><div class="m-small-hero__notice-type e-reveal" data-offset="0" data-delay="180"><span class="a-text a-text--xs h-white"><?php echo esc_html($notice_type); ?></span></div><?php endif; ?>
                <?php if ($method): ?><div class="m-small-hero__notice-type e-reveal" data-offset="0" data-delay="260"><span class="a-text a-text--xs h-white"><?php echo esc_html($method); ?></span></div><?php endif; ?>
                <?php if ($project): ?><div class="m-small-hero__project e-reveal" data-offset="0" data-delay="340"><span class="a-text a-text--xs h-dark-blue"><?php echo esc_html($project); ?></span></div><?php endif; ?>
            </div>
            <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> h-white h-uppercase e-reveal e-reveal--text" data-offset="0" data-delay="420"><?php echo esc_html(get_the_title($post_id)); ?></<?php echo tag_escape($tag); ?>>
        </div>
    </div>
</section>
