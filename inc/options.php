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
        'page_title' => __( 'Theme General Settings', 'MCA' ),
        'menu_title' => __( 'Theme Settings', 'MCA' ),
        'menu_slug'  => 'theme-general-settings',
        'capability' => 'edit_posts',
        'redirect'   => false,
    ] );
}

add_action( 'acf/init', function () {
    if ( ! class_exists( FieldsBuilder::class ) ) {
        return;
    }

    $themeSettings = new FieldsBuilder( 'theme_settings' );

    $themeSettings
        ->setLocation( 'options_page', '==', 'theme-general-settings' )

        // Social Media
        ->addTab( 'Social Media', [ 'label' => 'Social Media' ] )
        ->addRepeater( 'social_media_links', [
            'label'        => 'Social Media Links',
            'layout'       => 'row',
            'button_label' => 'Add Social Media Link',
        ] )
            ->addSelect( 'icon', [
                'label'    => 'Social icon',
                'choices'  => [
                    'icon-facebook'  => 'Facebook',
                    'icon-instagram' => 'Instagram',
                    'icon-linkedin'  => 'LinkedIn',
                    'icon-youtube'   => 'YouTube',
                ],
                'required' => 1,
            ] )
            ->addUrl( 'url', [
                'label'    => 'URL',
                'required' => false,
            ] )
        ->endRepeater()

        // Content
        ->addTab( 'Content', [ 'label' => 'Content' ] )
        ->addImage( 'footer_logo', [
            'label'         => 'Footer Logo',
            'return_format' => 'array',
            'preview_size'  => 'medium',
            'library'       => 'all',
            'required'      => 0,
        ] )
        ->addTextarea( 'footer_description', [
            'label'         => 'Footer introduction',
            'rows'          => 3,
            'default_value' => 'Millennium Challenge Account Kosovo implements the $236.7M MCC-Kosovo Compact — investing in energy security, a skilled workforce and private-sector growth.',
        ] )
        ->addText( 'footer_cookie_settings_label', [
            'label'         => 'Cookie settings label',
            'default_value' => 'Cookie settings',
        ] )

        // Footer contact
        ->addTab( 'Footer contact' )
        ->addText( 'contact_us_title', [
            'label'         => 'Contact Us Title',
            'default_value' => 'Contact',
            'required'      => 1,
        ] )
        ->addTextarea( 'contact_address', [
            'label'    => 'Address',
            'required' => 1,
        ] )
        ->addEmail( 'contact_email', [
            'label'    => 'Email Address',
            'required' => 1,
        ] )
        ->addLink( 'footer_address_link', [
            'label' => 'Address map link (optional)',
        ] )

        // Footer legal content
        ->addTab( 'Footer legal content' )
        ->addTextarea( 'footer_disclaimer', [
            'label'         => 'Partnership disclaimer',
            'rows'          => 3,
            'default_value' => 'This website was made possible through a partnership between the American people and the Republic of Kosovo through the Millennium Challenge Corporation (mcc.gov). The information provided is not official U.S. Government information and does not represent the views or positions of the U.S. Government or the Millennium Challenge Corporation.',
        ] )
        ->addLink( 'footer_button', [
            'label'    => 'Report Fraud and Corruption',
            'required' => 0,
        ] )
        ->addWysiwyg( 'footer_copyright', [
            'label'        => 'Copyright',
            'required'     => 0,
            'instructions' => 'Enter the copyright text.',
        ] )

        // Banner
        ->addTab( 'Banner', [ 'label' => 'Banner' ] )
        ->addTextarea( 'banner_title', [
            'label'        => 'Title',
            'instructions' => 'Enter the title text.',
            'required'     => true,
        ] )
        ->addTextarea( 'banner_text', [
            'label'        => 'Text',
            'instructions' => 'Enter the text.',
            'required'     => false,
        ] )
        ->addLink( 'banner_button', [
            'label'         => 'Button',
            'instructions'  => 'Add a button for the banner (optional).',
            'required'      => false,
            'return_format' => 'array',
        ] )

        // Resources menu
        ->addTab( 'Resources', [ 'label' => 'Resources menu' ] )
        ->addText( 'resources_featured_document_title', [
            'label' => 'Featured document title',
        ] )
        ->addFile( 'resources_featured_document', [
            'label'         => 'Featured document PDF',
            'return_format' => 'array',
            'mime_types'    => 'pdf',
        ] )
        ->addImage( 'resources_featured_document_cover', [
            'label'         => 'Featured document cover',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ] );

    // Cookie banner
    $themeSettings
        ->addTab( 'Cookie banner' )
        ->addText( 'cookie_banner_title', [
            'label' => 'Banner title',
        ] )
        ->addTextarea( 'cookie_banner_description', [
            'label' => 'Banner description',
            'rows'  => 3,
        ] )
        ->addText( 'cookie_banner_policy_intro', [
            'label' => 'Text before policy link',
        ] )
        ->addLink( 'cookie_banner_policy_link', [
            'label'         => 'Privacy / cookie policy link',
            'instructions'  => 'Leave empty to use the WordPress Privacy Policy page. The link title is shown in the banner.',
            'return_format' => 'array',
        ] )
        ->addText( 'cookie_banner_panel_title', [
            'label' => 'Preferences panel title',
        ] )
        ->addText( 'cookie_banner_preferences_label', [
            'label' => 'View preferences label',
        ] )
        ->addText( 'cookie_banner_deny_label', [
            'label' => 'Deny label',
        ] )
        ->addText( 'cookie_banner_save_label', [
            'label' => 'Save preferences label',
        ] )
        ->addText( 'cookie_banner_accept_label', [
            'label' => 'Accept all label',
        ] )
        ->addText( 'cookie_banner_always_active_label', [
            'label' => 'Always active label',
        ] )
        ->addText( 'cookie_banner_close_label', [
            'label' => 'Close panel accessible label',
        ] );

    $cookieCategories = [
        'functional'  => 'Functional',
        'preferences' => 'Preferences',
        'statistics'  => 'Statistics',
        'marketing'   => 'Marketing',
    ];

    foreach ( $cookieCategories as $key => $label ) {
        $themeSettings
            ->addText( "cookie_banner_{$key}_title", [
                'label' => "{$label} title",
            ] )
            ->addTextarea( "cookie_banner_{$key}_description", [
                'label' => "{$label} description",
                'rows'  => 2,
            ] );
    }

    acf_add_local_field_group( $themeSettings->build() );
} );