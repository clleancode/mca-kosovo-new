<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();
if (!$visibility['show']) {
    return;
}

$layout = get_field('hero_collection_layout') ?: 'album';
$classes = ['o-hero-collection', 'o-hero-collection--' . sanitize_html_class($layout), $visibility['visibility_classes'], $block['className'] ?? ''];
foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");
        if ($space) {
            $classes[] = "s-{$prefix}-{$suffix}-{$space}";
        }
    }
}

$link_markup = static function ($link, $class = 'a-text a-text--xs') {
    if (!is_array($link) || empty($link['url']) || empty($link['title'])) {
        return;
    }
    $target = $link['target'] ?? '_self';
    ?>
    <a class="<?php echo esc_attr($class); ?>" href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr($target); ?>" <?php if ($target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
        <?php echo esc_html($link['title'] ?? ''); ?>
    </a>
    <?php
};

$render_breadcrumbs = static function ($rows) use ($link_markup) {
    $rows = is_array($rows) ? array_filter($rows, static function ($row) {
        $link = $row['link'] ?? [];
        return !empty($link['url']) || !empty($link['title']);
    }) : [];
    if (!$rows) {
        return;
    }
    ?>
    <nav class="m-hero-collection__breadcrumbs" aria-label="Breadcrumb">
        <ol>
            <?php foreach ($rows as $row) : ?>
                <?php $link = $row['link'] ?? []; ?>
                <?php if (!empty($link['url'])) : ?>
                    <li><?php $link_markup($link); ?></li>
                <?php elseif (!empty($link['title'])) : ?>
                    <li><span class="a-text a-text--xs" aria-current="page"><?php echo esc_html($link['title']); ?></span></li>
                <?php endif; ?>
            <?php endforeach; ?>
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
                        <a class="a-text a-text--m" href="<?php echo esc_url($back_link['url']); ?>" target="<?php echo esc_attr($back_link['target'] ?? '_self'); ?>" <?php if (($back_link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/left-arrow.svg')); ?>" alt="" aria-hidden="true">
                            <span><?php echo esc_html($back_link['title'] ?? ''); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if ($value = get_field('hero_album_title')) : ?><h1 class="a-heading a-heading--h4"><?php echo esc_html($value); ?></h1><?php endif; ?>
                    <?php $category = get_field('hero_album_category'); $year = get_field('hero_album_year'); $photo_count = get_field('hero_album_photo_count'); ?>
                    <?php if ($category || $year || $photo_count) : ?>
                    <div class="m-hero-collection__meta">
                        <?php if ($category) : ?><span class="m-hero-collection__pill m-hero-collection__pill--blue"><span class="a-text a-text--xs"><?php echo esc_html($category); ?></span></span><?php endif; ?>
                        <?php if ($year) : ?><span class="a-text a-text--xs"><?php echo esc_html($year); ?></span><?php endif; ?>
                        <?php if ($photo_count) : ?><span class="a-text a-text--xs"><?php echo esc_html($photo_count); ?></span><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php $slideshow_link = get_field('hero_album_slideshow_link'); $share_link = get_field('hero_album_share_link'); ?>
                <?php if ((!empty($slideshow_link['url']) && !empty($slideshow_link['title'])) || (!empty($share_link['url']) && !empty($share_link['title']))) : ?>
                    <div class="m-hero-collection__actions">
                        <?php $link_markup($slideshow_link, 'a-btn a-btn--white'); ?>
                        <?php $link_markup($share_link, 'a-btn a-btn--border-white h-white'); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($layout === 'vacancy') : ?>
            <div class="m-hero-collection m-hero-collection--vacancy">
                <?php $back_link = get_field('hero_vacancy_back_link'); ?>
                <?php if (is_array($back_link) && !empty($back_link['url']) && !empty($back_link['title'])) : ?>
                    <div class="m-hero-collection__back-link">
                        <a class="a-text a-text--m" href="<?php echo esc_url($back_link['url']); ?>" target="<?php echo esc_attr($back_link['target'] ?? '_self'); ?>" <?php if (($back_link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/left-arrow.svg')); ?>" alt="" aria-hidden="true">
                            <span><?php echo esc_html($back_link['title'] ?? ''); ?></span>
                        </a>
                    </div>
                <?php endif; ?>
                <?php $vacancy_status = get_field('hero_vacancy_status'); $vacancy_notice = get_field('hero_vacancy_notice'); $vacancy_project = get_field('hero_vacancy_project'); ?>
                <?php if ($vacancy_status || $vacancy_notice || $vacancy_project) : ?><div class="m-hero-collection__meta">
                    <?php if ($vacancy_status) : ?><span class="m-hero-collection__pill m-hero-collection__pill--muted m-hero-collection__pill--status"><span class="a-text a-text--xs"><?php echo esc_html($vacancy_status); ?></span></span><?php endif; ?>
                    <?php if ($vacancy_notice) : ?><span class="m-hero-collection__pill m-hero-collection__pill--outline"><span class="a-text a-text--xs"><?php echo esc_html($vacancy_notice); ?></span></span><?php endif; ?>
                    <?php if ($vacancy_project) : ?><span class="m-hero-collection__pill m-hero-collection__pill--blue"><span class="a-text a-text--xs"><?php echo esc_html($vacancy_project); ?></span></span><?php endif; ?>
                </div><?php endif; ?>
                <?php if ($value = get_field('hero_vacancy_title')) : ?><h1 class="a-heading a-heading--h3"><?php echo nl2br(wp_kses($value, ['br' => []])); ?></h1><?php endif; ?>
            </div>
        <?php elseif ($layout === 'integrity') : ?>
            <div class="m-hero-collection m-hero-collection--integrity">
                <div class="m-hero-collection__integrity-copy">
                    <?php if ($value = get_field('hero_integrity_label')) : ?><span class="m-hero-collection__pill m-hero-collection__pill--red"><span class="a-text a-text--xs"><?php echo esc_html($value); ?></span></span><?php endif; ?>
                    <?php if ($value = get_field('hero_integrity_title')) : ?><h1 class="a-heading a-heading--h4"><?php echo esc_html($value); ?></h1><?php endif; ?>
                    <?php if ($value = get_field('hero_integrity_description')) : ?><p class="a-text a-text--xl"><?php echo nl2br(esc_html($value)); ?></p><?php endif; ?>
                </div>
                <?php $panel_title = get_field('hero_integrity_panel_title'); $panel_description = get_field('hero_integrity_panel_description'); ?>
                <?php if ($panel_title || $panel_description) : ?><aside class="m-hero-collection__anonymous">
                    <svg viewBox="0 0 40 44" aria-hidden="true" focusable="false"><path d="M20 2 36 8v12c0 10-6.8 17.5-16 22C10.8 37.5 4 30 4 20V8L20 2Z"/><path d="m13 21 5 5 10-11"/></svg>
                    <?php if ($panel_title) : ?><h2 class="a-heading a-heading--h6"><?php echo nl2br(esc_html($panel_title)); ?></h2><?php endif; ?>
                    <?php if ($panel_description) : ?><p class="a-text a-text--m"><?php echo nl2br(esc_html($panel_description)); ?></p><?php endif; ?>
                </aside><?php endif; ?>
            </div>
        <?php elseif ($layout === 'policy') : ?>
            <div class="m-hero-collection m-hero-collection--policy">
                <?php $render_breadcrumbs(get_field('hero_policy_breadcrumbs')); ?>
                <?php if ($value = get_field('hero_policy_title')) : ?><h1 class="a-heading a-heading--h4"><?php echo esc_html($value); ?></h1><?php endif; ?>
                <?php $links = get_field('hero_policy_links'); ?>
                <?php $links = is_array($links) ? array_values(array_filter($links, static function ($row) { return !empty($row['link']['url']) && !empty($row['link']['title']); })) : []; ?>
                <?php if ($links) : ?>
                    <nav class="m-hero-collection__policy-links" aria-label="Policy navigation">
                        <?php foreach ($links as $index => $row) : ?>
                            <?php $link = $row['link'] ?? []; if (empty($link['url'])) { continue; } ?>
                            <span class="m-hero-collection__policy-link<?php echo $index === 0 ? ' is-current' : ''; ?>"><?php $link_markup($link, 'a-text a-text--m h-semibold'); ?></span>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>
                <?php $updated = trim((string) get_field('hero_policy_updated_label') . ' ' . (string) get_field('hero_policy_updated_date')); ?>
                <?php if ($updated) : ?><p class="a-text a-text--xs"><?php echo esc_html($updated); ?></p><?php endif; ?>
            </div>
        <?php elseif ($layout === 'vacancies') : ?>
            <div class="m-hero-collection m-hero-collection--vacancies">
                <div class="m-hero-collection__vacancies-copy">
                    <?php $render_breadcrumbs(get_field('hero_vacancies_breadcrumbs')); ?>
                    <?php if ($value = get_field('hero_vacancies_title')) : ?><h1 class="a-heading a-heading--h4"><?php echo esc_html($value); ?></h1><?php endif; ?>
                    <?php if ($value = get_field('hero_vacancies_description')) : ?><p class="a-text a-text--xl"><?php echo nl2br(esc_html($value)); ?></p><?php endif; ?>
                </div>
                <?php $statistics = get_field('hero_vacancies_statistics'); ?>
                <?php $statistics = is_array($statistics) ? array_filter($statistics, static function ($row) { return trim((string) ($row['value'] ?? '')) !== '' || !empty($row['label']); }) : []; ?>
                <?php if (is_array($statistics) && $statistics) : ?>
                    <div class="m-hero-collection__statistics">
                        <?php foreach ($statistics as $row) : ?>
                            <div class="m-hero-collection__statistic">
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
