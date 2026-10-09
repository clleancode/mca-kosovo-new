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
                <div class="m-hero-about__image">
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
                        <nav class="h-breadcrumbs s-d-b-xs s-m-b-xs" aria-label="Breadcrumb">
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

                        <<?php echo tag_escape($about_tag); ?> class="a-heading a-heading--<?php echo esc_attr($about_size); ?> <?php echo esc_attr($about_color); ?> h--<?php echo esc_attr($about_height); ?> h-uppercase s-d-b-xs s-m-b-xs">
                            <?php echo wp_kses_post($slide['title'] ?? ''); ?>
                        </<?php echo tag_escape($about_tag); ?>>

                        <?php if (!empty($slide['description'])) : ?>
                            <p class="a-text a-text--<?php echo esc_attr($about_text); ?> <?php echo esc_attr($about_text_color); ?> h--<?php echo esc_attr($about_text_height); ?>">
                                <?php echo wp_kses_post($slide['description']); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if ($statistics) : ?>
                        <div class="m-hero-about__statistics">
                            <?php foreach ($statistics as $statistic) : ?>
                                <div class="m-hero-about__statistic">
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
?>

<section
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
    class="o-hero <?php echo esc_attr($spacing_classes); ?> <?php echo esc_attr($visibility_class_string); ?>"
>
    <div class="swiper swiper--hero">
        <div class="swiper-wrapper">
            <?php if ($slides && have_rows('hero_slides')) : ?>
                <?php $slide_index = 0; ?>
                <?php while (have_rows('hero_slides')) : the_row(); ?>
                    <?php
                    $background_image   = get_sub_field('background_image');
                    $title              = get_sub_field('title');
                    $description        = get_sub_field('description');
                    $button_link        = get_sub_field('button_link');
                    $current_header_tag = ($header_tag === 'h1' && $slide_index > 0) ? 'h2' : $header_tag;
                    ?>
                    <div class="swiper-slide m-hero__inner">
                        <?php if ($background_image) : ?>
                            <?php
                            echo wp_get_attachment_image($background_image, 'full', false, [
                                'class'    => 'a-img',
                                'loading'  => $slide_index === 0 ? 'eager' : 'lazy',
                                'decoding' => 'async',
                            ]);
                            ?>
                        <?php endif; ?>

                        <div class="container h-align-start">
                            <div class="m-content h-white">
                                <?php if ($title) : ?>
                                    <<?php echo tag_escape($current_header_tag); ?>
                                        class="a-heading <?php echo esc_attr(trim("$header_type $header_colorClass $header_line_height")); ?> h-uppercase"
                                    >
                                        <?php echo wp_kses_post($title); ?>
                                    </<?php echo tag_escape($current_header_tag); ?>>
                                <?php endif; ?>

                                <?php if ($description) : ?>
                                    <p class="a-text <?php echo esc_attr(trim("$textTypeClass $textColorClass $textLineHeightClass")); ?>">
                                        <?php echo wp_kses_post($description); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($button_link) && is_array($button_link)) : ?>
                                    <?php
                                    $button_url    = $button_link['url'] ?? '';
                                    $button_title  = $button_link['title'] ?? '';
                                    $button_target = $button_link['target'] ?? '_self';
                                    ?>
                                    <?php if ($button_url) : ?>
                                        <a
                                            href="<?php echo esc_url($button_url); ?>"
                                            class="a-btn a-btn--white h-semibold"
                                            target="<?php echo esc_attr($button_target); ?>"
                                            <?php if ($button_target === '_blank') : ?>
                                                rel="noopener noreferrer"
                                            <?php endif; ?>
                                            aria-label="<?php echo esc_attr($button_title); ?>"
                                        >
                                            <span><?php echo esc_html($button_title); ?></span>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php $slide_index++; ?>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <?php
        $show_last_vacancy = get_field('show_last_vacancy');
        $selected_news     = get_field('selected_news');
        $card_post         = !empty($selected_news[0]) ? get_post($selected_news[0]) : null;

        if (!$card_post || $card_post->post_status !== 'publish') {
            $latest_posts = get_posts([
                'post_type'      => $show_last_vacancy ? 'job' : 'post',
                'posts_per_page' => 1,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]);

            $card_post = $latest_posts[0] ?? null;
        }
        ?>

        <?php if ($card_post) : ?>
            <article class="m-hero-card">
                <div class="m-hero-card__top">
                    <a
                        class="a-hero-card-image"
                        href="<?php echo esc_url(get_permalink($card_post)); ?>"
                        aria-label="<?php echo esc_attr(get_the_title($card_post)); ?>"
                    >
                        <?php
                        echo get_the_post_thumbnail($card_post, 'medium', [
                            'loading'  => 'lazy',
                            'decoding' => 'async',
                        ]);
                        ?>
                    </a>

                    <div class="m-hero-card__heading">
                        <p class="a-text a-text--s">
                            Discover <?php echo $show_last_vacancy ? 'vacancies' : 'news'; ?>
                        </p>
                        <h2 class="a-text a-text--xxl h-white">
                            <a class="a-hero-card-title" href="<?php echo esc_url(get_permalink($card_post)); ?>">
                                <?php echo esc_html(get_the_title($card_post)); ?>
                            </a>
                        </h2>
                    </div>
                </div>

                <div class="m-hero-card__excerpt s-d-t-xs s-d-b-s s-m-t-xs s-m-b-xs">
                    <p class="a-text a-text--m">
                        <?php echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_excerpt($card_post)), 30)); ?>
                    </p>
                </div>

                <a class="a-btn--border-white h-semibold" href="<?php echo esc_url(get_permalink($card_post)); ?>">
                    <span>Read more</span>
                </a>
            </article>
        <?php endif; ?>

        <div class="m-navigation container s-d-t-s">
            <button type="button" class="swiper-button-prev" aria-label="Previous slide">
                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/Chevron-left.svg')); ?>" alt="" aria-hidden="true">
            </button>

            <span class="m-hero__current a-text a-text--s">01</span>
            <div class="m-hero__pagination"></div>
            <span class="m-hero__total a-text a-text--s">
                <?php echo esc_html(sprintf('%02d', is_array($slides) ? count($slides) : 0)); ?>
            </span>

            <button type="button" class="swiper-button-next" aria-label="Next slide">
                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/Chevron-right.svg')); ?>" alt="" aria-hidden="true">
            </button>
        </div>
    </div>
</section>