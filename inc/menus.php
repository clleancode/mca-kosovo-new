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
		'resources_documents' => esc_html__('Resources - Documents', 'MCA'),
		'resources_media' => esc_html__('Resources - Media', 'MCA'),
		'resources_navigation' => esc_html__('Resources - Transparency', 'MCA'),
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
    private $projects_menu = false;
    private $resources_menu = false;
    private $projects_url = '';

    private function get_projects() {
        $projects = [];
        $menu_locations = get_nav_menu_locations();
        $footer_menu_id = $menu_locations['footer_projects'] ?? 0;
        $footer_items = $footer_menu_id ? wp_get_nav_menu_items($footer_menu_id) : [];

        foreach ((array) $footer_items as $menu_item) {
            if (
                ($menu_item->object ?? '') !== 'project'
                || empty($menu_item->object_id)
                || get_post_status($menu_item->object_id) !== 'publish'
            ) {
                continue;
            }

            $projects[] = [
                'id' => (int) $menu_item->object_id,
                'title' => $menu_item->title,
                'url' => $menu_item->url,
            ];
        }

        if (!$projects) {
            foreach (get_posts([
                'post_type' => 'project',
                'post_status' => 'publish',
                'numberposts' => 3,
                'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
            ]) as $project) {
                $projects[] = [
                    'id' => $project->ID,
                    'title' => get_the_title($project),
                    'url' => get_permalink($project),
                ];
            }
        }

        return array_slice($projects, 0, 3);
    }

    private function mobile_projects_submenu() {
        $output = '<nav class="m-header__sub-menu"><ul>';

        foreach ($this->get_projects() as $project) {
            $output .= '<li><a class="a-list-item a-text a-text--l" href="' . esc_url($project['url']) . '">';
            $output .= esc_html($project['title']) . '</a></li>';
        }

        return $output . '</ul></nav>';
    }

    private function projects_dropdown() {
        $projects = $this->get_projects();

        $output = '<div id="projects-sub-menu" class="m-sub-menu m-sub-menu--projects"><div class="m-sub-menu__intro">';
        $output .= '<p class="a-text a-text--s h-semibol">Compact projects</p>';
        $output .= '<h2 class="a-heading a-heading--h5">Three projects.<br>One Compact.</h2>';
        $output .= '<a class="a-text a-text--m h-semibold" href="' . esc_url($this->projects_url) . '">Compact overview <span class="icon-arrow-right-up a-icon" aria-hidden="true"></span></a>';
        $output .= '</div><ul class="m-sub-menu__project-list">';

        foreach ($projects as $index => $project) {
            $project_id = $project['id'];
            $project_number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
            $project_title = $project['title'] ?: get_the_title($project_id);
            $project_slug = get_post_field('post_name', $project_id);
            $project_styles = [
                'energy-storage' => 'energy-storage',
                'jeta' => 'jeta',
                'acfd' => 'acfd',
                'acfg' => 'acfd',
            ];
            $project_style = $project_styles[$project_slug] ?? sanitize_html_class($project_slug);
            $project_class = sanitize_html_class('m-project-box--' . $project_style);
            $card_copy = [
                'energy-storage' => ['title' => 'Energy Storage', 'description' => '340 MWh of battery storage'],
                'jeta' => ['title' => 'JETA', 'description' => 'Skills for the energy workforce'],
                'acfd' => ['title' => 'ACFD', 'description' => 'Catalyzing private investment'],
            ];
            $description = get_field('short_description', $project_id) ?: get_the_excerpt($project_id);
            $image_id = get_post_thumbnail_id($project_id);
            $hero_description = '';

            foreach (parse_blocks(get_post_field('post_content', $project_id)) as $project_block) {
                if (($project_block['blockName'] ?? '') !== 'acf/projects-hero') {
                    continue;
                }

                $image_id = absint($project_block['attrs']['data']['small_hero_image'] ?? $image_id);
                $hero_description = $project_block['attrs']['data']['small_hero_description'] ?? '';
                break;
            }
            $project_title = $card_copy[$project_slug]['title'] ?? $project_title;
            $description = $description ?: ($card_copy[$project_slug]['description'] ?? $hero_description);
            $description = wp_trim_words(wp_strip_all_tags($description), 8, '...');

            $output .= '<li><article class="m-project-box ' . esc_attr($project_class) . '">';
            $output .= '<a href="' . esc_url($project['url']) . '" class="m-project-box__link" aria-label="' . esc_attr('Explore ' . $project_title) . '">';
            $output .= wp_get_attachment_image($image_id, 'large', false, [
                'class' => 'a-img',
                'loading' => 'lazy',
                'decoding' => 'async',
            ]);
            $output .= '<div class="m-content"><p class="a-text a-text--xs">' . esc_html($project_number) . '</p>';
            $output .= '<h2 class="a-heading a-heading--h5">' . esc_html($project_title) . '</h2>';
            if ($description) {
                $output .= '<p class="a-text a-text--m">' . esc_html($description) . '</p>';
            }

            $output .= '</div><span class="icon-arrow-right-up a-icon" aria-hidden="true"></span></a></article></li>';
        }

        return $output . '</ul></div>';
    }

    private function resources_dropdown() {
        $locations = get_nav_menu_locations();
        $columns = [
            'Documents' => $locations['resources_documents'] ?? 0,
            'Media' => $locations['resources_media'] ?? 0,
            'Transparency' => $locations['resources_navigation'] ?? 0,
        ];

        $output = '<div id="resources-sub-menu" class="m-sub-menu m-sub-menu--resources"><div class="m-sub-menu__resources-groups">';

        foreach ($columns as $heading => $menu_id) {
            $menu_items = $menu_id ? wp_get_nav_menu_items($menu_id) : [];
            $output .= '<section><h2 class="a-text a-text--xs">' . esc_html($heading) . '</h2><ul>';

            foreach ((array) $menu_items as $menu_item) {
                $output .= '<li><a class="a-text a-text--xxl" href="' . esc_url($menu_item->url) . '">';
                $output .= '<span>' . esc_html($menu_item->title) . '</span>';
                $output .= '<span class="icon-arrow-right-up a-icon" aria-hidden="true"></span></a></li>';
            }

            $output .= '</ul></section>';
        }

        $output .= '</div>';

        $document = get_field('resources_featured_document', 'option');
        if (is_array($document) && !empty($document['url'])) {
            $document_cover = get_field('resources_featured_document_cover', 'option');
            $document_cover_id = is_array($document_cover)
                ? absint($document_cover['ID'] ?? 0)
                : absint($document_cover);
            $document_title = get_field('resources_featured_document_title', 'option');
            $document_title = $document_title ?: ($document['title'] ?? '');

            $output .= '<a class="m-sub-menu__featured" href="' . esc_url($document['url']) . '" download>';
            if ($document_cover_id) {
                $output .= wp_get_attachment_image($document_cover_id, 'medium', false, [
                    'class' => 'a-img',
                    'loading' => 'lazy',
                    'decoding' => 'async',
                ]);
            }
            $output .= '<div><p class="a-text a-text--xs">Featured</p>';
            $output .= '<h2 class="a-heading a-heading--h6">' . esc_html($document_title) . '</h2>';
            $output .= '<p class="a-text a-text--m">Download PDF</p></div></a>';
        }

        return $output . '</div>';
    }

    function start_lvl( &$output, $depth = 0, $args = null ) {
        if ($this->projects_menu || $this->resources_menu) {
            return;
        }

        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"m-sub-menu flex flex-row gap-4\">\n";
    }

    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        if ($depth === 0) {
            $this->projects_menu = strtolower(trim(wp_strip_all_tags($item->title))) === 'projects';
            $this->resources_menu = strtolower(trim(wp_strip_all_tags($item->title))) === 'resources';
            $this->projects_url = $this->projects_menu ? $item->url : '';
            if ($this->projects_menu) {
                $classes[] = 'menu-item--projects-dropdown';
            } elseif ($this->resources_menu) {
                $classes[] = 'menu-item--resources-dropdown';
            }
        } elseif ($this->projects_menu || $this->resources_menu) {
            return;
        }

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
            if ($this->projects_menu) {
                $output .= '<button type="button" class="a-list-item a-list-item--m" aria-haspopup="true" aria-controls="projects-sub-menu">' . esc_html($item->title) . '</button>';
                $output .= $this->projects_dropdown();
            } elseif ($this->resources_menu) {
                $output .= '<a href="' . esc_url($item->url) . '" class="a-list-item a-list-item--m" aria-haspopup="true" aria-controls="resources-sub-menu">' . esc_html($item->title) . '</a>';
                $output .= $this->resources_dropdown();
            } else {
                $output .= '<a href="' . esc_url($item->url) . '" class="a-list-item a-list-item--m">' . esc_html($item->title) . '</a>';
            }
        }
    }

    function end_el( &$output, $item, $depth = 0, $args = null ) {
        if ($depth > 0 && ($this->projects_menu || $this->resources_menu)) {
            return;
        }

        $output .= "</li>\n";
        if ($depth === 0 && $this->projects_menu) {
            $this->projects_menu = false;
            $this->projects_url = '';
        }
        if ($depth === 0 && $this->resources_menu) {
            $this->resources_menu = false;
        }
    }

    function end_lvl( &$output, $depth = 0, $args = null ) {
        if ($this->projects_menu || $this->resources_menu) {
            return;
        }

        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }
}



class Custom_Main_Menu_Mobile_Walker extends \Walker_Nav_Menu {
    public function start_lvl(&$output, $depth = 0, $args = null) {
        $output .= '<nav class="m-header__sub-menu" inert>';
        $output .= '<a href="#" class="m-header__submenu-back h-semibold"><span aria-hidden="true">&larr;</span> ' . esc_html__('Back', 'MCA') . '</a><ul>';
    }

    public function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= '</ul></nav>';
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = (array) ($item->classes ?? []);
        $has_children = !empty($args->has_children) || in_array('menu-item-has-children', $classes, true);
        $output .= '<li class="' . esc_attr(implode(' ', array_filter($classes))) . '">';
        $link_class = $depth === 0 ? 'a-list-item h-bold' : 'a-list-item a-text a-text--l h-semibold';
        $attributes = ' class="' . esc_attr($link_class) . '"';
        if ($has_children) {
            $output .= '<button type="button"' . $attributes . ' aria-haspopup="true" aria-expanded="false">';
        } else {
            $attributes .= ' href="' . esc_url($item->url) . '"';
            if (!empty($item->attr_title)) $attributes .= ' title="' . esc_attr($item->attr_title) . '"';
            if (!empty($item->target)) $attributes .= ' target="' . esc_attr($item->target) . '"';
            $rel = $item->xfn ?? '';
            if (($item->target ?? '') === '_blank') $rel = trim($rel . ' noopener noreferrer');
            if ($rel) $attributes .= ' rel="' . esc_attr($rel) . '"';
            $output .= '<a' . $attributes . '>';
        }
        $output .= esc_html($item->title);
        if ($has_children) $output .= ' <i class="icon-arrow-right a-icon" aria-hidden="true"></i>';
        $output .= $has_children ? '</button>' : '</a>';
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
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
