</main>
<?php
$footer_logo = get_field('footer_logo', 'option');
$description = get_field('footer_description', 'option');
$columns = [
    'footer_projects' => 'Projects',
    'footer_work_with_us' => 'Work with us',
    'footer_organization' => 'Organization',
];
$contact_title = get_field('contact_us_title', 'option') ?: 'Contact';
$address = get_field('contact_address', 'option');
$address_link = get_field('footer_address_link', 'option') ?: [];
$email = get_field('contact_email', 'option');
$report = get_field('footer_button', 'option') ?: [];
if (empty($report['url']) || trim($report['url']) === '#') {
    $report['url'] = home_url('/report-fraud-corruption/');
}
$socials = get_field('social_media_links', 'option') ?: [];
$disclaimer = get_field('footer_disclaimer', 'option');
$copyright = get_field('footer_copyright', 'option');
$legal_menu = 'footer_navigation';
$menu_link_classes = static function ($attributes, $item, $args) {
    $size = $args->theme_location === 'footer_navigation' ? 'xs' : 'l';
    $attributes['class'] = 'a-footer-link a-text a-text--' . $size;
    if (in_array('current-menu-item', $item->classes, true)) {
        $attributes['class'] .= ' h-semibold';
    }
    return $attributes;
};
$render_menu = static function ($menu, $reveal_delay = null) use ($menu_link_classes) {
    if (!has_nav_menu($menu)) return;
    $reveal = $reveal_delay !== null;
    $item_index = 0;
    $item_reveal_class = static function ($classes) {
        $classes[] = 'e-reveal';
        return $classes;
    };
    $item_reveal_delay = static function ($attributes) use ($reveal_delay, &$item_index) {
        $attributes['data-delay'] = $reveal_delay + $item_index++ * 80;
        return $attributes;
    };
    add_filter('nav_menu_link_attributes', $menu_link_classes, 10, 3);
    if ($reveal) {
        add_filter('nav_menu_css_class', $item_reveal_class);
        add_filter('nav_menu_item_attributes', $item_reveal_delay);
    }
    wp_nav_menu([
        'theme_location' => $menu,
        'container'      => false,
        'menu_class'     => '',
        'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        'fallback_cb'    => false,
        'depth'          => 1,
    ]);
    remove_filter('nav_menu_link_attributes', $menu_link_classes);
    if ($reveal) {
        remove_filter('nav_menu_css_class', $item_reveal_class);
        remove_filter('nav_menu_item_attributes', $item_reveal_delay);
    }
};
?>
<footer class="o-footer">
    <div class="container">
        <div class="m-footer__main s-d-b-l s-m-b-s">
            <div class="m-footer__brand">
                <?php if ($footer_logo): ?>
                    <a class="a-footer-logo e-reveal" href="<?php echo esc_url(home_url('/')); ?>" aria-label="MCA Kosovo home">
                        <?php if (is_array($footer_logo) && !empty($footer_logo['url'])): ?>
                            <img src="<?php echo esc_url($footer_logo['url']); ?>" alt="<?php echo esc_attr(($footer_logo['alt'] ?? '') ?: 'MCA Kosovo'); ?>" loading="lazy" decoding="async">
                        <?php else: echo wp_get_attachment_image(is_array($footer_logo) ? ($footer_logo['ID'] ?? 0) : $footer_logo, 'medium', false, ['loading' => 'lazy', 'decoding' => 'async']); endif; ?>
                    </a>
                <?php endif; ?>
                <?php if ($description): ?><div class="m-footer__description s-d-t-s s-d-b-s s-m-t-xs s-m-b-xs"><p class="a-text a-text--l e-reveal e-reveal--text" data-delay="100"><?php echo nl2br(esc_html($description)); ?></p></div><?php endif; ?>
                <?php if ($socials): ?>
                    <nav class="m-footer__socials" aria-label="Social media">
                        <ul>
                            <?php $social_index = 0; ?>
                            <?php foreach ($socials as $social): if (empty($social['url'])) continue; ?>
                                <li class="e-reveal" data-delay="<?php echo esc_attr(300 + $social_index++ * 100); ?>"><a class="a-footer-social" href="<?php echo esc_url($social['url']); ?>" aria-label="<?php echo esc_attr(($social['icon_text'] ?? '') ?: ucwords(str_replace(['icon-', '-'], ['', ' '], $social['icon'] ?? 'Social media'))); ?>">
                                    <?php
                                    $icon_name = str_replace('icon-', '', $social['icon'] ?? '');
                                    $social_icon_files = [
                                        'facebook' => 'facebook.svg',
                                        'linkedin' => 'linkedin.svg',
                                        'instagram' => 'instagram.svg',
                                        'youtube' => 'youtube.svg',
                                    ];
                                    ?>
                                    <?php if (isset($social_icon_files[$icon_name])): ?>
                                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/' . $social_icon_files[$icon_name])); ?>" alt="" aria-hidden="true">
                                    <?php elseif (!empty($social['icon'])): ?>
                                        <i class="a-icon <?php echo esc_attr($social['icon']); ?>" aria-hidden="true"></i>
                                    <?php else: ?>
                                        <span class="a-text a-text--xs"><?php echo esc_html($social['icon_text'] ?? 'Social'); ?></span>
                                    <?php endif; ?>
                                </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
            <div class="m-footer__menus">
                <?php foreach (array_keys($columns) as $column_index => $location): ?>
                    <?php $title = $columns[$location]; ?>
                    <nav class="m-footer__column" aria-label="<?php echo esc_attr($title); ?>">
                        <h2 class="a-footer-label s-d-b-s s-m-b-xs a-text a-text--xs e-reveal e-reveal--text" data-delay="<?php echo esc_attr(100 + $column_index * 120); ?>"><?php echo esc_html($title); ?></h2>
                        <?php $render_menu($location, 200 + $column_index * 120); ?>
                    </nav>
                <?php endforeach; ?>
            </div>
            <div class="m-footer__contact">
                <h2 class="a-footer-label s-d-b-s s-m-b-xs a-text a-text--xs e-reveal e-reveal--text" data-delay="460"><?php echo esc_html($contact_title); ?></h2>
                <address>
                    <?php $contact_delay = 560; ?>
                    <?php if ($address): ?>
                        <div class="m-footer__contact-row e-reveal" data-delay="<?php echo esc_attr($contact_delay); $contact_delay += 100; ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M19 10c0 5-7 12-7 12S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            <?php if (!empty($address_link['url'])): ?><a class="a-footer-link a-text a-text--l" href="<?php echo esc_url($address_link['url']); ?>"><?php echo nl2br(esc_html($address)); ?></a><?php else: ?><p class="a-text a-text--s"><?php echo nl2br(esc_html($address)); ?></p><?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($email): ?>
                        <div class="m-footer__contact-row e-reveal" data-delay="<?php echo esc_attr($contact_delay); $contact_delay += 100; ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="1"/><path d="m3 6 9 7 9-7"/></svg>
                            <a class="a-footer-link a-text a-text--l" href="<?php echo esc_url('mailto:' . sanitize_email($email)); ?>"><?php echo esc_html($email); ?></a>
                        </div>
                    <?php endif; ?>
                </address>
                <?php if (!empty($report['url'])): ?><a class="a-footer-report a-text a-text--xs e-reveal" data-delay="<?php echo esc_attr($contact_delay); ?>" href="<?php echo esc_url($report['url']); ?>" target="<?php echo esc_attr(($report['target'] ?? '') ?: '_self'); ?>"<?php if (($report['target'] ?? '') === '_blank'): ?> rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html(($report['title'] ?? '') ?: 'Report fraud & corruption'); ?> <span aria-hidden="true">&rarr;</span></a><?php endif; ?>
            </div>
        </div>
        <div class="m-footer__bottom s-d-t-s s-m-t-xs">
            <span class="m-footer__divider e-reveal e-reveal--clip" aria-hidden="true"></span>
            <?php if ($disclaimer): ?><div class="m-footer__disclaimer"><p class="a-text a-text--m e-reveal e-reveal--text" data-delay="150"><?php echo nl2br(esc_html(wp_strip_all_tags($disclaimer))); ?></p></div><?php endif; ?>
            <div class="m-footer__legal-row">
                <div class="m-footer__copyright"><p class="a-text a-text--xs h-semibold e-reveal e-reveal--text" data-delay="300"><?php echo $copyright ? esc_html(wp_strip_all_tags($copyright)) : esc_html(sprintf('© %s MCA Kosovo. All rights reserved.', wp_date('Y'))); ?></p></div>
                <nav class="m-footer__legal e-reveal" data-delay="450" aria-label="Legal and privacy">
                    <?php $render_menu($legal_menu); ?>
                    <button type="button" class="a-footer-link a-text a-text--xs" data-cookie-settings><?php echo esc_html(get_field('footer_cookie_settings_label', 'option') ?: 'Cookie settings'); ?></button>
                </nav>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
