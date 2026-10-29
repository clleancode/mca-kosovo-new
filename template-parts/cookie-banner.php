<?php
$cookie_text = static function ($key, $default) {
    $value = get_field('cookie_banner_' . $key, 'option');
    return is_string($value) && trim($value) !== '' ? $value : $default;
};
$policy_link = get_field('cookie_banner_policy_link', 'option');
$privacy_url = !empty($policy_link['url']) ? $policy_link['url'] : (get_privacy_policy_url() ?: home_url('/privacy-policy/'));
$privacy_title = !empty($policy_link['title']) ? $policy_link['title'] : 'Privacy Policy and Cookie Policy';
$privacy_target = !empty($policy_link['target']) ? $policy_link['target'] : '_self';

$categories = [
    'functional'  => ['Functional', 'Required for the website to work.'],
    'preferences' => ['Preferences', 'Stores preferences you set, like language.'],
    'statistics'  => ['Statistics', 'Used exclusively for anonymous statistical purposes.'],
    'marketing'   => ['Marketing', 'Used to create profiles for advertising or tracking.'],
];
foreach ($categories as $key => [$label, $description]) {
    $categories[$key] = [
        $cookie_text($key . '_title', $label),
        $cookie_text($key . '_description', $description),
    ];
}
?>
<section class="o-cookie-banner" id="cookie-banner" aria-label="Cookie consent" hidden>
    <div class="m-cookie-banner s-m-t-xs s-m-b-xs s-d-t-s s-d-b-s" data-cookie-notice>
        <div class="m-cookie-banner__icon" aria-hidden="true">
            <img
                src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/cookie.svg')); ?>"
                alt=""
            >
        </div>

        <div class="m-cookie-banner__content">
            <p class="a-heading a-heading--h6"><?php echo esc_html($cookie_text('title', 'We use cookies')); ?></p>
            <p class="a-text a-text--m h-regular">
                <?php echo nl2br(esc_html($cookie_text('description', 'We use cookies to improve your experience on this website, for traffic analysis and for digital marketing. You can accept all cookies, refuse them or manage your preferences.'))); ?>
                <?php echo esc_html($cookie_text('policy_intro', 'Read our')); ?> <a href="<?php echo esc_url($privacy_url); ?>" target="<?php echo esc_attr($privacy_target); ?>"<?php if ($privacy_target === '_blank') : ?> rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html($privacy_title); ?></a>.
            </p>
        </div>

        <div class="m-cookie-banner__actions s-d-t-xs s-m-t-xs">
            <a class="a-text a-text--m h-semibold" href="#cookie-consent-title" role="button" data-cookie-action="preferences"><?php echo esc_html($cookie_text('preferences_label', 'View preferences')); ?></a>
            <a class="a-text a-text--m h-semibold" href="#cookie-consent-title" role="button" data-cookie-action="deny"><?php echo esc_html($cookie_text('deny_label', 'Deny')); ?></a>
            <a class="a-text a-text--m h-semibold" href="#cookie-consent-title" role="button" data-cookie-action="accept"><?php echo esc_html($cookie_text('accept_label', 'Accept all')); ?></a>
        </div>
    </div>

    <div class="m-cookie-banner__overlay" data-cookie-overlay hidden>
    <div role="dialog" aria-modal="true" tabindex="-1" data-cookie-panel class="m-cookie-banner__dialog s-m-t-xs s-m-b-xs s-d-t-s s-d-b-s" aria-labelledby="cookie-consent-title">
        <div class="m-cookie-banner__heading">
            <p class="a-heading a-heading--h5" id="cookie-consent-title"><?php echo esc_html($cookie_text('panel_title', 'Manage consent')); ?></p>
            <button
                class="a-text a-text--xxl"
                type="button"
                data-cookie-action="close"
                aria-label="<?php echo esc_attr($cookie_text('close_label', 'Close cookie preferences')); ?>"
            ><img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/close.svg')); ?>" alt="" aria-hidden="true"></button>
        </div>

        <div class="m-cookie-banner__categories">
            <?php foreach ($categories as $key => [$label, $description]) : ?>
                <label class="m-cookie-banner__category s-m-t-xs s-m-b-xs s-d-t-xs s-d-b-xs">
                    <span>
                        <strong class="a-text a-text--xxl h-semibold"><?php echo esc_html($label); ?></strong>
                        <?php if ($key === 'functional') : ?>
                            <small class="a-text a-text--xs"><?php echo esc_html($cookie_text('always_active_label', 'Always active')); ?></small>
                        <?php endif; ?>
                        <span class="a-text a-text--m h-regular"><?php echo nl2br(esc_html($description)); ?></span>
                    </span>
                    <input
                        type="checkbox"
                        role="switch"
                        name="<?php echo esc_attr($key); ?>"
                        aria-label="<?php echo esc_attr($label); ?> cookies"
                        <?php if ($key === 'functional') : ?>checked disabled<?php endif; ?>
                    >
                </label>
            <?php endforeach; ?>
        </div>

        <div class="m-cookie-banner__actions s-d-t-xs s-m-t-xs">
            <a class="a-text a-text--m h-semibold" href="#cookie-consent-title" role="button" data-cookie-action="deny"><?php echo esc_html($cookie_text('deny_label', 'Deny')); ?></a>
            <a class="a-text a-text--m h-semibold" href="#cookie-consent-title" role="button" data-cookie-action="save"><?php echo esc_html($cookie_text('save_label', 'Save preferences')); ?></a>
            <a class="a-text a-text--m h-semibold" href="#cookie-consent-title" role="button" data-cookie-action="accept"><?php echo esc_html($cookie_text('accept_label', 'Accept all')); ?></a>
        </div>
    </div>
    </div>
</section>