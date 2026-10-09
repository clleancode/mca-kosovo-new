<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-filter',
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

$projects = get_posts([
    'post_type'      => 'project',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
]);

$links = get_field('filter_links') ?: [];
$id    = sanitize_html_class($block['id'] ?? 'procurement-filter');

$status_options = [
    'all'     => 'All',
    'ongoing' => 'Ongoing',
    'closed'  => 'Closed',
];

$type_options = [
    'all'      => 'All types',
    'general'  => 'General notice',
    'specific' => 'Specific notice',
    'award'    => 'Award notice',
];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <form class="m-filter" data-procurement-filter role="search" aria-label="Procurement notice filters">
            <div class="m-filter__body">
                <div class="m-filter__search">
                    <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/search.svg')); ?>" alt="" aria-hidden="true">
                    <input
                        class="a-text a-text--l"
                        type="search"
                        name="notice_search"
                        placeholder="<?php echo esc_attr(get_field('filter_placeholder')); ?>"
                        aria-label="Search notices by title or reference number"
                    >
                    <button class="a-btn a-btn--blue" type="submit">
                        <span class="a-text a-text--m h-semibold">
                            <?php echo esc_html(get_field('filter_search_label')); ?>
                            <i class="icon-arrow-right a-icon" aria-hidden="true"></i>
                        </span>
                    </button>
                </div>

                <div class="m-filter__controls">
                    <fieldset class="m-filter__group">
                        <legend class="a-text a-text--xs h-semibold">Status</legend>
                        <div class="m-filter__options m-filter__options--status">
                            <?php foreach ($status_options as $value => $label) : ?>
                                <button
                                    class="a-text a-text--l"
                                    type="button"
                                    data-filter-choice="status"
                                    data-value="<?php echo esc_attr($value); ?>"
                                    aria-pressed="<?php echo $value === 'all' ? 'true' : 'false'; ?>"
                                >
                                    <?php echo esc_html($label); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="status" value="all">
                    </fieldset>

                    <fieldset class="m-filter__group">
                        <legend class="a-text a-text--xs h-semibold">Notice type</legend>
                        <div class="m-filter__options m-filter__options--type">
                            <?php foreach ($type_options as $value => $label) : ?>
                                <button
                                    class="a-text a-text--l"
                                    type="button"
                                    data-filter-choice="notice_type"
                                    data-value="<?php echo esc_attr($value); ?>"
                                    aria-pressed="<?php echo $value === 'all' ? 'true' : 'false'; ?>"
                                >
                                    <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/tick.svg')); ?>" alt="" aria-hidden="true">
                                    <?php echo esc_html($label); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="notice_type" value="all">
                    </fieldset>

                    <div class="m-filter__group">
                        <label class="a-text a-text--xs h-semibold" for="<?php echo esc_attr($id . '-project'); ?>">Project</label>
                        <div class="m-filter__select">
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/strike.svg')); ?>" alt="" aria-hidden="true">
                            <select class="a-text a-text--l" name="project" id="<?php echo esc_attr($id . '-project'); ?>">
                                <option value="">All projects</option>
                                <?php foreach ($projects as $project) : ?>
                                    <option value="<?php echo esc_attr($project->ID); ?>">
                                        <?php echo esc_html(get_the_title($project)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="m-filter__group">
                        <label class="a-text a-text--xs h-semibold" for="<?php echo esc_attr($id . '-sort'); ?>">Sort by</label>
                        <div class="m-filter__select">
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/sort.svg')); ?>" alt="" aria-hidden="true">
                            <select class="a-text a-text--s" name="sort" id="<?php echo esc_attr($id . '-sort'); ?>">
                                <option value="newest">Newest first</option>
                                <option value="oldest">Oldest first</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="m-filter__footer">
                <span class="a-text a-text--xs h-semibold"><?php echo esc_html(get_field('filter_links_label')); ?></span>

                <div class="m-filter__links">
                    <?php foreach ($links as $row) : ?>
                        <?php
                        $link = $row['link'] ?? [];

                        if (empty($link['url'])) {
                            continue;
                        }

                        $icon_files = [
                            'icon-pdf'     => 'blue-pdf.svg',
                            'icon-message' => 'secure.svg',
                            'icon-search'  => 'cash.svg',
                        ];
                        $icon_file = $icon_files[$row['icon'] ?? ''] ?? '';
                        $target = $link['target'] ?? '';
                        ?>
                        <a
                            class="a-text a-text--l"
                            href="<?php echo esc_url($link['url']); ?>"
                            target="<?php echo esc_attr($target); ?>"
                            <?php if ($target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                        >
                            <?php if ($icon_file) : ?>
                                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/' . $icon_file)); ?>" alt="" aria-hidden="true">
                            <?php endif; ?>
                            <?php echo esc_html($link['title'] ?? ''); ?>
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/icon-up.svg')); ?>" alt="" aria-hidden="true">
                        </a>
                    <?php endforeach; ?>
                </div>

                <button class="a-text a-text--l" type="reset">
                    <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/close.svg')); ?>" alt="" aria-hidden="true">
                    Reset filters
                </button>
            </div>
        </form>
    </div>
</section>