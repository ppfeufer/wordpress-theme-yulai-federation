<?php

namespace WordPress\Themes\YulaiFederation\Helper;

\defined('ABSPATH') or die();

class NavigationHelper {
    /**
     * instance
     *
     * static variable to keep the current (and only!) instance of this class
     *
     * @var Singleton
     */
    protected static $instance = null;

    public static function getInstance() {
        if(null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * clone
     *
     * no cloning allowed
     */
    protected function __clone() {
        ;
    }

    /**
     * constructor
     *
     * no external instanciation allowed
     */
    protected function __construct() {
        ;
    }

    /**
     * Display page next/previous navigation links.
     *
     * @global object $wp_query
     * @param int $nav_id
     */
    public function getContentNav($nav_id) {
        global $wp_query;

        if($wp_query->max_num_pages > 1) {
            ?>
            <nav id="<?php echo $nav_id; ?>" class="navigation post-navigation clearfix" role="navigation">
                <h3 class="assistive-text"><?php \_e('Post navigation', 'yulai-federation'); ?></h3>
                <div class="nav-previous pull-left">
                    <?php self::getNextPostsLink(\__('<span class="meta-nav">&larr;</span> Older posts', 'yulai-federation'), 0, true); ?>
                </div>
                <div class="nav-next pull-right">
                    <?php self::getPreviousPostsLink(\__('Newer posts <span class="meta-nav">&rarr;</span>', 'yulai-federation'), true); ?>
                </div>
            </nav><!-- #<?php echo $nav_id; ?> .navigation -->
            <?php
        }
    }

    /**
     * Retrieves the next posts page link.
     *
     * @global int      $paged
     * @global WP_Query $wp_query
     *
     * @param string $label    Content for link text.
     * @param int    $max_page Optional. Max pages. Default 0.
     * @return string|void HTML-formatted next posts page link.
     */
    public function getNextPostsLink($label = null, $max_page = 0, $echo = false, $wp_query = null) {
        global $paged;

        if($wp_query === null) {
            global $wp_query;
        }

        if(!$max_page) {
            $max_page = $wp_query->max_num_pages;
        }

        if(!$paged) {
            $paged = 1;
        }

        $nextpage = \intval($paged) + 1;

        if(null === $label) {
            $label = \__('&laquo; Previous Page', 'yulai-federation');
        }

        if(!\is_single() && ($nextpage <= $max_page)) {
            /**
             * Filters the anchor tag attributes for the next posts page link.
             *
             * @since 2.7.0
             *
             * @param string $attributes Attributes for the anchor tag.
             */
            $attr = \apply_filters('previous_posts_link_attributes', '');

            if($echo === true) {
                echo '<a class="btn btn-default" href="' . \next_posts($max_page, false) . "\" $attr>" . \preg_replace('/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label) . '</a>';
            } else {
                return '<a class="btn btn-default" href="' . \next_posts($max_page, false) . "\" $attr>" . \preg_replace('/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label) . '</a>';
            }
        }
    }

    /**
     * Retrieves the previous posts page link.
     *
     * @global int $paged
     *
     * @param string $label Optional. Previous page link text.
     * @param bool $echo Optional. echoing or returning the link.
     * @return string|void HTML-formatted previous page link.
     */
    public function getPreviousPostsLink($label = null, $echo = false) {
        global $paged;

        if(null === $label) {
            $label = \__('Next Page &raquo;', 'yulai-federation');
        }

        if(!\is_single() && $paged > 1) {
            /**
             * Filters the anchor tag attributes for the previous posts page link.
             *
             * @since 2.7.0
             *
             * @param string $attributes Attributes for the anchor tag.
             */
            $attr = \apply_filters('next_posts_link_attributes', '');

            if($echo === true) {
                echo '<a class="btn btn-default" href="' . \previous_posts(false) . "\" $attr>" . \preg_replace('/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label) . '</a>';
            } else {
                return '<a class="btn btn-default" href="' . \previous_posts(false) . "\" $attr>" . \preg_replace('/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label) . '</a>';
            }
        }
    }

    /**
     * Get the Breadcrumb Navigation
     *
     * @global type $post
     * @global object $wp_query
     * @global type $author
     * @param boolean $addTexts
     * @param boolean $echo
     * @return string
     */
    public function getBreadcrumbNavigation($addTexts = true, $echo = false) {
        $home = __('Home', 'yulai-federation'); // text for the 'Home' link
        $before = '<li class="active">'; // tag before the current crumb
        $sep = '';
        $after = '</li>'; // tag after the current crumb

        $breadcrumb = '';

        if(!\is_home() && !\is_front_page() || \is_paged()) {
            $breadcrumb .= '<ul class="breadcrumb">';

            global $post;

            $homeLink = \home_url();

            $breadcrumb .= '<li><a href="' . $homeLink . '">' . $home . '</a> ' . $sep . '</li> ';

            if(\is_category()) {
                global $wp_query;

                $categoryObject = $wp_query->get_queried_object();
                $thisCat = \get_category($categoryObject->term_id);
                $parentCat = \get_category($thisCat->parent);

                if($thisCat->parent != 0) {
                    $breadcrumb .= '<li>' . \get_category_parents($parentCat, true, $sep . '</li><li>') . '</li>';
                }

                $format = $before . ($addTexts ? (__('Archive by category ', 'yulai-federation') . '"%s"') : '%s') . $after;

                $breadcrumb .= \sprintf($format, \single_cat_title('', false));
            } elseif(\is_day()) {
                $breadcrumb .= '<li><a href="' . \get_year_link(\get_the_time('Y')) . '">' . \get_the_time('Y') . '</a></li> ';
                $breadcrumb .= '<li><a href="' . \get_month_link(\get_the_time('Y'), \get_the_time('m')) . '">' . \get_the_time('F') . '</a></li> ';
                $breadcrumb .= $before . \get_the_time('d') . $after;
            } elseif(\is_month()) {
                $breadcrumb .= '<li><a href="' . \get_year_link(\get_the_time('Y')) . '">' . \get_the_time('Y') . '</a></li> ';
                $breadcrumb .= $before . \get_the_time('F') . $after;
            } elseif(\is_year()) {
                $breadcrumb .= $before . \get_the_time('Y') . $after;
            } elseif(\is_single() && !\is_attachment()) {
                if(\get_post_type() != 'post') {
                    $postType = \get_post_type_object(\get_post_type());
                    $slug = $postType->rewrite;

                    $breadcrumb .= '<li><a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $postType->labels->singular_name . '</a></li> ';
                    $breadcrumb .= $before . \get_the_title() . $after;
                } else {
                    $cat = \get_the_category();
                    $cat = $cat[0];

                    $breadcrumb .= '<li>' . \get_category_parents($cat, true, $sep) . '</li>';
                    $breadcrumb .= $before . \get_the_title() . $after;
                }
            } elseif(!\is_single() && !\is_page() && \get_post_type() != 'post' && !\is_404()) {
                $postType = \get_post_type_object(\get_post_type());

                $breadcrumb .= $before . $postType->labels->singular_name . $after;
            } elseif(\is_attachment()) {
                $parent = \get_post($post->post_parent);
                $cat = \get_the_category($parent->ID);
                $cat = (isset($cat['0'])) ? $cat['0'] : '';

                $breadcrumb .= (isset($cat['0'])) ? \get_category_parents($cat, true, $sep) : '';
                $breadcrumb .= '<li><a href="' . \get_permalink($parent) . '">' . $parent->post_title . '</a></li> ';
                $breadcrumb .= $before . \get_the_title() . $after;
            } elseif(\is_page() && !$post->post_parent) {
                $breadcrumb .= $before . get_the_title() . $after;
            } elseif(\is_page() && $post->post_parent) {
                $parent_id = $post->post_parent;
                $breadcrumbs = array();

                while($parent_id) {
                    $page = \get_page($parent_id);
                    $breadcrumbs[] = '<li><a href="' . \get_permalink($page->ID) . '">' . \get_the_title($page->ID) . '</a>' . $sep . '</li>';
                    $parent_id = $page->post_parent;
                }

                $breadcrumbs = \array_reverse($breadcrumbs);

                foreach($breadcrumbs as $crumb) {
                    $breadcrumb .= $crumb;
                }

                $breadcrumb .= $before . \get_the_title() . $after;
            } elseif(\is_search()) {
                $format = $before . ($addTexts ? (\__('Search results for "', 'yulai-federation') . '"%s"') : '%s') . $after;

                $breadcrumb .= \sprintf($format, \get_search_query());
            } elseif(\is_tag()) {
                $format = $before . ($addTexts ? (\__('Posts tagged "', 'yulai-federation') . '"%s"') : '%s') . $after;

                $breadcrumb .= \sprintf($format, \single_tag_title('', false));
            } elseif(\is_author()) {
                global $author;

                $userdata = \get_userdata($author);
                $format = $before . ($addTexts ? (\__('Articles posted by ', 'yulai-federation') . '"%s"') : '%s') . $after;

                $breadcrumb .= \sprintf($format, $userdata->display_name);
            } elseif(\is_404()) {
                $breadcrumb .= $before . \__('Error 404', 'yulai-federation') . $after;
            }

            $breadcrumb .= '</ul>';
        }

        if($echo === true) {
            echo $breadcrumb;
        } else {
            return $breadcrumb;
        }
    }

    /**
     * Articlenavigation.
     * Displaying the next/previous links in single article.
     *
     * @since 0.1-r20170329
     *
     * @package WordPress
     * @subpackage Yulai Federation Theme
     */
    public function getArticleNavigation($echo = false) {
        $htmlOutput = null;
        $previousPostObject = \get_previous_post();
        $nextPostObject = \get_next_post();

        $htmlOutput .= '<nav class="article-navigation clearfix">';
        $htmlOutput .= '<h3 class="assistive-text">' . \__('Post Navigation', 'yulai-federation') . '</h3>';

        $htmlOutput .= '<div class="row clearfix">';
        if($previousPostObject) {
            $htmlOutput .= '<div class="nav-previous ' . \WordPress\Themes\YulaiFederation\Helper\PostHelper::getInstance()->getArticleNavigationPanelClasses() . ' pull-left clearfix">';
            $htmlOutput .= '<div class="nav-previous-link">' . \get_previous_post_link('%link', \__('<span class="meta-nav">&larr;</span> Previous Article', 'yulai-federation')) . '</div>';

            if(\has_post_thumbnail($previousPostObject->ID)) {
                $htmlOutput .= '<div class="nav-previous-thumbnail">';
                $htmlOutput .= '<a href="' . \get_the_permalink($previousPostObject->ID) . '" title="' . \esc_html($previousPostObject->post_title) . '">';
                $htmlOutput .= '<figure class="post-loop-thumbnail">';

                if(\function_exists('\fly_get_attachment_image')) {
                    $htmlOutput .= \fly_get_attachment_image(\get_post_thumbnail_id($previousPostObject->ID), 'post-loop-thumbnail');
                } else {
                    $htmlOutput .= \get_the_post_thumbnail($previousPostObject->ID, 'post-loop-thumbnail');
                }

//                $htmlOutput .= '<figcaption>' . $previousPostObject->post_title . '</figcaption>';
                $htmlOutput .= '</figure>';
                $htmlOutput .= '</a>';
                $htmlOutput .= '</div>';
            } else {
                // Article Image Placeholder. We don't have it yet ....
//                $htmlOutput .= '<a class="related-article-header" href="' . \get_permalink($previousPostObject->ID) . '" rel="bookmark" title="' . \__('Permanent link to: ', 'yulai-federation') . \esc_html($previousPostObject->post_title) . '"><img width="251" height="115" title="' . \__('Placeholder Postthumbnail Related Article', 'yulai-federation') . '" alt="' . \__('Placeholder Postthumbnail Related Article', 'yulai-federation') . '" class="attachment-related-article wp-post-image" src="' . get_theme_file_uri('/images/placeholder/postthumbnail-related-article.jpg') . '" /></a>';
            } // END if(\has_post_thumbnail($obj_PreviousPost->ID))

            $htmlOutput .= '<div><em>' . \esc_html($previousPostObject->post_title) . '</em></div>';
            $htmlOutput .= '</div>';
        } // END if($obj_PreviousPost)

        if($nextPostObject) {
            $htmlOutput .= '<div class="nav-next ' . \WordPress\Themes\YulaiFederation\Helper\PostHelper::getInstance()->getArticleNavigationPanelClasses() . ' pull-right text-align-right clearfix">';
            $htmlOutput .= '<div class="nav-next-link">' . \get_next_post_link('%link', \__('Next Article <span class="meta-nav">&rarr;</span>', 'yulai-federation')) . '</div>';

            if(\has_post_thumbnail($nextPostObject->ID)) {
                $htmlOutput .= '<div class="nav-next-thumbnail">';
                $htmlOutput .= '<a href="' . \get_the_permalink($nextPostObject->ID) . '" title="' . \esc_html($nextPostObject->post_title) . '">';
                $htmlOutput .= '<figure class="post-loop-thumbnail">';

                if(\function_exists('\fly_get_attachment_image')) {
                    $htmlOutput .= \fly_get_attachment_image(\get_post_thumbnail_id($nextPostObject->ID), 'post-loop-thumbnail');
                } else {
                    $htmlOutput .= \get_the_post_thumbnail($nextPostObject->ID, 'post-loop-thumbnail');
                }

//                $htmlOutput .= '<figcaption>' . $nextPostObject->post_title . '</figcaption>';
                $htmlOutput .= '</figure>';
                $htmlOutput .= '</a>';
                $htmlOutput .= '</div>';
            } else {
                // Article Image Placeholder. We don't have it yet ....
//                $htmlOutput .= '<a class="related-article-header" href="' . \get_permalink($nextPostObject->ID) . '" rel="bookmark" title="' . \__('Permanent link to: ', 'yulai-federation') . \esc_html($nextPostObject->post_title) . '"><img width="251" height="115" title="' . \__('Placeholder Postthumbnail Related Article', 'yulai-federation') . '" alt="' . \__('Placeholder Postthumbnail Related Article', 'yulai-federation') . '" class="attachment-related-article wp-post-image" src="' . get_theme_file_uri('/images/placeholder/postthumbnail-related-article.jpg') . '" /></a>';
            } // END if(has_post_thumbnail($obj_NextPost->ID))

            $htmlOutput .= '<div><em>' . \esc_html($nextPostObject->post_title) . '</em></div>';
            $htmlOutput .= '</div>';
        }

        $htmlOutput .= '</div>';
        $htmlOutput .= '</nav>';

        if($echo === true) {
            echo $htmlOutput;
        } else {
            return $htmlOutput;
        }
    }
}
