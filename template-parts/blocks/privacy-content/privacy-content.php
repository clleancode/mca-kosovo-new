<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-privacy-content',
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

$sections = array_values(array_filter(
    get_field('privacy_sections') ?: [],
    static function ($section) {
        return !empty($section['title']);
    }
));

$instance = wp_unique_id('privacy-');
$email    = sanitize_email(get_field('privacy_contact_email'));
$link     = get_field('privacy_settings_link');
?>
<section
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
>
    <div class="container">
        <div class="m-privacy-content" data-privacy-content>
            <nav class="m-privacy-content__contents" aria-label="<?php esc_attr_e('Privacy policy contents', 'mca'); ?>">
                <h2 class="a-text a-text--m h-semibold h-uppercase e-reveal e-reveal--text">
                    <?php echo esc_html(get_field('privacy_contents_title') ?: __('Contents', 'mca')); ?>
                </h2>
                <?php foreach ($sections as $index => $section) : ?>
                    <a
                        class="a-text a-text--l e-reveal"
                        data-delay="<?php echo esc_attr(150 + min($index, 7) * 90); ?>"
                        href="#<?php echo esc_attr($instance . '-' . ($index + 1)); ?>"
                        <?php if ($index === 0) : ?>aria-current="location"<?php endif; ?>
                    >
                        <span class="a-text a-text--xs"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                        <?php echo esc_html($section['title']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="m-privacy-content__main">
                <?php foreach ($sections as $index => $section) : ?>
                    <div
                        class="m-privacy-content__section"
                        id="<?php echo esc_attr($instance . '-' . ($index + 1)); ?>"
                        data-privacy-section
                    >
                        <h2 class="a-heading a-heading--h5 h-dark-blue e-reveal e-reveal--text" data-delay="<?php echo esc_attr($index ? 0 : 200); ?>">
                            <?php echo esc_html(sprintf('%02d', $index + 1) . ' ' . $section['title']); ?>
                        </h2>
                        <div class="m-privacy-content__body<?php echo !empty($section['dashed_content']) ? ' is-dashed' : ''; ?> e-reveal" data-delay="<?php echo esc_attr($index ? 150 : 350); ?>">
                            <?php echo wp_kses_post($section['content'] ?? ''); ?>
                        </div>
                        <?php if (!empty($section['details'])) : ?>
                            <div class="m-privacy-content__details e-reveal" data-delay="250">
                                <dl>
                                    <?php foreach ($section['details'] as $detail) : ?>
                                        <div>
                                            <dt class="a-text a-text--m"><?php echo esc_html($detail['label'] ?? ''); ?></dt>
                                            <dd class="a-text a-text--m">
                                                <?php if (!empty($detail['link']['url'])) : ?>
                                                    <a
                                                        href="<?php echo esc_url($detail['link']['url']); ?>"
                                                        <?php if (($detail['link']['target'] ?? '') === '_blank') : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                                                    ><?php echo esc_html($detail['value'] ?: ($detail['link']['title'] ?? '')); ?></a>
                                                <?php else : ?>
                                                    <?php echo esc_html($detail['value'] ?? ''); ?>
                                                <?php endif; ?>
                                            </dd>
                                        </div>
                                    <?php endforeach; ?>
                                </dl>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($section['additional_content'])) : ?>
                            <div class="m-privacy-content__body e-reveal" data-delay="250">
                                <?php echo wp_kses_post($section['additional_content']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($section['table_rows'])) : ?>
                            <div
                                class="m-privacy-content__table e-reveal"
                                data-delay="250"
                                role="region"
                                aria-label="<?php echo esc_attr($section['title']); ?>"
                                tabindex="0"
                            >
                                <table>
                                    <thead>
                                        <tr>
                                            <?php foreach (['column_one', 'column_two', 'column_three'] as $column) : ?>
                                                <th scope="col"><?php echo esc_html($section[$column] ?? ''); ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($section['table_rows'] as $row) : ?>
                                            <tr>
                                                <?php foreach (['one', 'two', 'three'] as $column) : ?>
                                                    <td><?php echo esc_html($row[$column] ?? ''); ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php if ($email || !empty($link['url'])) : ?>
                    <div class="m-privacy-content__contact e-reveal">
                        <div>
                            <h2 class="a-heading a-heading--h6 h-semibold">
                                <?php echo esc_html(get_field('privacy_contact_title') ?: __('Questions about your data?', 'mca')); ?>
                            </h2>
                            <?php if ($email) : ?>
                                <p class="a-text a-text--m">
                                    Contact <a href="<?php echo esc_url('mailto:' . $email); ?>"><?php echo esc_html($email); ?></a>
                                </p>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($link['url'])) : ?>
                            <?php if ($link['url'] === '#') : ?>
                            <button type="button" class="a-text a-text--m h-semibold h-white" data-cookie-action="preferences"><?php echo esc_html($link['title'] ?: __('Cookie settings', 'mca')); ?></button>
                            <?php else : ?>
                            <a
                                class="a-text a-text--m h-semibold"
                                href="<?php echo esc_url($link['url']); ?>"
                                <?php if (($link['target'] ?? '') === '_blank') : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                            >
                                <?php echo esc_html($link['title'] ?: __('Cookie settings', 'mca')); ?>
                            </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>