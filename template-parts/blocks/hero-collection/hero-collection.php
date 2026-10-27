<?php
use MCA\Helpers;
use MCA\Notices;

require_once get_template_directory() . '/inc/procurement-notices.php';

$visibility = Helpers\mca_get_device_visibility();
if (!$visibility['show']) {
    return;
}

$layout = get_field('hero_collection_layout') ?: 'album';
$heading = Helpers\get_block_typography([
    'header_tag' => 'h1',
    'header_size' => $layout === 'vacancy' ? 'h3' : 'h4',
    'header_color' => '',
]);
$heading_classes = 'a-heading a-heading--' . $heading['header_size'] . ' ' . $heading['header_color'];
if (get_field('header_line_height')) $heading_classes .= ' h--' . $heading['header_line_height'];
$classes = ['o-hero-collection', 'o-hero-collection--' . sanitize_html_class($layout), $visibility['visibility_classes'], $block['className'] ?? ''];
foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");
        if ($space) {
            $classes[] = "s-{$prefix}-{$suffix}-{$space}";
        }
    }
}

$link_markup = static function ($link, $class = 'a-text a-text--xs', $delay = null) {
    if (!is_array($link) || empty($link['url']) || empty($link['title'])) {
        return;
    }
    $target = $link['target'] ?? '_self';
    ?>
    <a class="<?php echo esc_attr($class); ?>" <?php if ($delay !== null) : ?>data-offset="0" data-delay="<?php echo esc_attr($delay); ?>" <?php endif; ?>href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr($target); ?>" <?php if ($target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
        <?php if (strpos($class, 'a-btn') !== false) : ?><span><?php echo esc_html($link['title'] ?? ''); ?></span><?php else : ?><?php echo esc_html($link['title'] ?? ''); ?><?php endif; ?>
    </a>
    <?php
};

$page_id = get_queried_object_id();
if (!$page_id || get_post_type($page_id) !== 'page') {
    $page_id = get_the_ID();
}

$render_breadcrumbs = static function () use ($link_markup, $page_id) {
    if (!$page_id) {
        return;
    }
    $ancestors = array_reverse(get_post_ancestors($page_id));
    ?>
    <nav class="m-hero-collection__breadcrumbs e-reveal" data-offset="0" aria-label="Breadcrumb">
        <ol>
            <li><?php $link_markup(['url' => home_url('/'), 'title' => __('Home', 'MCA')]); ?></li>
            <?php foreach ($ancestors as $ancestor_id) : ?>
                <li><?php $link_markup(['url' => get_permalink($ancestor_id), 'title' => get_the_title($ancestor_id)]); ?></li>
            <?php endforeach; ?>
            <li><span class="a-text a-text--xs" aria-current="page"><?php echo esc_html(get_the_title($page_id)); ?></span></li>
        </ol>
    </nav>
    <?php
};
?>
<section <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?> class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <?php if ($layout === 'album') : ?>
            <div class="m-hero-collection m-hero-collection--album">
                <div class="m-hero-collection__album-copy">
                    <?php $back_link = get_field('hero_album_back_link'); ?>
                    <?php if (is_array($back_link) && !empty($back_link['url']) && !empty($back_link['title'])) : ?>
                        <a class="a-text a-text--m e-reveal" data-offset="0" href="<?php echo esc_url($back_link['url']); ?>" target="<?php echo esc_attr($back_link['target'] ?? '_self'); ?>" <?php if (($back_link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/left-arrow.svg')); ?>" alt="" aria-hidden="true">
                            <span><?php echo esc_html($back_link['title'] ?? ''); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if ($value = get_field('hero_album_title')) : ?><<?php echo tag_escape($heading['header_tag']); ?> class="<?php echo esc_attr(trim($heading_classes)); ?> e-reveal e-reveal--text" data-offset="0" data-delay="100"><?php echo esc_html($value); ?></<?php echo tag_escape($heading['header_tag']); ?>><?php endif; ?>
                    <?php $category = get_field('hero_album_category'); $year = get_field('hero_album_year'); $photo_count = get_field('hero_album_photo_count'); ?>
                    <?php if ($category || $year || $photo_count) : ?>
                    <div class="m-hero-collection__meta">
                        <?php if ($category) : ?><span class="m-hero-collection__pill m-hero-collection__pill--blue e-reveal" data-offset="0" data-delay="350"><span class="a-text a-text--xs"><?php echo esc_html($category); ?></span></span><?php endif; ?>
                        <?php if ($year) : ?><span class="a-text a-text--xs e-reveal e-reveal--text" data-offset="0" data-delay="430"><?php echo esc_html($year); ?></span><?php endif; ?>
                        <?php if ($photo_count) : ?><span class="a-text a-text--xs e-reveal e-reveal--text" data-offset="0" data-delay="510"><?php echo esc_html($photo_count); ?></span><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php $slideshow_link = get_field('hero_album_slideshow_link'); $share_link = get_field('hero_album_share_link'); ?>
                <?php if ((!empty($slideshow_link['url']) && !empty($slideshow_link['title'])) || (!empty($share_link['url']) && !empty($share_link['title']))) : ?>
                    <div class="m-hero-collection__actions">
                        <?php $link_markup($slideshow_link, 'a-btn a-btn--white e-reveal', 600); ?>
                        <?php $link_markup($share_link, 'a-btn a-btn--border-white h-white e-reveal', 700); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($layout === 'vacancy') : ?>
            <div class="m-hero-collection m-hero-collection--vacancy">
                <?php $back_link = get_field('hero_vacancy_back_link'); ?>
                <?php if (is_array($back_link) && !empty($back_link['url']) && !empty($back_link['title'])) : ?>
                    <div class="m-hero-collection__back-link e-reveal" data-offset="0">
                        <a class="a-text a-text--m" href="<?php echo esc_url($back_link['url']); ?>" target="<?php echo esc_attr($back_link['target'] ?? '_self'); ?>" <?php if (($back_link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/left-arrow.svg')); ?>" alt="" aria-hidden="true">
                            <span><?php echo esc_html($back_link['title'] ?? ''); ?></span>
                        </a>
                    </div>
                <?php endif; ?>
                <?php
                $vacancy_id = get_the_ID();
                if (!$vacancy_id || get_post_type($vacancy_id) !== 'job') {
                    $vacancy_id = 0;
                }
                $vacancy_deadline = $vacancy_id ? Notices\get_deadline($vacancy_id) : false;
                $vacancy_is_closed = $vacancy_deadline
                    ? $vacancy_deadline < new DateTimeImmutable('today', wp_timezone())
                    : ($vacancy_id ? has_term('closed', 'job_status', $vacancy_id) : false);
                $vacancy_status = $vacancy_id ? ($vacancy_is_closed ? __('Closed', 'MCA') : __('Ongoing', 'MCA')) : '';
                $vacancy_notice = get_field('hero_vacancy_notice');
                $vacancy_project = $vacancy_id ? get_field('job_department', $vacancy_id) : '';
                ?>
                <?php if ($vacancy_status || $vacancy_notice || $vacancy_project) : ?><div class="m-hero-collection__meta">
                    <?php if ($vacancy_status) : ?><span class="m-hero-collection__pill m-hero-collection__pill--muted m-hero-collection__pill--status e-reveal" data-offset="0" data-delay="100"><span class="a-text a-text--xs"><?php echo esc_html($vacancy_status); ?></span></span><?php endif; ?>
                    <?php if ($vacancy_notice) : ?><span class="m-hero-collection__pill m-hero-collection__pill--outline e-reveal" data-offset="0" data-delay="180"><span class="a-text a-text--xs"><?php echo esc_html($vacancy_notice); ?></span></span><?php endif; ?>
                    <?php if ($vacancy_project) : ?><span class="m-hero-collection__pill m-hero-collection__pill--blue e-reveal" data-offset="0" data-delay="260"><span class="a-text a-text--xs"><?php echo esc_html($vacancy_project); ?></span></span><?php endif; ?>
                </div><?php endif; ?>
                <?php if ($value = get_field('hero_vacancy_title')) : ?><<?php echo tag_escape($heading['header_tag']); ?> class="<?php echo esc_attr(trim($heading_classes)); ?> e-reveal e-reveal--text" data-offset="0" data-delay="350"><?php echo nl2br(wp_kses($value, ['br' => []])); ?></<?php echo tag_escape($heading['header_tag']); ?>><?php endif; ?>
            </div>
        <?php elseif ($layout === 'integrity') : ?>
            <div class="m-hero-collection m-hero-collection--integrity">
                <div class="m-hero-collection__integrity-copy">
                    <?php if ($value = get_field('hero_integrity_label')) : ?><span class="m-hero-collection__pill m-hero-collection__pill--red e-reveal" data-offset="0"><span class="a-text a-text--xs"><?php echo esc_html($value); ?></span></span><?php endif; ?>
                    <?php if ($value = get_field('hero_integrity_title')) : ?><<?php echo tag_escape($heading['header_tag']); ?> class="<?php echo esc_attr(trim($heading_classes)); ?> e-reveal e-reveal--text" data-offset="0" data-delay="100"><?php echo esc_html($value); ?></<?php echo tag_escape($heading['header_tag']); ?>><?php endif; ?>
                    <?php if ($value = get_field('hero_integrity_description')) : ?><p class="a-text a-text--xl e-reveal e-reveal--text" data-offset="0" data-delay="350"><?php echo nl2br(esc_html($value)); ?></p><?php endif; ?>
                </div>
                <?php $panel_title = get_field('hero_integrity_panel_title'); $panel_description = get_field('hero_integrity_panel_description'); ?>
                <?php if ($panel_title || $panel_description) : ?><aside class="m-hero-collection__anonymous e-reveal" data-offset="0" data-delay="500">
                    <svg viewBox="0 0 40 44" aria-hidden="true" focusable="false"><path d="M20 2 36 8v12c0 10-6.8 17.5-16 22C10.8 37.5 4 30 4 20V8L20 2Z"/><path d="m13 21 5 5 10-11"/></svg>
                    <?php if ($panel_title) : ?><h2 class="a-heading a-heading--h6"><?php echo nl2br(esc_html($panel_title)); ?></h2><?php endif; ?>
                    <?php if ($panel_description) : ?><p class="a-text a-text--m"><?php echo nl2br(esc_html($panel_description)); ?></p><?php endif; ?>
                </aside><?php endif; ?>
            </div>
        <?php elseif ($layout === 'policy') : ?>
            <div class="m-hero-collection m-hero-collection--policy">
                <?php $render_breadcrumbs(); ?>
                <?php if ($value = get_field('hero_policy_title')) : ?><<?php echo tag_escape($heading['header_tag']); ?> class="<?php echo esc_attr(trim($heading_classes)); ?> e-reveal e-reveal--text" data-offset="0" data-delay="100"><?php echo esc_html($value); ?></<?php echo tag_escape($heading['header_tag']); ?>><?php endif; ?>
                <?php $links = get_field('hero_policy_links'); ?>
                <?php $links = is_array($links) ? array_values(array_filter($links, static function ($row) { return !empty($row['link']['url']) && !empty($row['link']['title']); })) : []; ?>
                <?php if ($links) : ?>
                    <nav class="m-hero-collection__policy-links" aria-label="Policy navigation">
                        <?php foreach ($links as $index => $row) : ?>
                            <?php $link = $row['link'] ?? []; if (empty($link['url'])) { continue; } ?>
                            <span class="m-hero-collection__policy-link<?php echo $index === 0 ? ' is-current' : ''; ?> e-reveal" data-offset="0" data-delay="<?php echo esc_attr(350 + $index * 80); ?>"><?php $link_markup($link, 'a-text a-text--m h-semibold'); ?></span>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>
                <?php $updated = trim((string) get_field('hero_policy_updated_label') . ' ' . (string) get_field('hero_policy_updated_date')); ?>
                <?php if ($updated) : ?><p class="a-text a-text--xs e-reveal e-reveal--text" data-offset="0" data-delay="<?php echo esc_attr(350 + count($links) * 80); ?>"><?php echo esc_html($updated); ?></p><?php endif; ?>
            </div>
        <?php elseif ($layout === 'vacancies') : ?>
            <div class="m-hero-collection m-hero-collection--vacancies">
                <div class="m-hero-collection__vacancies-copy">
                    <?php $render_breadcrumbs(); ?>
                    <?php if ($value = get_field('hero_vacancies_title')) : ?><<?php echo tag_escape($heading['header_tag']); ?> class="<?php echo esc_attr(trim($heading_classes)); ?> e-reveal e-reveal--text" data-offset="0" data-delay="100"><?php echo esc_html($value); ?></<?php echo tag_escape($heading['header_tag']); ?>><?php endif; ?>
                    <?php if ($value = get_field('hero_vacancies_description')) : ?><p class="a-text a-text--xl e-reveal e-reveal--text" data-offset="0" data-delay="350"><?php echo nl2br(esc_html($value)); ?></p><?php endif; ?>
                </div>
                <?php $statistics = get_field('hero_vacancies_statistics'); ?>
                <?php $statistics = is_array($statistics) ? array_filter($statistics, static function ($row) { return trim((string) ($row['value'] ?? '')) !== '' || !empty($row['label']); }) : []; ?>
                <?php if (is_array($statistics) && $statistics) : ?>
                    <div class="m-hero-collection__statistics">
                        <?php foreach ($statistics as $stat_index => $row) : ?>
                            <div class="m-hero-collection__statistic e-reveal" data-offset="0" data-delay="<?php echo esc_attr(500 + ($stat_index % 3) * 120); ?>">
                                <?php if (trim((string) ($row['value'] ?? '')) !== '') : ?><span class="a-heading a-heading--h3"><?php echo esc_html($row['value']); ?></span><?php endif; ?>
                                <?php if (!empty($row['label'])) : ?><span class="a-text a-text--xs"><?php echo esc_html($row['label']); ?></span><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
