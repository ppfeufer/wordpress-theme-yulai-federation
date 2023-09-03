<?php

namespace WordPress\Themes\YulaiFederation\Helper;

\defined('ABSPATH') or die();

class PostHelper {
    /**
     * Instance
     *
     * static variable to keep the current (and only!) instance of this class
     *
     * @var ?PostHelper
     */
    protected static ?PostHelper $instance = null;

    public static function getInstance(): ?PostHelper {
        if(self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Clone
     *
     * no cloning allowed
     */
    protected function __clone() {
        ;
    }

    /**
     * Constructor
     *
     * no external instantiation allowed
     */
    protected function __construct() {
        ;
    }

    public function getPostMetaInformation() {
        $options = \get_option('yulai_theme_options', ThemeHelper::getInstance()->getThemeDefaultOptions());

        if(!empty($options['show_post_meta']['yes'])) {
            \printf(\__('Posted on <time class="entry-date" datetime="%3$s">%4$s</time><span class="byline"> <span class="sep"> by </span> <span class="author vcard">%7$s</span></span>', 'yulai-federation'), \esc_url(\get_permalink()), \esc_attr(\get_the_time()), \esc_attr(\get_the_date('c')), \esc_html(\get_the_date()), \esc_url(\get_author_posts_url(\get_the_author_meta('ID'))), \esc_attr(\sprintf(\__('View all posts by %s', 'yulai-federation'), \get_the_author()
                )), \esc_html(get_the_author())
            );
        }
    }

    /**
     * Display template for post-categories and tags
     */
    public function getPostCategoryAndTags() {
        $options = \get_option('yulai_theme_options', ThemeHelper::getInstance()->getThemeDefaultOptions());

        if(!empty($options['show_post_meta']['yes'])) {
            \printf('<span class="cats_tags"><span class="glyphicon glyphicon-folder-open" title="My tip"></span><span class="cats">');
            \printf(\the_category(', '));
            \printf('</span>');

            if(\has_tag() === true) {
                \printf('<span class="glyphicon glyphicon-tags"></span><span class="tags">');
                \printf(\the_tags(' '));
                \printf('</span>');
            } // END if(has_tag() === true)

            \printf('</span>');
        }
    }

    /**
     * Display template for comments and pingbacks.
     */
    public function getComments($comment, $args, $depth) {
        switch($comment->comment_type) {
            case 'pingback' :
            case 'trackback' :
                ?>
                <li class="comment media" id="comment-<?php \comment_ID(); ?>">
                    <div class="media-body">
                        <p>
                            <?php \_e('Pingback:', 'yulai-federation'); ?> <?php \comment_author_link(); ?>
                        </p>
                    </div><!--/.media-body -->
                <?php
                break;

            default :
                // Proceed with normal comments.
                global $post;
                ?>
                <li class="comment media" id="li-comment-<?php \comment_ID(); ?>">
                    <?php
                    if(!empty($comment->comment_author_url)) {
                        ?>
                        <a href="<?php echo $comment->comment_author_url; ?>" class="pull-left comment-avatar">
                            <?php echo \get_avatar($comment, 64); ?>
                        </a>
                        <?php
                    } else {
                        ?>
                        <span class="pull-left comment-avatar">
                            <?php echo \get_avatar($comment, 64); ?>
                        </span>
                        <?php
                    }
                    ?>
                    <div class="media-body">
                        <h4 class="media-heading comment-author vcard">
                            <?php
                            \printf('<cite class="fn">%1$s %2$s</cite>', \get_comment_author_link(),
                                // If current post author is also comment author, make it known visually.
                                ($comment->user_id === $post->post_author) ? '<span class="label"> ' . \__('Post author', 'yulai-federation') . '</span> ' : ''
                            );
                            ?>
                        </h4>
                        <?php
                        if('0' == $comment->comment_approved) {
                            ?>
                            <p class="comment-awaiting-moderation">
                                <?php \_e('Your comment is awaiting moderation.', 'yulai-federation'); ?>
                            </p>
                            <?php
                        } // END if('0' == $comment->comment_approved)

                        \comment_text();
                        ?>
                        <p class="meta">
                            <?php
                            \printf('<a href="%1$s"><time datetime="%2$s">%3$s</time></a>', \esc_url(\get_comment_link($comment->comment_ID)), \get_comment_time('c'), \sprintf(\__('%1$s at %2$s', 'yulai-federation'), \get_comment_date(), \get_comment_time()));
                            ?>
                        </p>
                        <p class="reply">
                            <?php
                            \comment_reply_link(\array_merge($args, [
                                'reply_text' => __('Reply <span>&darr;</span>', 'yulai-federation'),
                                'depth' => $depth,
                                'max_depth' => $args['max_depth']
                            ]));
                            ?>
                        </p>
                    </div> <!--/.media-body -->
                    <?php
                    break;
        }
    }

    public function getHeaderColClasses($echo = false) {
        if(ThemeHelper::getInstance()->hasSidebar('header-widget-area')) {
            $contentColClass = 'col-xs-12 col-sm-9 col-md-6 col-lg-6';
        } else {
            $contentColClass = 'col-xs-12 col-sm-9 col-md-9 col-lg-9';
        }

        if($echo === true) {
            echo $contentColClass;
        } else {
            return $contentColClass;
        }
    }

    public function getMainContentColClasses($echo = false) {
        if(\is_page() || \is_home()) {
            if(ThemeHelper::getInstance()->hasSidebar('sidebar-page') || ThemeHelper::getInstance()->hasSidebar('sidebar-general')) {
                $contentColClass = 'col-lg-9 col-md-9 col-sm-9 col-9';
            } else {
                $contentColClass = 'col-lg-12 col-md-12 col-sm-12 col-12';
            }
        } else {
            if(ThemeHelper::getInstance()->hasSidebar('sidebar-general') || ThemeHelper::getInstance()->hasSidebar('sidebar-post')) {
                $contentColClass = 'col-lg-9 col-md-9 col-sm-9 col-9';
            } else {
                $contentColClass = 'col-lg-12 col-md-12 col-sm-12 col-12';
            }
        }

        if($echo === true) {
            echo $contentColClass;
        } else {
            return $contentColClass;
        }
    }

    public function getLoopContentClasses($echo = false) {
        if(\is_page() || \is_home()) {
            if(ThemeHelper::getInstance()->hasSidebar('sidebar-page') || ThemeHelper::getInstance()->hasSidebar('sidebar-general')) {
                $contentColClass = 'col-lg-4 col-md-6 col-sm-12 col-xs-12';
            } else {
                $contentColClass = 'col-lg-3 col-md-4 col-sm-6 col-xs-12';
            }
        } else {
            if(ThemeHelper::getInstance()->hasSidebar('sidebar-general') || ThemeHelper::getInstance()->hasSidebar('sidebar-post')) {
                $contentColClass = 'col-lg-4 col-md-6 col-sm-12 col-xs-12';
            } else {
                $contentColClass = 'col-lg-3 col-md-4 col-sm-6 col-xs-12';
            }
        }

        if($echo === true) {
            echo $contentColClass;
        } else {
            return $contentColClass;
        }
    }

    public function getArticleNavigationPanelClasses($echo = false) {
        if(\is_page() || \is_home()) {
            if(ThemeHelper::getInstance()->hasSidebar('sidebar-page') || ThemeHelper::getInstance()->hasSidebar('sidebar-general')) {
                $contentColClass = 'col-lg-4 col-md-6 col-sm-6 col-xs-6';
            } else {
                $contentColClass = 'col-lg-3 col-md-4 col-sm-6 col-xs-6';
            }
        } else {
            if(ThemeHelper::getInstance()->hasSidebar('sidebar-general') || ThemeHelper::getInstance()->hasSidebar('sidebar-post')) {
                $contentColClass = 'col-lg-4 col-md-6 col-sm-6 col-xs-6';
            } else {
                $contentColClass = 'col-lg-3 col-md-4 col-sm-6 col-xs-6';
            }
        }

        if($echo === true) {
            echo $contentColClass;
        } else {
            return $contentColClass;
        }
    }

    public function getContentColumnCount($echo = false) {
        if(\is_page() || \is_home()) {
            if(ThemeHelper::getInstance()->hasSidebar('sidebar-page') || ThemeHelper::getInstance()->hasSidebar('sidebar-general')) {
                $columnCount = 3;
            } else {
                $columnCount = 4;
            }
        } else {
            if(ThemeHelper::getInstance()->hasSidebar('sidebar-general') || ThemeHelper::getInstance()->hasSidebar('sidebar-post')) {
                $columnCount = 3;
            } else {
                $columnCount = 4;
            }
        }

        if($echo === true) {
            echo $columnCount;
        } else {
            return $columnCount;
        }
    }

    /**
     * check if a post has content or not
     *
     * @param int $postID ID of the post
     * @return boolean
     */
    public function hasContent($postID) {
        $content_post = \get_post($postID);
        $content = $content_post->post_content;

        return \trim(\str_replace('&nbsp;', '', \strip_tags($content))) !== '';
    }

    public function getExcerptById($postID, $excerptLength = 35) {
        $the_post = \get_post($postID); //Gets post ID
        $the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
        $the_excerpt = \strip_tags(\strip_shortcodes($the_excerpt)); //Strips tags and images
        $words = \explode(' ', $the_excerpt, $excerptLength + 1);

        if(\count($words) > $excerptLength) {
            \array_pop($words);
            \array_push($words, '…');
            $the_excerpt = \implode(' ', $words);
        }

        $the_excerpt = '<p>' . $the_excerpt . '</p>';

        return $the_excerpt;
    }
}
