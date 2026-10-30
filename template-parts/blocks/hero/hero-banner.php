<?php
/**
 * Hero Banner Block Template
 */

use MCA\Helpers;

$device_visibility       = Helpers\mca_get_device_visibility();
$show                    = $device_visibility['show'];
$visibility_class_string = $device_visibility['visibility_classes'];

if (!$show) {
    return;
}

$spacing_fields = array_filter([
    get_field('space_mobile_top') ? 's-m-t-' . esc_attr(get_field('space_mobile_top')) : '',
    get_field('space_mobile_bottom') ? 's-m-b-' . esc_attr(get_field('space_mobile_bottom')) : '',
    get_field('space_desktop_top') ? 's-d-t-' . esc_attr(get_field('space_desktop_top')) : '',
    get_field('space_desktop_bottom') ? 's-d-b-' . esc_attr(get_field('space_desktop_bottom')) : '',
]);

$spacing_classes = implode(' ', $spacing_fields);

$header_tag         = get_field('header_tag') ?: 'h1';
$header_type        = get_field('header_size') ? 'a-heading--' . get_field('header_size') : '';
$header_color       = get_field('header_color') ?: '';
$header_line_height = get_field('header_line_height') ? 'h-text-l h--' . get_field('header_line_height') : '';
$header_colorClass  = $header_color ? esc_attr($header_color) : '';

$textType            = get_field('text_type');
$textColor           = get_field('text_color');
$textLineHeight      = get_field('text_line_height');
$textTypeClass       = $textType ? 'a-text--' . esc_attr($textType) : '';
$textColorClass      = $textColor ? esc_attr($textColor) : '';
$textLineHeightClass = $textLineHeight ? 'h-text-l h--' . esc_attr($textLineHeight) : '';

$slides = get_field('hero_slides');

if (get_field('hero_layout') === 'about') {
    $slide      = $slides[0] ?? [];
    $statistics = array_slice(get_field('hero_about_statistics') ?: [], 0, 4);

    [
        'header_tag'         => $about_tag,
        'header_size'        => $about_size,
        'header_color'       => $about_color,
        'header_line_height' => $about_height,
        'text_type'          => $about_text,
        'text_color'         => $about_text_color,
        'text_line_height'   => $about_text_height,
    ] = Helpers\get_block_typography([
        'header_tag' => 'h1',
        'text_type'  => 'l',
    ]);
    ?>
    <section
        <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
        class="o-hero <?php echo esc_attr($spacing_classes . ' ' . $visibility_class_string); ?>"
    >
        <div class="m-hero-about">
            <?php if (!empty($slide['background_image'])) : ?>
                <div class="m-hero-about__image" data-parallax>
                    <?php
                    echo wp_get_attachment_image($slide['background_image'], 'full', false, [
                        'loading'       => 'eager',
                        'fetchpriority' => 'high',
                        'decoding'      => 'async',
                    ]);
                    ?>
                </div>
            <?php endif; ?>

            <div class="container">
                <div class="m-hero-about__layout">
                    <div class="m-hero-about__copy">
                        <nav class="h-breadcrumbs s-d-b-xs s-m-b-xs e-reveal" data-offset="0" aria-label="Breadcrumb">
                            <ol>
                                <li>
                                    <a class="a-text a-text--xs h-white" href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                                </li>
                                <li>
                                    <span class="a-text a-text--xs h-white" aria-current="page">
                                        <?php echo esc_html(get_the_title()); ?>
                                    </span>
                                </li>
                            </ol>
                        </nav>

                        <<?php echo tag_escape($about_tag); ?> class="a-heading a-heading--<?php echo esc_attr($about_size); ?> <?php echo esc_attr($about_color); ?> h--<?php echo esc_attr($about_height); ?> h-uppercase s-d-b-xs s-m-b-xs e-reveal e-reveal--text" data-offset="0" data-delay="120">
                            <?php echo wp_kses_post($slide['title'] ?? ''); ?>
                        </<?php echo tag_escape($about_tag); ?>>

                        <?php if (!empty($slide['description'])) : ?>
                            <p class="a-text a-text--<?php echo esc_attr($about_text); ?> <?php echo esc_attr($about_text_color); ?> h--<?php echo esc_attr($about_text_height); ?> e-reveal e-reveal--text" data-offset="0" data-delay="380">
                                <?php echo wp_kses_post($slide['description']); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if ($statistics) : ?>
                        <div class="m-hero-about__statistics">
                            <?php foreach ($statistics as $stat_index => $statistic) : ?>
                                <div class="m-hero-about__statistic e-reveal" data-offset="0" data-delay="<?php echo esc_attr(500 + $stat_index * 100); ?>">
                                    <span class="a-heading a-heading--h3 h-white"><?php echo esc_html($statistic['value'] ?? ''); ?></span>
                                    <span class="a-text a-text--xs h-white"><?php echo esc_html($statistic['caption'] ?? ''); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php
    return;
}

$ids = array_filter(array_map('absint', (array) get_field('hero_projects')));

$args = [
    'post_type'      => ['post', 'project'],
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'orderby'        => ['date' => 'DESC', 'ID' => 'DESC'],
    'no_found_rows'  => true,
];

if ($ids) {
    $args['post__in']       = $ids;
    $args['orderby']        = 'post__in';
    $args['posts_per_page'] = count($ids);
}

$items = get_posts($args);

if (!$items) {
    return;
}

$find_hero = static function (array $blocks) use (&$find_hero) {
    foreach ($blocks as $item) {
        if (($item['blockName'] ?? '') === 'acf/projects-hero') {
            return $item['attrs']['data'] ?? [];
        }

        $nested = $find_hero($item['innerBlocks'] ?? []);

        if ($nested) {
            return $nested;
        }
    }

    return [];
};

$summaries = [];
$slide_labels = [];

foreach ($items as $project) {
    $slide_labels[$project->ID] = get_the_title($project);
    if ($project->post_type === 'post') {
        $slide_labels[$project->ID] = '';
        foreach (get_the_category($project->ID) as $category) {
            if (strtolower($category->slug) === 'news' || strtolower(trim($category->name)) === 'news') continue;
            $slide_labels[$project->ID] = $category->name;
            break;
        }
    }
    $hero = $project->post_type === 'project'
        ? $find_hero(parse_blocks($project->post_content))
        : [];

    $summaries[$project->ID] = wp_strip_all_tags(
        ($hero['small_hero_description'] ?? '') ?: get_the_excerpt($project)
    );
}

$type = Helpers\get_block_typography([
    'header_tag'  => 'h1',
    'header_size' => 'h4',
    'text_type'   => 'l',
]);
?>
<section
    class="o-hero <?php echo esc_attr($spacing_classes . ' ' . $visibility_class_string); ?>"
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
>
    <div class="swiper swiper--hero m-hero" data-hero-slider aria-label="Featured news and projects">
        <div class="swiper-wrapper">
            <?php foreach ($items as $index => $project) : ?>
                <?php $tag = $index === 0 ? $type['header_tag'] : 'h2'; ?>
                <div class="swiper-slide m-hero__inner">
                    <div class="m-hero__image" data-parallax>
                        <?php
                        echo get_the_post_thumbnail($project, 'full', [
                            'loading'       => $index === 0 ? 'eager' : 'lazy',
                            'fetchpriority' => $index === 0 ? 'high' : 'auto',
                        ]);
                        ?>
                    </div>

                    <div class="container">
                        <div class="m-hero__copy">
                            <div class="m-hero__meta<?php echo $index > 0 ? ' e-reveal' : ''; ?>" data-offset="0" data-delay="100">
                                <?php if ($slide_labels[$project->ID]): ?><span class="a-text a-text--xs h-uppercase<?php echo $index === 0 ? ' e-reveal' : ''; ?>" data-offset="0" data-delay="100"><?php echo esc_html($slide_labels[$project->ID]); ?></span><?php endif; ?>
                                <time class="a-text a-text--xs h-uppercase<?php echo $index === 0 ? ' e-reveal e-reveal--text' : ''; ?>" data-offset="0" data-delay="180"><?php echo esc_html(get_the_date('M Y', $project)); ?></time>
                            </div>

                            <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($type['header_size']); ?> h-white h--<?php echo esc_attr($type['header_line_height']); ?> e-reveal e-reveal--text" data-offset="0" data-delay="200">
                                <?php echo esc_html(get_the_title($project)); ?>
                            </<?php echo tag_escape($tag); ?>>

                            <?php if ($excerpt = $summaries[$project->ID]) : ?>
                                <p class="a-text a-text--<?php echo esc_attr($type['text_type']); ?> h-white e-reveal e-reveal--text" data-offset="0" data-delay="420">
                                    <?php echo esc_html(wp_trim_words($excerpt, 28)); ?>
                                </p>
                            <?php endif; ?>

                            <div class="m-hero__action e-reveal" data-offset="0" data-delay="560">
                                <a class="a-text a-text--m h-semibold" href="<?php echo esc_url(get_permalink($project)); ?>">
                                    <span>
                                        <?php echo esc_html($project->post_type === 'project' ? __('Explore project', 'mca') : __('Read the story', 'mca')); ?>
                                    </span>
                                    <span aria-hidden="true"><i class="icon-arrow-right-up a-icon"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="m-hero__footer">
            <div class="container">
                <div class="m-hero__controls">
                    <div class="m-hero__fraction e-reveal" data-offset="0" data-delay="650">
                        <span class="a-text a-text--s" data-hero-current>01</span>
                        <span class="a-text a-text--s">/</span>
                        <span class="a-text a-text--s"><?php echo esc_html(sprintf('%02d', count($items))); ?></span>
                    </div>

                    <a class="e-reveal" data-offset="0" data-delay="730" href="#" role="button" data-hero-pause aria-label="Pause slideshow" aria-pressed="false">
                        <span data-hero-pause-icon aria-hidden="true">
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/stop.svg')); ?>" alt="">
                        </span>
                    </a>

                    <a class="e-reveal" data-offset="0" data-delay="810" href="#" role="button" data-hero-prev aria-label="Previous slide">
                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/left-hero.svg')); ?>" alt="">
                    </a>

                    <a class="e-reveal" data-offset="0" data-delay="890" href="#" role="button" data-hero-next aria-label="Next slide">
                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/right-hero.svg')); ?>" alt="">
                    </a>
                </div>

                <div class="swiper m-hero__cards" data-hero-cards>
                    <div class="swiper-wrapper">
                        <?php foreach ($items as $index => $project) : ?>
                            <div class="swiper-slide">
                                <div class="m-hero__card<?php echo $index === 0 ? ' is-active' : ''; ?> e-reveal" data-offset="0" data-delay="<?php echo esc_attr(720 + $index * 100); ?>">
                                    <a
                                        href="<?php echo esc_url(get_permalink($project)); ?>"
                                        data-hero-slide="<?php echo esc_attr($index); ?>"
                                        <?php if ($index === 0) : ?>aria-current="true"<?php endif; ?>
                                        aria-label="<?php echo esc_attr(get_the_title($project)); ?>"
                                    >
                                        <div class="m-hero__thumbnail">
                                            <?php echo get_the_post_thumbnail($project, 'thumbnail', ['loading' => 'lazy', 'alt' => '']); ?>
                                        </div>

                                        <div class="m-hero__card-copy h-semibold">
                                            <p class="a-text a-text--xs h-uppercase">
                                                <span class="a-text a-text--s h-semibold"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                                                <?php echo esc_html($slide_labels[$project->ID]); ?>
                                            </p>
                                            <p class="a-text a-text--xl h-semibold">
                                                <?php echo esc_html(wp_html_excerpt($summaries[$project->ID] ?: get_the_title($project), 110, '…')); ?>
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
