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

use Walker_Nav_Menu;
use WordPress\Themes\YulaiFederation;
use WordPress\Themes\YulaiFederation\Helper\EsiHelper;
use function __;
use function admin_url;
use function apply_filters;
use function array_filter;
use function current_user_can;
use function defined;
use function esc_attr;
use function esc_url;
use function extract;
use function get_option;
use function get_post_meta;
use function is_object;
use function sanitize_title;
use function sprintf;
use function str_repeat;
use function strcasecmp;

defined('ABSPATH') or die();

class BootstrapMenuWalker extends Walker_Nav_Menu {
    /**
     * Theme Options
     *
     * @var array
     */
    private $themeOptions;

    /**
     * EVE API
     *
     * @var EsiHelper
     */
    private $eveApi;

    /**
     * constructor
     */
    public function __construct() {
        $this->themeOptions = get_option(
            'yulai_theme_options',
            YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions()
        );
        $this->eveApi = EsiHelper::getInstance();
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
    public static function fallback(array $args): void {
        if (current_user_can('manage_options')) {
            extract($args);

            $fbOutput = null;

            if ($container) {
                $fbOutput = '<' . $container;

                if ($container_id) {
                    $fbOutput .= ' id="' . $container_id . '"';
                }

                if ($container_class) {
                    $fbOutput .= ' class="' . $container_class . '"';
                }

                $fbOutput .= '>';
            }

            $fbOutput .= '<ul';

            if ($menu_id) {
                $fbOutput .= ' id="' . $menu_id . '"';
            }

            if ($menu_class) {
                $fbOutput .= ' class="' . $menu_class . '"';
            }

            $fbOutput .= '>';
            $fbOutput .= '<li><a href="' . admin_url('nav-menus.php') . '">' . __('Add a menu', 'yulai-federation') . '</a></li>';
            $fbOutput .= '</ul>';

            if ($container) {
                $fbOutput .= '</' . $container . '>';
            }

            echo $fbOutput;
        }
    }

    /**
     * @param string $output Passed by reference. Used to append additional content.
     * @param int $depth Depth of page. Used for padding.
     * @see Walker::start_lvl()
     * @since 3.0.0
     *
     */
    public function start_lvl(&$output, $depth = 0, $args = []): void {
        $indent = str_repeat("\t", $depth);
        $output .= "\n" . $indent . '<ul role="menu" class="dropdown-menu clearfix">' . "\n";
    }

    /**
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $data_object Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param object $args
     * @param int $current_object_currentobject_id Menu item ID.
     * @since 3.0.0
     *
     * @see Walker::start_el()
     */
    public function start_el(&$output, $data_object, $depth = 0, $args = [], $current_object_currentobject_id = 0): void {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        /**
         * Dividers, Headers or Disabled
         * =============================
         * Determine whether the item is a Divider, Header, Disabled or regular
         * menu item. To prevent errors, we use the strcasecmp() function to so a
         * comparison that is not case-sensitive. The strcasecmp() function returns
         * a 0 if the strings are equal.
         */
        if ($depth === 1 && strcasecmp($data_object->attr_title, 'divider') === 0) {
            $output .= $indent . '<li role="presentation" class="divider">';
        } else if ($depth === 1 && strcasecmp($data_object->title, 'divider') === 0) {
            $output .= $indent . '<li role="presentation" class="divider">';
        } else if ($depth === 1 && strcasecmp($data_object->attr_title, 'dropdown-header') === 0) {
            $output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr($data_object->title);
        } else if (strcasecmp($data_object->attr_title, 'disabled') === 0) {
            $output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr($data_object->title) . '</a>';
        } else {
            $value = '';
            $classes = empty($data_object->classes) ? [] : (array)$data_object->classes;
            $classes[] = 'menu-item-' . $data_object->ID;
            $classes[] = 'post-item-' . $data_object->object_id;
            $classNames = implode(' ', apply_filters('nav_menu_css_class', array_filter($classes), $data_object, $args));

            if ($args->has_children) {
                if ($depth === '0') {
                    // first level
                    $classNames .= ' dropdown';
                } else {
                    // next levels
                    $classNames .= ' dropdown-submenu';
                }
            }

            if (in_array('current-menu-item', $classes)) {
                $classNames .= ' active';
            }

            // let's check if a page actually has content ...
            $hasContent = true;
            if ($data_object->post_parent !== 0 && YulaiFederation\Helper\PostHelper::getInstance()->hasContent($data_object->object_id) === false) {
                $hasContent = false;
                $classNames .= ' no-post-content';
            }

            $classNames = $classNames ? ' class="' . esc_attr($classNames) . '"' : '';

            $id = apply_filters('nav_menu_item_id', 'menu-item-' . $data_object->ID, $data_object, $args);
            $id = $id ? ' id="' . esc_attr($id) . '"' : '';

            $output .= $indent . '<li' . $id . $value . $classNames . '>';

            $atts = [];
            $atts['title'] = !empty($data_object->title) ? $data_object->title : '';
            $atts['target'] = !empty($data_object->target) ? $data_object->target : '';
            $atts['rel'] = !empty($data_object->xfn) ? $data_object->xfn : '';

            // If item has_children add atts to a.
            $atts['href'] = !empty($data_object->url) ? $data_object->url : '';
            if ($args->has_children && $depth === 0) {
                $atts['data-toggle'] = 'dropdown';
                $atts['class'] = 'dropdown-toggle';
            }

            $atts = apply_filters('nav_menu_link_attributes', $atts, $data_object, $args);

            $attributes = '';
            foreach ($atts as $attr => $value) {
                if (!empty($value)) {
                    if ($attr === 'href') {
                        $value = esc_url($value);

                        // remove the link of no description is available
                        if ($hasContent === false) {
                            $value = '#';
                        }
                    } else {
                        $value = esc_attr($value);
                    }

                    // change the title if no description is available
                    if (($attr === 'title') && $hasContent === false) {
                        $value .= ' ' . __('(No description available)', 'yulai-federation');
                    }

                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            $itemOutput = $args->before;

            /**
             * Corp Logos
             */
            $yf_page_corp_eve_ID = get_post_meta($data_object->object_id, 'yf_page_corp_eve_ID', true);
            if ($yf_page_corp_eve_ID) {
                if (isset($this->themeOptions['show_corp_logos']['show'])) {
                    $corpLogoPath = sprintf(
                        $this->eveApi->getImageServerEndpoint('corporation') . '?size=32',
                        $yf_page_corp_eve_ID
                    );

                    $itemOutput .= '<a' . $attributes . '><span class="corp-' . sanitize_title($data_object->title) . ' ' . esc_attr($data_object->attr_title) . ' corp-eveID-' . $yf_page_corp_eve_ID . '"><img src="' . $corpLogoPath . '" width="24" height="24" alt="' . $data_object->title . '"></span>&nbsp;';
                } else {
                    $itemOutput .= '<a' . $attributes . '>';
                }
            } else if (!empty($data_object->attr_title)) {
                /**
                 * Glyphicons
                 * ==========================
                 * Since the the menu item is NOT a Divider or Header we check the see
                 * if there is a value in the attr_title property. If the attr_title
                 * property is NOT null, we apply it as the class name for the glyphicon.
                 */
                $itemOutput .= '<a' . $attributes . '><span class="glyphicon ' . esc_attr($data_object->attr_title) . '"></span>&nbsp;';
            } else {
                $itemOutput .= '<a' . $attributes . '>';
            }

            $itemOutput .= $args->link_before . apply_filters('the_title', $data_object->title, $data_object->ID) . $args->link_after;
            $itemOutput .= ($args->has_children && $depth === 0) ? ' <span class="caret"></span></a>' : '</a>';
            $itemOutput .= $args->after;

            $output .= apply_filters('walker_nav_menu_start_el', $itemOutput, $data_object, $depth, $args);
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
     * @param object $element Data object
     * @param array $children_elements List of elements to continue traversing.
     * @param int $max_depth Max depth to traverse.
     * @param int $depth Depth of current element.
     * @param array $args
     * @param string $output Passed by reference. Used to append additional content.
     * @return void Null on failure with no changes to parameters.
     * @since 2.5.0
     *
     * @see Walker::start_el()
     */
    public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output): void {
        if (!$element) {
            return;
        }

        $idField = $this->db_fields['id'];

        // Display this element.
        if (is_object($args[0])) {
            $args[0]->has_children = !empty($children_elements[$element->$idField]);
        }

        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }
}
