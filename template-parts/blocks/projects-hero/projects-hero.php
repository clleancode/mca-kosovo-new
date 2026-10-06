<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-projects-hero',
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

$tag = $setting('header_tag', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'], 'h1');
$size = $setting('header_size', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], 'h4');
$color = $setting('header_color', $colors, 'h-white');
$height = $setting('header_line_height', ['3xs', '2xs', 'xs', 's', 'm', 'ls', 'l', 'xl'], 's');
$text_size = $setting('text_type', ['xs', 's', 'm', 'l', 'xl', 'xxl'], 'l');
$text_color = $setting('text_color', $colors, 'h-white');
$text_height = $setting('text_line_height', ['3xs', '2xs', 'xs', 's', 'm', 'l', 'xl'], 'l');
$tone = $setting('projects_hero_tone', ['blue', 'purple', 'green'], 'blue');

$image = get_field('small_hero_image');
$title = get_field('small_hero_title');
$description = get_field('small_hero_description');
$label = get_field('small_hero_label');
$breadcrumbs = apply_filters('mca_projects_hero_breadcrumbs', [], $post_id ?? get_the_ID());
$statistics = array_slice(get_field('small_hero_statistics') ?: [], 0, 4);

$links = [
    'primary'   => get_field('small_hero_primary_link'),
    'secondary' => get_field('small_hero_secondary_link'),
];
?>
<section
    data-project-tone="<?php echo esc_attr($tone); ?>"
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <?php if ($image) : ?>
        <div class="m-projects-hero__image">
            <?php echo wp_get_attachment_image($image, 'full', false, ['loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async']); ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="m-projects-hero__content">
            <nav class="m-projects-hero__breadcrumbs s-d-b-s s-m-b-xs" aria-label="Breadcrumb">
                <ol>
                    <?php foreach ($breadcrumbs as $breadcrumb) : ?>
                        <li>
                            <?php if (!empty($breadcrumb['url'])) : ?>
                                <a class="a-text a-text--xs" href="<?php echo esc_url($breadcrumb['url']); ?>"><?php echo esc_html($breadcrumb['title']); ?></a>
                            <?php else : ?>
                                <span class="a-text a-text--xs" aria-current="page"><?php echo esc_html($breadcrumb['title']); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>

            <?php if ($label) : ?>
                <div class="m-projects-hero__label">
                    <span class="a-text a-text--xs h-semibold"><?php echo esc_html($label); ?></span>
                </div>
            <?php endif; ?>

            <<?php echo tag_escape($tag); ?> class="s-d-b-s s-d-t-s s-m-b-xs s-m-t-xs a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                <?php echo nl2br(esc_html($title)); ?>
            </<?php echo tag_escape($tag); ?>>

            <?php if ($description) : ?>
                <div class="m-projects-hero__description s-d-b-s s-m-b-xs">
                    <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                        <?php echo nl2br(esc_html($description)); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="m-projects-hero__buttons s-d-b-2xl s-m-b-m">
                <?php foreach ($links as $type => $link) : ?>
                    <?php
                    if (empty($link['url'])) {
                        continue;
                    }

                    $target = $link['target'] ?? '';
                    ?>
                    <a
                        class="h-semibold a-projects-hero-button a-projects-hero-button--<?php echo esc_attr($type); ?> a-text a-text--m"
                        href="<?php echo esc_url($link['url']); ?>"
                        target="<?php echo esc_attr($target); ?>"
                        <?php if ($target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                    >
                        <?php echo esc_html($link['title'] ?? ''); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($statistics) : ?>
            <div class="m-projects-hero__statistics s-d-t-s s-m-t-xs">
                <?php foreach ($statistics as $statistic) : ?>
                    <div class="m-projects-hero__statistic">
                        <div class="m-projects-hero__value">
                            <span class="a-heading a-heading--h3 h-white"><?php echo esc_html($statistic['value'] ?? ''); ?></span>
                            <?php if (!empty($statistic['unit'])) : ?>
                                <span class="a-text a-heading--h6"><?php echo esc_html($statistic['unit']); ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="a-text a-text--xs"><?php echo esc_html($statistic['label'] ?? ''); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
