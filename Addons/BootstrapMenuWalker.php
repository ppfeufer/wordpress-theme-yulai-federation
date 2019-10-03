<?php

/**
 * Class Name: BootstrapMenuWalker
 * GitHub URI: https://github.com/twittem/wp-bootstrap-navwalker
 * Description: A custom WordPress nav walker class to implement the Bootstrap 3 navigation style in a custom theme using the WordPress built in menu manager.
 * Version: 2.0.4
 * Author: Edward McIntyre - @twittem
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace WordPress\Themes\YulaiFederation\Addons;

use WordPress\Themes\YulaiFederation;

\defined('ABSPATH') or die();

class BootstrapMenuWalker extends \Walker_Nav_Menu {
    /**
     * Theme Options
     *
     * @var array
     */
    private $themeOptions = null;

    /**
     * EVE API
     *
     * @var \WordPress\Themes\YulaiFederation\Helper\EsiHelper
     */
    private $eveApi = null;

    /**
     * constructor
     */
    public function __construct() {
        $this->themeOptions = \get_option('yulai_theme_options', YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions());
        $this->eveApi = YulaiFederation\Helper\EsiHelper::getInstance();
    }

    /**
     * @see Walker::start_lvl()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int $depth Depth of page. Used for padding.
     */
    public function start_lvl(&$output, $depth = 0, $args = []) {
        $indent = \str_repeat("\t", $depth);
        $output .= "\n" . $indent . '<ul role="menu" class="dropdown-menu clearfix">' . "\n";
    }

    /**
     * @see Walker::start_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param object $args
     * @param int $id Menu item ID.
     */
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0) {
        $indent = ($depth) ? \str_repeat("\t", $depth) : '';

        /**
         * Dividers, Headers or Disabled
         * =============================
         * Determine whether the item is a Divider, Header, Disabled or regular
         * menu item. To prevent errors we use the strcasecmp() function to so a
         * comparison that is not case sensitive. The strcasecmp() function returns
         * a 0 if the strings are equal.
         */
        if(\strcasecmp($item->attr_title, 'divider') == 0 && $depth === 1) {
            $output .= $indent . '<li role="presentation" class="divider">';
        } else if(\strcasecmp($item->title, 'divider') == 0 && $depth === 1) {
            $output .= $indent . '<li role="presentation" class="divider">';
        } else if(\strcasecmp($item->attr_title, 'dropdown-header') == 0 && $depth === 1) {
            $output .= $indent . '<li role="presentation" class="dropdown-header">' . \esc_attr($item->title);
        } else if(\strcasecmp($item->attr_title, 'disabled') == 0) {
            $output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . \esc_attr($item->title) . '</a>';
        } else {
            $classNames = $value = '';
            $classes = empty($item->classes) ? [] : (array) $item->classes;
            $classes[] = 'menu-item-' . $item->ID;
            $classes[] = 'post-item-' . $item->object_id;
            $classNames = \join(' ', \apply_filters('nav_menu_css_class', \array_filter($classes), $item, $args));

            if($args->has_children) {
                switch($depth) {
                    // first level
                    case '0':
                        $classNames .= ' dropdown';
                        break;

                    // next levels
                    default:
                        $classNames .= ' dropdown-submenu';
                        break;
                }
            }

            if(in_array('current-menu-item', $classes)) {
                $classNames .= ' active';
            }

            // let's check if a page actually has content ...
            $hasContent = true;
            if($item->post_parent !== 0 && YulaiFederation\Helper\PostHelper::getInstance()->hasContent($item->object_id) === false) {
                $hasContent = false;
                $classNames .= ' no-post-content';
            }

            $classNames = $classNames ? ' class="' . \esc_attr($classNames) . '"' : '';

            $id = \apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
            $id = $id ? ' id="' . \esc_attr($id) . '"' : '';

            $output .= $indent . '<li' . $id . $value . $classNames . '>';

            $atts = [];
            $atts['title'] = !empty($item->title) ? $item->title : '';
            $atts['target'] = !empty($item->target) ? $item->target : '';
            $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';

            // If item has_children add atts to a.
            if($args->has_children && $depth === 0) {
                $atts['href'] = !empty($item->url) ? $item->url : '';
                $atts['data-toggle'] = 'dropdown';
                $atts['class'] = 'dropdown-toggle';
            } else {
                $atts['href'] = !empty($item->url) ? $item->url : '';
            }

            $atts = \apply_filters('nav_menu_link_attributes', $atts, $item, $args);

            $attributes = '';
            foreach($atts as $attr => $value) {
                if(!empty($value)) {
                    if($attr === 'href') {
                        $value = \esc_url($value);

                        // remove the link of no description is available
                        if($hasContent === false) {
                            $value = '#';
                        }
                    } else {
                        $value = \esc_attr($value);
                    }

                    if($attr === 'title') {
                        // change the title if no description is available
                        if($hasContent === false) {
                            $value .= ' ' . \__('(No description available)', 'yulai-federation');
                        }
                    }

                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            $itemOutput = $args->before;

            /**
             * Corp Logos
             */
            $yf_page_corp_eve_ID = \get_post_meta($item->object_id, 'yf_page_corp_eve_ID', true);
            if($yf_page_corp_eve_ID) {
                if(isset($this->themeOptions['show_corp_logos']['show'])) {
                    $corpLogoPath = YulaiFederation\Helper\ImageHelper::getInstance()->getLocalCacheImageUriForRemoteImage('corporation', $this->eveApi->getImageServerEndpoint('corporation') . $yf_page_corp_eve_ID . '_32.png');

                    $itemOutput .= '<a' . $attributes . '><span class="corp-' . \sanitize_title($item->title) . ' ' . \esc_attr($item->attr_title) . ' corp-eveID-' . $yf_page_corp_eve_ID . '"><img src="' . $corpLogoPath . '" width="24" height="24" alt="' . $item->title . '"></span>&nbsp;';
                } else {
                    $itemOutput .= '<a' . $attributes . '>';
                }
            } else {
                /**
                 * Glyphicons
                 * ==========================
                 * Since the the menu item is NOT a Divider or Header we check the see
                 * if there is a value in the attr_title property. If the attr_title
                 * property is NOT null we apply it as the class name for the glyphicon.
                 */
                if(!empty($item->attr_title)) {
                    $itemOutput .= '<a' . $attributes . '><span class="glyphicon ' . \esc_attr($item->attr_title) . '"></span>&nbsp;';
                } else {
                    $itemOutput .= '<a' . $attributes . '>';
                }
            }

            $itemOutput .= $args->link_before . \apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
            $itemOutput .= ( $args->has_children && 0 === $depth ) ? ' <span class="caret"></span></a>' : '</a>';
            $itemOutput .= $args->after;

            $output .= \apply_filters('walker_nav_menu_start_el', $itemOutput, $item, $depth, $args);
        }
    }

    /**
     * Traverse elements to create list from elements.
     *
     * Display one element if the element doesn't have any children otherwise,
     * display the element and its children. Will only traverse up to the max
     * depth and no ignore elements under that depth.
     *
     * This method shouldn't be called directly, use the walk() method instead.
     *
     * @see Walker::start_el()
     * @since 2.5.0
     *
     * @param object $element Data object
     * @param array $children_elements List of elements to continue traversing.
     * @param int $max_depth Max depth to traverse.
     * @param int $depth Depth of current element.
     * @param array $args
     * @param string $output Passed by reference. Used to append additional content.
     * @return null Null on failure with no changes to parameters.
     */
    public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output) {
        if(!$element) {
            return;
        }

        $idField = $this->db_fields['id'];

        // Display this element.
        if(\is_object($args[0])) {
            $args[0]->has_children = !empty($children_elements[$element->$idField]);
        }

        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

    /**
     * Menu Fallback
     * =============
     * If this function is assigned to the wp_nav_menu's fallback_cb variable
     * and a manu has not been assigned to the theme location in the WordPress
     * menu manager the function with display nothing to a non-logged in user,
     * and will add a link to the WordPress menu manager if logged in as an admin.
     *
     * @param array $args passed from the wp_nav_menu function.
     *
     */
    public static function fallback($args) {
        if(\current_user_can('manage_options')) {
            \extract($args);

            $fbOutput = null;

            if($container) {
                $fbOutput = '<' . $container;

                if($container_id) {
                    $fbOutput .= ' id="' . $container_id . '"';
                }

                if($container_class) {
                    $fbOutput .= ' class="' . $container_class . '"';
                }

                $fbOutput .= '>';
            }

            $fbOutput .= '<ul';

            if($menu_id) {
                $fbOutput .= ' id="' . $menu_id . '"';
            }

            if($menu_class) {
                $fbOutput .= ' class="' . $menu_class . '"';
            }

            $fbOutput .= '>';
            $fbOutput .= '<li><a href="' . \admin_url('nav-menus.php') . '">' . __('Add a menu', 'yulai-federation') . '</a></li>';
            $fbOutput .= '</ul>';

            if($container) {
                $fbOutput .= '</' . $container . '>';
            }

            echo $fbOutput;
        }
    }
}
