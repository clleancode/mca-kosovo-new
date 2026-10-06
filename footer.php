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
$render_menu = static function ($menu) use ($menu_link_classes) {
    if (!has_nav_menu($menu)) return;
    add_filter('nav_menu_link_attributes', $menu_link_classes, 10, 3);
    wp_nav_menu(['theme_location' => $menu, 'container' => false, 'menu_class' => '', 'fallback_cb' => false, 'depth' => 1]);
    remove_filter('nav_menu_link_attributes', $menu_link_classes);
};
?>
<footer class="o-footer">
    <div class="container">
        <div class="m-footer__main s-d-b-l s-m-b-s">
            <div class="m-footer__brand">
                <?php if ($footer_logo): ?>
                    <a class="a-footer-logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="MCA Kosovo home">
                        <?php if (is_array($footer_logo) && !empty($footer_logo['url'])): ?>
                            <img src="<?php echo esc_url($footer_logo['url']); ?>" alt="<?php echo esc_attr(($footer_logo['alt'] ?? '') ?: 'MCA Kosovo'); ?>" loading="lazy" decoding="async">
                        <?php else: echo wp_get_attachment_image(is_array($footer_logo) ? ($footer_logo['ID'] ?? 0) : $footer_logo, 'medium', false, ['loading' => 'lazy', 'decoding' => 'async']); endif; ?>
                    </a>
                <?php endif; ?>
                <?php if ($description): ?><div class="m-footer__description s-d-t-s s-d-b-s s-m-t-xs s-m-b-xs"><p class="a-text a-text--l"><?php echo nl2br(esc_html($description)); ?></p></div><?php endif; ?>
                <?php if ($socials): ?>
                    <nav class="m-footer__socials" aria-label="Social media">
                        <ul>
                            <?php foreach ($socials as $social): if (empty($social['url'])) continue; ?>
                                <li><a class="a-footer-social" href="<?php echo esc_url($social['url']); ?>" aria-label="<?php echo esc_attr(($social['icon_text'] ?? '') ?: ucwords(str_replace(['icon-', '-'], ['', ' '], $social['icon'] ?? 'Social media'))); ?>">
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
                <?php foreach ($columns as $location => $title): ?>
                    <nav class="m-footer__column" aria-label="<?php echo esc_attr($title); ?>">
                        <h2 class="a-footer-label s-d-b-s s-m-b-xs a-text a-text--xs"><?php echo esc_html($title); ?></h2>
                        <?php $render_menu($location); ?>
                    </nav>
                <?php endforeach; ?>
            </div>
            <div class="m-footer__contact">
                <h2 class="a-footer-label s-d-b-s s-m-b-xs a-text a-text--xs"><?php echo esc_html($contact_title); ?></h2>
                <address>
                    <?php if ($address): ?>
                        <div class="m-footer__contact-row">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M19 10c0 5-7 12-7 12S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            <?php if (!empty($address_link['url'])): ?><a class="a-footer-link a-text a-text--l" href="<?php echo esc_url($address_link['url']); ?>"><?php echo nl2br(esc_html($address)); ?></a><?php else: ?><p class="a-text a-text--s"><?php echo nl2br(esc_html($address)); ?></p><?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($email): ?>
                        <div class="m-footer__contact-row">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="1"/><path d="m3 6 9 7 9-7"/></svg>
                            <a class="a-footer-link a-text a-text--l" href="<?php echo esc_url('mailto:' . sanitize_email($email)); ?>"><?php echo esc_html($email); ?></a>
                        </div>
                    <?php endif; ?>
                </address>
                <?php if (!empty($report['url'])): ?><a class="a-footer-report a-text a-text--xs" href="<?php echo esc_url($report['url']); ?>" target="<?php echo esc_attr(($report['target'] ?? '') ?: '_self'); ?>"<?php if (($report['target'] ?? '') === '_blank'): ?> rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html(($report['title'] ?? '') ?: 'Report fraud & corruption'); ?> <span aria-hidden="true">&rarr;</span></a><?php endif; ?>
            </div>
        </div>
        <div class="m-footer__bottom s-d-t-s s-m-t-xs">
            <?php if ($disclaimer): ?><div class="m-footer__disclaimer"><p class="a-text a-text--m"><?php echo nl2br(esc_html(wp_strip_all_tags($disclaimer))); ?></p></div><?php endif; ?>
            <div class="m-footer__legal-row">
                <div class="m-footer__copyright"><p class="a-text a-text--xs h-semibold"><?php echo $copyright ? esc_html(wp_strip_all_tags($copyright)) : esc_html(sprintf('© %s MCA Kosovo. All rights reserved.', wp_date('Y'))); ?></p></div>
                <nav class="m-footer__legal" aria-label="Legal and privacy">
                    <?php $render_menu($legal_menu); ?>
                </nav>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
