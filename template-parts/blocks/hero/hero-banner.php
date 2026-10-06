<?php
/**
 * Hero Banner Block Template
 */

use MCA\Helpers;

$device_visibility = Helpers\rapture_get_device_visibility();
$show = $device_visibility['show'];
$visibility_class_string = $device_visibility['visibility_classes'];

if (!$show) return;

$spacing_fields = array_filter([
    get_field('space_mobile_top') ? 's-m-t-' . esc_attr(get_field('space_mobile_top')) : '',
    get_field('space_mobile_bottom') ? 's-m-b-' . esc_attr(get_field('space_mobile_bottom')) : '',
    get_field('space_desktop_top') ? 's-d-t-' . esc_attr(get_field('space_desktop_top')) : '',
    get_field('space_desktop_bottom') ? 's-d-b-' . esc_attr(get_field('space_desktop_bottom')) : '',
]);

$spacing_classes = implode(' ', $spacing_fields);

// Heading styling
$header_tag = get_field('header_tag') ?: 'h1';
$header_type = get_field('header_size') ? 'a-heading--' . get_field('header_size') : '';
$header_color = get_field('header_color') ?: '';
$header_line_height = get_field('header_line_height') ? 'h-text-l h--' . get_field('header_line_height') : '';
$header_colorClass = $header_color ? esc_attr($header_color) : '';

// Text styling
$textType = get_field('text_type');
$textColor = get_field('text_color');
$textLineHeight = get_field('text_line_height');
$textTypeClass = $textType ? 'a-text--' . esc_attr($textType) : '';
$textColorClass = $textColor ? esc_attr($textColor) : '';
$textLineHeightClass = $textLineHeight ? 'h-text-l h--' . esc_attr($textLineHeight) : '';

$slides = get_field('hero_slides');
?>

<section <?php if (!empty($block['anchor'])): ?>id="<?php echo esc_attr($block['anchor']); ?>" <?php endif; ?>class="o-hero <?php echo esc_attr($spacing_classes); ?> <?php echo esc_attr($visibility_class_string); ?>">
    <div class="swiper swiper--hero">
        <div class="swiper-wrapper">
            <?php if ($slides && have_rows('hero_slides')): ?>
                <?php $slide_index = 0; ?>
                <?php while (have_rows('hero_slides')): the_row();
                    $background_image = get_sub_field('background_image');
                    $title = get_sub_field('title');
                    $description = get_sub_field('description');
                    $button_link = get_sub_field('button_link');
                    $current_header_tag = ($header_tag === 'h1' && $slide_index > 0) ? 'h2' : $header_tag;
                ?>
                    <div class="swiper-slide m-hero__inner">
                        <?php if ($background_image): ?>
                            <?php echo wp_get_attachment_image($background_image, 'full', false, ['class' => 'a-img', 'loading' => 'lazy', 'decoding' => 'async']); ?>
                        <?php endif; ?>
                        <div class="container h-align-start">
                            <div class="m-content h-white">
                                <?php if ($title): ?>
                                    <<?php echo tag_escape($current_header_tag); ?>
                                        class="a-heading <?php echo esc_attr(trim("$header_type $header_colorClass $header_line_height")); ?> h-uppercase">
                                        <?php echo wp_kses_post($title); ?>
                                    </<?php echo tag_escape($current_header_tag); ?>>
                                <?php endif; ?>

                                <?php if ($description): ?>
                                    <p class="a-text <?php echo esc_attr(trim("$textTypeClass $textColorClass $textLineHeightClass")); ?>">
                                        <?php echo wp_kses_post($description); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($button_link) && is_array($button_link)):
                                    $button_url = $button_link['url'] ?? '';
                                    $button_title = $button_link['title'] ?? '';
                                    $button_target = $button_link['target'] ?? '_self';
                                    if ($button_url): ?>
                                        <a href="<?php echo esc_url($button_url); ?>" class="a-btn a-btn--smaller a-btn--link" target="<?php echo esc_attr($button_target); ?>" aria-label="<?php echo esc_attr($button_title); ?>">
                                            <span>
                                                <?php echo esc_html($button_title ?: 'Read More'); ?>
                                                <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                            </span>
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
        // HERO CARD SECTION
        $show_last_vacancy = get_field('show_last_vacancy');
        $selected_news = get_field('selected_news'); // Relationship field (returns array)

        if ($selected_news && is_array($selected_news)) {
            // Show manually selected post
            $post = $selected_news[0];
            setup_postdata($post);
        } else {
            // Otherwise show latest news or vacancy
            $post_type = $show_last_vacancy ? 'job' : 'post';
            $args = [
                'post_type'      => $post_type,
                'posts_per_page' => 1,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ];
            $latest_post = get_posts($args);
            if ($latest_post) {
                $post = $latest_post[0];
                setup_postdata($post);
            } else {
                $post = null;
            }
        }

        if ($post):
            $card_image = get_the_post_thumbnail_url($post->ID, 'full');
            $card_title = get_the_title($post->ID);
            $card_link  = get_permalink($post->ID);
            $card_excerpt = get_the_excerpt($post->ID);
            ?>
            <div class="m-hero-card">
                <div class="m-hero-card__left">
                    <?php if ($card_image): ?>
                        <img src="<?php echo esc_url($card_image); ?>" class="a-img" alt="<?php echo esc_attr($card_title); ?>">
                    <?php endif; ?>
                    <a href="<?php echo esc_url($card_link); ?>" class="icon-arrow-right-up a-icon a-icon--s h-white" aria-hidden="true"></a>
                </div>
                <div class="m-hero-card__right h-white">
                    <p class="a-text a-text--xs">
                        Discover <?php echo $show_last_vacancy ? 'vacancy' : 'news'; ?>
                    </p>
                    <div class="m-content">
                        <p class="a-text a-text--xxl h-britanica"><?php echo esc_html($card_title); ?></p>
                        <p class="a-text a-text--m s-d-b-s s-m-b-xs"><?php echo esc_html($card_excerpt); ?></p>
                    </div>
                </div>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>

        <div class="m-navigation container">
            <button type="button" class="swiper-button-prev icon-arrow-left-down a-icon a-icon--m h-white" aria-label="Go to previous slide"></button>
            <button type="button" class="swiper-button-next icon-arrow-right-up a-icon a-icon--m h-white" aria-label="Go to next slide"></button>
            <div class="m-hero__pagination"></div>
            <span class="m-hero__fraction"><span class="m-hero__current">01</span> / <span class="m-hero__total"><?php echo esc_html(sprintf('%02d', is_array($slides) ? count($slides) : 0)); ?></span></span>
        </div>
    </div>
</section>
