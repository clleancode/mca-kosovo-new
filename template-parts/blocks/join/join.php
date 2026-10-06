<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-join',
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

$tag = $setting('header_tag', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'], 'h2');
$size = $setting('header_size', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], 'h4');
$color = $setting('header_color', ['h-white', 'h-dark-blue', 'h-blue', 'h-purple', 'h-dark-green'], 'h-white');
$height = $setting('header_line_height', ['3xs', '2xs', 'xs', 's', 'm', 'ls', 'l', 'xl'], 's');

$image = get_field('join_image');

$links = [
    'contact'   => get_field('join_contact_link') ?: [],
    'subscribe' => get_field('join_subscribe_link') ?: [],
];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-join">
            <?php if ($image) : ?>
                <div class="m-join__image">
                    <?php echo wp_get_attachment_image($image, 'full', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                </div>
            <?php endif; ?>

            <div class="m-join__content s-d-t-m s-d-b-m s-m-t-xs s-m-b-xs">
                <div class="m-join__label s-d-b-s s-m-b-s">
                    <p class="a-text a-text--s h-blue"><?php echo esc_html(get_field('join_label')); ?></p>
                </div>

                <<?php echo tag_escape($tag); ?> class="h-medium a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                    <?php echo nl2br(esc_html(get_field('join_title'))); ?>
                </<?php echo tag_escape($tag); ?>>

                <div class="m-join__buttons s-d-t-s s-m-t-s">
                    <?php foreach ($links as $type => $link) : ?>
                        <?php
                        if (empty($link['url'])) {
                            continue;
                        }

                        $target = $link['target'] ?? '';
                        ?>
                        <a
                            class="a-join__button a-join__button--<?php echo esc_attr($type); ?> a-text a-text--m"
                            href="<?php echo esc_url($link['url']); ?>"
                            target="<?php echo esc_attr($target); ?>"
                            <?php if ($target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                        >
                            <?php echo esc_html($link['title'] ?? ''); ?>
                            <?php if ($type === 'contact') : ?>
                                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/small-right.svg')); ?>" alt="" aria-hidden="true">
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>