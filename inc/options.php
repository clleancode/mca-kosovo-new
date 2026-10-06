<?php
/**
 * Theme Settings Options
 *
 * @package MCA
 */

namespace MCA\Options;

use StoutLogic\AcfBuilder\FieldsBuilder;


if ( function_exists( 'acf_add_options_page' ) ) {
    acf_add_options_page( [
        'page_title'  => __('Theme General Settings', 'MCA'),
        'menu_title'  => __('Theme Settings', 'MCA'),
        'menu_slug'   => 'theme-general-settings',
        'capability'  => 'edit_posts',
        'redirect'    => false,
    ] );
}

add_action( 'acf/init', function() {  

    if (class_exists(FieldsBuilder::class)) {
        $themeSettings = new FieldsBuilder('theme_settings');

        $themeSettings
            ->setLocation('options_page', '==', 'theme-general-settings')
            ->addTab('Social Media', ['label' => 'Social Media'])
            ->addRepeater('social_media_links', [
                'label' => 'Social Media Links',
                'layout' => 'row',
                'button_label' => 'Add Social Media Link'
            ])
                ->addSelect('icon', [
                    'label' => 'Social icon',
                    'choices' => ['icon-facebook' => 'Facebook', 'icon-instagram' => 'Instagram', 'icon-linkedin' => 'LinkedIn', 'icon-youtube' => 'YouTube'],
                    'required' => 1,
                ])
                ->addUrl('url', [
                    'label' => 'URL',
                    'required' => false,
                ])
            ->endRepeater()
            ->addTab('Content', ['label' => 'Content'])
            ->addImage('footer_logo', [
                'label' => 'Footer Logo',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'required' => 0,
            ])
            ->addTextarea('footer_description', [
                'label' => 'Footer introduction',
                'rows' => 3,
                'default_value' => 'Millennium Challenge Account Kosovo implements the $236.7M MCC-Kosovo Compact — investing in energy security, a skilled workforce and private-sector growth.',
            ])
            ->addText('footer_cookie_settings_label', [
                'label' => 'Cookie settings label', 'default_value' => 'Cookie settings',
            ])
            ->addTab('Footer contact')
            ->addText('contact_us_title', [
                'label' => 'Contact Us Title',
                'default_value' => 'Contact',
                'required' => 1,
            ])
            ->addTextarea('contact_address', [
                'label' => 'Address',
                'required' => 1,
            ])
            ->addEmail('contact_email', [
                'label' => 'Email Address',
                'required' => 1,
            ])
            ->addLink('footer_address_link', ['label' => 'Address map link (optional)'])
            ->addTab('Footer legal content')
            ->addTextarea('footer_disclaimer', [
                'label' => 'Partnership disclaimer', 'rows' => 3,
                'default_value' => 'This website was made possible through a partnership between the American people and the Republic of Kosovo through the Millennium Challenge Corporation (mcc.gov). The information provided is not official U.S. Government information and does not represent the views or positions of the U.S. Government or the Millennium Challenge Corporation.',
            ])
            ->addLink('footer_button', [
                'label' => 'Report Fraud and Corruption',
                'required' => 0,
            ])
            ->addWysiwyg('footer_copyright', [
                'label' => 'Copyright',
                'required' => 0,
                'instructions' => 'Enter the copyrighttext.',
            ])
            ->addTab('Banner', ['label' => 'Banner'])
            ->addTextarea( 'banner_title', [
                'label' => 'Title',
                'instructions' => 'Enter the title text.',
                'required' => true,
            ])
            ->addTextarea( 'banner_text', [
                'label' => 'text',
                'instructions' => 'Enter the text.',
                'required' => false,
            ])
            ->addLink( 'banner_button', [
                'label' => 'Button',
                'instructions' => 'Add a button for the banner (optional).',
                'required' => false,
                'return_format' => 'array',
            ]);

        acf_add_local_field_group( $themeSettings->build() );
    }
});
