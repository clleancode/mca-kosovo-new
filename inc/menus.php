<?php
/**
 * Register navigation menus.
 *
 * @package MCA
 */

namespace MCA\Menus;

function register_menus() {
    register_nav_menus( [
        'primary_navigation' => esc_html__('Primary Navigation', 'MCA'),
		'footer_navigation' => esc_html__('Footer Legal Links', 'MCA'),
        'footer_projects' => esc_html__('Footer - Projects', 'MCA'),
        'footer_work_with_us' => esc_html__('Footer - Work with us', 'MCA'),
        'footer_organization' => esc_html__('Footer - Organization', 'MCA'),
    ] );
}
add_action( 'init', __NAMESPACE__ . '\\register_menus' );

class Custom_Footer_Menu_Walker extends \Walker_Nav_Menu {
    public function start_lvl( &$output, $depth = 0, $args = [] ) {
        $output .= "\n<ul>\n";
    }

    public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
        $classes = 'a-text a-text--m h-white-700';
        $output .= '<li><a href="' . esc_url($item->url) . '" class="' . esc_attr($classes) . '">' . esc_html($item->title) . '</a></li>' . "\n";
    }
}

class Custom_Main_Menu_Walker extends \Walker_Nav_Menu {
    function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"m-sub-menu flex flex-row gap-4\">\n";
    }

    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', array_filter($classes));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= '<li' . $class_names . '>';

        if ($depth > 0) {
            $thumb_id = get_post_thumbnail_id($item->object_id);

             if ( 
    isset($item->object)
    && $item->object === 'project'
    && isset($item->object_id)
    && get_post_field('post_name', $item->object_id) === 'energy-storage'
) {
    $item->title = 'Energy <br> Storage';
}

            $output .= '<article class="m-project-box">';
            $output .= '<a href="' . esc_url($item->url) . '" class="m-project-box__link" aria-labelledby="project-title-' . $item->ID . '">';

            if ($thumb_id) {
                $output .= wp_get_attachment_image($thumb_id, 'large', false, [
                    'class' => 'a-img',
                    'loading' => 'lazy',
                    'decoding' => 'async'
                ]);
            }

            $output .= '<div class="m-content">';
            $output .= '<h1 id="project-title-' . $item->ID . '" class="a-heading a-heading--h4 h-uppercase h-semibold">' . wp_kses_post($item->title) . '</h1>';

            if (!empty($item->description)) {
                $output .= '<p class="a-text a-text--l">' . esc_html($item->description) . '</p>';
            }

            $output .= '</div>';

            $output .= '<span class="icon-arrow-right-up a-icon a-icon--xs" aria-hidden="true"></span>';

            $output .= '</a>';
            $output .= '</article>';
        }
        else {
            $output .= '<a href="' . esc_url($item->url) . '" class="a-list-item a-list-item--m">' . esc_html($item->title) . '</a>';
        }
    }

    function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }

    function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }
}



class Custom_Main_Menu_Mobile_Walker extends \Walker_Nav_Menu {
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "\n$indent<nav class=\"m-header__sub-menu\"><ul>\n";
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent</ul></nav>\n";
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? [] : (array) $item->classes;
        $has_children = in_array( 'menu-item-has-children', $classes );
        $is_custom_with_children = ($item->type === 'custom' && $has_children);

        $class_names = implode( ' ', array_filter( $classes ) );
        $class_attr = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $output .= "<li$class_attr>";

        // Add the requested classes to all menu links in the mobile menu
        $link_class = 'a-list-item';
        if ($depth === 0) {
            $link_class .= ' h-bold';
        } else {
            $link_class .= ' a-text a-text--l';
        }

        $attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target ) . '"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
        $attributes .= ( ! empty( $item->url ) && ! $is_custom_with_children ) ? ' href="' . esc_url( $item->url ) . '"' : 'href="#"';
        $attributes .= ' class="' . esc_attr( $link_class ) . '"';
       

        $title = apply_filters( 'the_title', $item->title, $item->ID );

        $output .= '<a' . $attributes . '>' . $title;
        
        // Add dropdown icon for menu items with children
        if ( $has_children && $depth === 0 ) {
            $output .= ' <i class="icon-arrow-right a-icon" aria-hidden="true"></i>';
        }
        
        $output .= '</a>';
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}

class Menu_Tab_Walker extends \Walker_Nav_Menu {
    private $item_count = 0; // Counter to track the index

    function start_lvl(&$output, $depth = 0, $args = []) {
        $output .= "\n<ul class=\"m-menu-tabs\" id=\"tabsList\">\n";
    }

    function end_lvl(&$output, $depth = 0, $args = []) {
        $output .= "</ul>\n";
    }

    function start_el(&$output, $item, $depth = 0, $args = [], $id = 0) {
        $title = esc_html($item->title);
        $item_url = esc_url($item->url);

        $current_url_path = rtrim(wp_parse_url(add_query_arg([]), PHP_URL_PATH), '/');
        $item_url_path = rtrim(wp_parse_url($item_url, PHP_URL_PATH), '/');

        $is_child = false;

        // Skip ancestor check for the first menu item only
        if ($this->item_count > 0 && !empty($item->classes)) {
            foreach ($item->classes as $class) {
                if (strpos($class, 'menu-item-object-') === 0) {
                    $post_type = str_replace('menu-item-object-', '', $class);
                    if (is_singular($post_type)) {
                        $ancestors = get_post_ancestors(get_the_ID());
                        if (in_array($item->object_id, $ancestors)) {
                            $is_child = true;
                            break;
                        }
                    }
                }
            }
        }

        $is_active = ($current_url_path === $item_url_path) || $is_child;
        $active_class = $is_active ? ' active' : '';

        $output .= "<li class=\"a-tab{$active_class}\"><a href=\"{$item_url}\" data-text=\"{$title}\"><span>{$title}</span></a></li>\n";

        $this->item_count++; // Increment after rendering
    }

    function end_el(&$output, $item, $depth = 0, $args = []) {
        // No closing output needed
    }
}
