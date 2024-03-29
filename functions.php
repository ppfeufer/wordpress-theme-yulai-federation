<?php

/**
 * Our Theme's namespace to keep the global namespace clear
 *
 * WordPress\Themes\YulaiFederation
 */

namespace WordPress\Themes\YulaiFederation;

require_once(\trailingslashit(\dirname(__FILE__)) . 'inc/autoloader.php');

/**
 * Just to make sure, if this line is not in wp-config, that our environment
 * variable is still set right.
 *
 * This is to determine between "development/staging" and "live/production" environments.
 * If you are testing this theme in your own test environment, make sure you
 * set the following in your webservers vhosts config.
 * 		SetEnv APPLICATION_ENV "development"
 */
\defined('APPLICATION_ENV') || \define('APPLICATION_ENV', (\preg_match('/development/', \getenv('APPLICATION_ENV')) || \preg_match('/staging/', \getenv('APPLICATION_ENV'))) ? \getenv('APPLICATION_ENV') : 'production');

/**
 * WP Filesystem API
 */
require_once(\ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
require_once(\ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');

/**
 * Initiate needed general Classes
 */
$updateHelper = new Helper\UpdateHelper;
$cron = new Addons\Cron;
//$moCache = new Plugins\MoCache;
$metaSlider = new Plugins\Metaslider;
$shortcodes = new Plugins\Shortcodes;
$bootstrapImageGallery = new Plugins\BootstrapImageGallery;
$bootstrapVideoGallery = new Plugins\BootstrapVideoGallery;
$bootstrapContentGrid = new Plugins\BootstrapContentGrid;
$corppage = new Plugins\Corppage;
$whitelabel = new Plugins\Whitelabel;
$childpageMenu = new Plugins\ChildpageMenu;
$latestBlogPosts = new Plugins\LatestBlogPosts;
$eveOnlineAvatar = new Plugins\EveOnlineAvatar;

// Minify output if set in options
$themeOptions = \get_option('yulai_theme_options', Helper\ThemeHelper::getInstance()->getThemeDefaultOptions());

function yf_html_compression_finish($html) {
    return new Plugins\HtmlMinify($html);
}

function yf_html_compression_start() {
    \ob_start('\\WordPress\Themes\YulaiFederation\yf_html_compression_finish');
}


if(!empty($themeOptions['minify_html_output']['yes'])) {
    \add_action('get_header', '\\WordPress\Themes\YulaiFederation\yf_html_compression_start');
}

// initialize the classes that need to
$cron->init();
$metaSlider->init();

/**
 * Initiate needed Backend Classes
 */
if(\is_admin()) {
    $themeSettings = new Admin\ThemeSettings;
    $wordPressCoreUpdateCleaner = new Security\WordPressCoreUpdateCleaner;
}

/**
 * Maximal content width
 */
if(!isset($content_width)) {
    $content_width = 1680;
}

/**
 * Enqueue JavaScripts
 */
function yf_enqueue_scripts() {
    /**
     * Adds JavaScript to pages with the comment form to support
     * sites with threaded comments (when in use).
     */
    if(\is_singular() && \comments_open() && \get_option('thread_comments')) {
        \wp_enqueue_script('comment-reply');
    }

    $enqueue_script = yf_get_javascripts();

    /**
     * Loop through the JS array and load the scripts
     */
    foreach($enqueue_script as $script) {
        if(\preg_match('/development/', \APPLICATION_ENV)) {
            // for external scripts we might not have a development source
            if(!isset($script['source-development'])) {
                $script['source-development'] = $script['source'];
            }

            \wp_enqueue_script($script['handle'], $script['source-development'], $script['deps'], $script['version'], $script['in_footer']);
        } else {
            \wp_enqueue_script($script['handle'], $script['source'], $script['deps'], $script['version'], $script['in_footer']);
        }

        // conditional scripts
        if(!empty($script['condition'])) {
            \wp_script_add_data($script['handle'], $script['condition']['conditionKey'], $script['condition']['conditionValue']);
        }

        // translations
        if(!empty($script['l10n'])) {
            \wp_localize_script($script['handle'], $script['l10n']['handle'], $script['l10n']['translations']);
        }
    }
}
\add_action('wp_enqueue_scripts', '\\WordPress\Themes\YulaiFederation\yf_enqueue_scripts');

if(!function_exists('\WordPress\Themes\YulaiFederation\yf_get_javascripts')) {
    function yf_get_javascripts() {
        return Helper\ThemeHelper::getInstance()->getThemeJavaScripts();
    }
}

/**
 * Enqueue Styles
 */
function yf_enqueue_styles() {
    $enqueue_style = yf_get_stylesheets();

    /**
     * Loop through the CSS array and load the styles
     */
    foreach($enqueue_style as $style) {
        if(\preg_match('/development/', \APPLICATION_ENV)) {
            // for external styles we might not have a development source
            if(!isset($style['source-development'])) {
                $style['source-development'] = $style['source'];
            }

            \wp_enqueue_style($style['handle'], $style['source-development'], $style['deps'], $style['version'], $style['media']);
        } else {
            \wp_enqueue_style($style['handle'], $style['source'], $style['deps'], $style['version'], $style['media']);
        }

        // conditional styles
        if(!empty($style['condition'])) {
            \wp_style_add_data($style['handle'], $style['condition']['conditionKey'], $style['condition']['conditionValue']);
        }
    }
}
\add_action('wp_enqueue_scripts', '\\WordPress\Themes\YulaiFederation\yf_enqueue_styles');

if(!function_exists('\WordPress\Themes\YulaiFederation\yf_get_stylesheets')) {
    function yf_get_stylesheets() {
        return Helper\ThemeHelper::getInstance()->getThemeStyleSheets();
    }
}

if(!\function_exists('\WordPress\Themes\YulaiFederation\yf_enqueue_admin_styles')) {
    function yf_enqueue_admin_styles() {
        $enqueue_style = Helper\ThemeHelper::getInstance()->getThemeAdminStyleSheets();

        /**
         * Loop through the CSS array and load the styles
         */
        foreach($enqueue_style as $style) {
            if(\preg_match('/development/', \APPLICATION_ENV)) {
                // for external styles we might not have a development source
                if(!isset($style['source-development'])) {
                    $style['source-development'] = $style['source'];
                }

                \wp_enqueue_style($style['handle'], $style['source-development'], $style['deps'], $style['version'], $style['media']);
            } else {
                \wp_enqueue_style($style['handle'], $style['source'], $style['deps'], $style['version'], $style['media']);
            }

            // conditional styles
            if(!empty($style['condition'])) {
                \wp_style_add_data($style['handle'], $style['condition']['conditionKey'], $style['condition']['conditionValue']);
            }
        }
    }
}
\add_action('admin_init', '\\WordPress\Themes\YulaiFederation\yf_enqueue_admin_styles');

/**
 * Theme Setup
 */
function yf_theme_setup() {
    /**
     * Check if options need to be updated
     */
    Helper\ThemeHelper::getInstance()->updateOptions('yulai_theme_options', 'yulai_theme_db_version', Helper\ThemeHelper::getInstance()->getThemeDbVersion(), Helper\ThemeHelper::getInstance()->getThemeDefaultOptions());

    /**
     * Loading out textdomain
     */
    \load_theme_textdomain('yulai-federation', \get_template_directory() . '/l10n');

    \add_theme_support('automatic-feed-links');
    \add_theme_support('post-thumbnails');
    \add_theme_support('title-tag');
    \add_theme_support('post-formats', [
        'aside',
        'image',
        'gallery',
        'link',
        'quote',
        'status',
        'video',
        'audio',
        'chat'
    ]);

    /**
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    \add_theme_support('html5', [
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ]);

    \register_nav_menus([
        'main-menu' => __('Main Menu', 'yulai-federation'),
        'footer-menu' => __('Footer Menu', 'yulai-federation'),
        'header-menu' => __('Header Menu', 'yulai-federation'),
    ]);

    /**
     * Define post thumbnail size.
     * Add two additional image sizes.
     */
    \set_post_thumbnail_size(1680, 500);

    /**
     * Thumbnails used for the theme
     * Compatibilty with Fly Dynamic Image Resizer plugin
     */
    if(\function_exists('\fly_add_image_size')) {
        \fly_add_image_size('header-image', 1680, 500, true);
        \fly_add_image_size('post-loop-thumbnail', 768, 432, true);
    } else {
        \add_image_size('header-image', 1680, 500, true);
        \add_image_size('post-loop-thumbnail', 768, 432, true);
    }

    /**
     * This theme styles the visual editor to resemble the theme style,
     * specifically font, colors, icons, and column width.
     */
    \add_editor_style([
        'css/editor-style.css',
        'genericons/genericons.css',
        yf_fonts_url()
    ]);

    // Setting up the image cache directories
    Helper\CacheHelper::getInstance()->createCacheDirectory();
    Helper\CacheHelper::getInstance()->createCacheDirectory('images');
    Helper\CacheHelper::getInstance()->createCacheDirectory('images/corporation');
    Helper\CacheHelper::getInstance()->createCacheDirectory('images/alliance');
    Helper\CacheHelper::getInstance()->createCacheDirectory('images/character');
    Helper\CacheHelper::getInstance()->createCacheDirectory('images/render');
}
\add_action('after_setup_theme', '\\WordPress\Themes\YulaiFederation\yf_theme_setup');

if(!\function_exists('\WordPress\Themes\YulaiFederation\yf_title_separator')) {
    function yf_title_separator($separator) {
        $separator = '»';

        return $separator;
    }
}
\add_filter('document_title_separator', '\\WordPress\Themes\YulaiFederation\yf_title_separator');

/**
 * Remove integrated gallery styles in the content area of standard gallery shortcode.
 * style in css.
 */
function yf_gallery_style_filter($a) {
    return "<div class=\"gallery\">";
}
//\add_filter('gallery_style', \create_function('$a', 'return "<div class=\'gallery\'>";'));
\add_filter('gallery_style', '\\WordPress\Themes\YulaiFederation\yf_gallery_style_filter');

/**
 * Return the Google font stylesheet URL, if available.
 *
 * The use of Source Sans Pro and Bitter by default is localized. For languages
 * that use characters not supported by the font, the font can be disabled.
 *
 * @since YF Theme 1.0
 *
 * @return string Font stylesheet or empty string if disabled.
 */
if(!\function_exists('\WordPress\Themes\YulaiFederation\yf_fonts_url')) {
    function yf_fonts_url() {
        $fonts_url = '';

        /**
         * Translators: If there are characters in your language that are not
         * supported by Source Sans Pro, translate this to 'off'. Do not translate
         * into your own language.
         */
        $source_sans_pro = \_x('on', 'Source Sans Pro font: on or off', 'yulai-federation');

        /**
         * Translators: If there are characters in your language that are not
         * supported by Bitter, translate this to 'off'. Do not translate into your
         * own language.
         */
        $bitter = \_x('on', 'Bitter font: on or off', 'yulai-federation');

        if('off' !== $source_sans_pro || 'off' !== $bitter) {
            $font_families = [];

            if('off' !== $source_sans_pro) {
                $font_families[] = 'Source Sans Pro:300,400,700,300italic,400italic,700italic';

                if('off' !== $bitter) {
                    $font_families[] = 'Bitter:400,700';

                    $query_args = [
                        'family' => \urlencode(\implode('|', $font_families)),
                        'subset' => \urlencode('latin,latin-ext'),
                    ];
                    $fonts_url = \add_query_arg($query_args, 'https://fonts.googleapis.com/css');
                }
            }
        }

        return $fonts_url;
    }
}

/**
 * Adding the clearfix CSS class to every paragraph in .entry-content
 */
if(!\function_exists('\WordPress\Themes\YulaiFederation\yf_paragraph_clearfix')) {
    function yf_paragraph_clearfix($content) {
        return \preg_replace('/<p([^>]+)?>/', '<p$1 class="clearfix">', $content);
    }
}
//\add_filter('the_content', '\\WordPress\Themes\YulaiFederation\yf_paragraph_clearfix');

/**
 * Picking up teh first paragraph from the_content
 */
if(!\function_exists('\WordPress\Themes\YulaiFederation\yf_first_paragraph')) {
    function yf_first_paragraph($content) {
        return \preg_replace('/<p([^>]+)?>/', '<p$1 class="intro">', $content, 1);
    }
}
//\add_filter('the_content', '\\WordPress\Themes\YulaiFederation\yf_first_paragraph');

/**
 * Adding a CSS class to the excerpt
 * @param string $excerpt
 * @return string
 */
if(!\function_exists('\WordPress\Themes\YulaiFederation\yf_add_class_to_excerpt')) {
    function yf_add_class_to_excerpt($excerpt) {
        return \str_replace('<p', '<p class="excerpt"', $excerpt);
    }
}
\add_filter('the_excerpt', '\\WordPress\Themes\YulaiFederation\yf_add_class_to_excerpt');

/**
 * Define theme's widget areas.
 */
function yf_widgets_init() {
    \register_sidebar([
        'name' => \__('Page Sidebar', 'yulai-federation'),
        'description' => \__('This sidebar will be displayed if the current is a page or your blog index.', 'yulai-federation'),
        'id' => 'sidebar-page',
        'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
        'after_widget' => "</div></aside>",
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ]);

    \register_sidebar([
        'name' => \__('Post Sidebar', 'yulai-federation'),
        'description' => \__('This sidebar will always be displayed if teh current is a post / blog article.', 'yulai-federation'),
        'id' => 'sidebar-post',
        'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
        'after_widget' => "</div></aside>",
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ]);

    \register_sidebar([
        'name' => \__('General Sidebar', 'yulai-federation'),
        'id' => 'sidebar-general',
        'description' => \__('General sidebar that is always right from the topic, below the side specific sidebars', 'yulai-federation'),
        'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
        'after_widget' => "</div></aside>",
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ]);

    \register_sidebar([
        'name' => \__('Home Column 1', 'yulai-federation'),
        'id' => 'home-column-1',
        'description' => \__('Home Column 1', 'yulai-federation'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ]);

    \register_sidebar([
        'name' => \__('Home Column 2', 'yulai-federation'),
        'id' => 'home-column-2',
        'description' => \__('Home Column 2', 'yulai-federation'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ]);

    \register_sidebar([
        'name' => \__('Home Column 3', 'yulai-federation'),
        'id' => 'home-column-3',
        'description' => \__('Home Column 3', 'yulai-federation'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ]);

    \register_sidebar([
        'name' => \__('Home Column 4', 'yulai-federation'),
        'id' => 'home-column-4',
        'description' => \__('Home Column 4', 'yulai-federation'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ]);

    \register_sidebar([
        'name' => \__('Footer Column 1', 'yulai-federation'),
        'id' => 'footer-column-1',
        'description' => \__('Footer Column 1', 'yulai-federation'),
        'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></aside>',
        'before_title' => '<h4>',
        'after_title' => '</h4>'
    ]);

    \register_sidebar([
        'name' => \__('Footer Column 2', 'yulai-federation'),
        'id' => 'footer-column-2',
        'description' => \__('Footer Column 2', 'yulai-federation'),
        'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></aside>',
        'before_title' => '<h4>',
        'after_title' => '</h4>'
    ]);

    \register_sidebar([
        'name' => \__('Footer Column 3', 'yulai-federation'),
        'id' => 'footer-column-3',
        'description' => \__('Footer Column 3', 'yulai-federation'),
        'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></aside>',
        'before_title' => '<h4>',
        'after_title' => '</h4>'
    ]);

    \register_sidebar([
        'name' => \__('Footer Column 4', 'yulai-federation'),
        'id' => 'footer-column-4',
        'description' => \__('Footer Column 4', 'yulai-federation'),
        'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></aside>',
        'before_title' => '<h4>',
        'after_title' => '</h4>'
    ]);

    // header widget sidebar
    \register_sidebar([
        'name' => \__('Header Widget Area', 'yulai-federation'),
        'id' => 'header-widget-area',
        'description' => \__('Header Widget Area', 'yulai-federation'),
        'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></aside>',
        'before_title' => '<h4>',
        'after_title' => '</h4>'
    ]);
}
\add_action('init', '\\WordPress\Themes\YulaiFederation\yf_widgets_init');

/**
 * Replaces the excerpt "more" text by a link
 */
if(!function_exists('\WordPress\Themes\YulaiFederation\yf_excerpt_more')) {

    function yf_excerpt_more($more) {
        return ' ... <br/><a class="read-more" href="' . \get_permalink(\get_the_ID()) . '">' . __('Read More', 'yulai-federation') . '</a>';
    }
}
\add_filter('excerpt_more', '\\WordPress\Themes\YulaiFederation\yf_excerpt_more');

/**
 * prevent scrolling when using more-tag
 */
function yf_remove_more_link_scroll($link) {
    $link = \preg_replace('|#more-[0-9]+|', '', $link);

    return $link;
}
\add_filter('the_content_more_link', '\\WordPress\Themes\YulaiFederation\yf_remove_more_link_scroll');

/**
 * Adds custom classes to the array of body classes.
 */
function yf_body_classes($classes) {
    if(!\is_multi_author()) {
        $classes[] = 'single-author';
    }

    return $classes;
}
\add_filter('body_class', '\\WordPress\Themes\YulaiFederation\yf_body_classes');

/**
 * Add post ID attribute to image attachment pages prev/next navigation.
 */
function yf_enhanced_image_navigation($url) {
    global $post;

    if(\wp_attachment_is_image($post->ID)) {
        $url = $url . '#main';
    }

    return $url;
}
\add_filter('attachment_link', '\\WordPress\Themes\YulaiFederation\yf_enhanced_image_navigation');

/**
 * Define default page titles.
 */
function yf_wp_title($title, $sep) {
    global $paged, $page;

    if(\is_feed()) {
        return $title;
    }

    // Add the site name.
    $title .= \get_bloginfo('name');

    // Add the site description for the home/front page.
    $site_description = \get_bloginfo('description', 'display');

    if($site_description && (\is_home() || \is_front_page())) {
        $title = "$title $sep $site_description";
    }

    // Add a page number if necessary.
    if($paged >= 2 || $page >= 2) {
        $title = $title . ' ' . $sep . ' ' . \sprintf(\__('Page %s', 'yulai-federation'), \max($paged, $page));
    }

    return $title;
}
\add_filter('wp_title', '\\WordPress\Themes\YulaiFederation\yf_wp_title', 10, 2);

/**
 * Link Pages
 * @author toscho
 * @link http://wordpress.stackexchange.com/questions/14406/how-to-style-current-page-number-wp-link-pages
 * @param  array $args
 * @return void
 * Modification of wp_link_pages() with an extra element to highlight the current page.
 */
function yf_link_pages($args = []) {
    $defaults = [
        'before' => '<p>' . __('Pages:', 'yulai-federation'),
        'after' => '</p>',
        'before_link' => '',
        'after_link' => '',
        'current_before' => '',
        'current_after' => '',
        'link_before' => '',
        'link_after' => '',
        'pagelink' => '%',
        'echo' => 1
    ];

    $r = \wp_parse_args($args, $defaults);
    $r = \apply_filters('wp_link_pages_args', $r);

    \extract($r, \EXTR_SKIP);

    global $page, $numpages, $multipage, $more, $pagenow;

    if(!$multipage) {
        return;
    }

    $output = $before;

    for($i = 1; $i < ($numpages + 1); $i++) {
        $j = \str_replace('%', $i, $pagelink);
        $output .= ' ';

        if($i != $page || (!$more && 1 == $page)) {
            $output .= $before_link . \_wp_link_page($i) . $link_before . $j . $link_after . '</a>' . $after_link;
        } else {
            $output .= $current_before . $link_before . '<a>' . $j . '</a>' . $link_after . $current_after;
        }
    }

    echo $output . $after;
}

function yf_bootstrapDebug() {
    $script = '<script type="text/javascript">'
        . 'jQuery(function($) {'
        . '     (function($, document, window, viewport) {'
        . '         console.log(\'Current breakpoint:\', viewport.current());'
        . '         $("footer").append("<span class=\"viewport-debug\"><span class=\"viewport-debug-inner\"></span></span>");'
        . '         $(".viewport-debug-inner").html(viewport.current().toUpperCase());'
        . '         $(window).resize(viewport.changed(function() {'
        . '             console.log(\'Breakpoint changed to:\', viewport.current());'
        . '             $(".viewport-debug-inner").html(viewport.current().toUpperCase());'
        . '         }));'
        . '     })(jQuery, document, window, ResponsiveBootstrapToolkit);'
        . '});'
        . '</script>';

    echo $script;
}
if(\preg_match('/development/', \APPLICATION_ENV)) {
    \add_action('wp_footer', '\\WordPress\Themes\YulaiFederation\yf_bootstrapDebug', 99);
}

/**
 * Disable Smilies
 *
 * @todo Make it configurable
 */
\add_filter('option_use_smilies', '__return_false');

/**
 * Adding the custom style to the theme
 */
function yf_get_theme_custom_style() {
    $themeSettings = \get_option('yulai_theme_options', Helper\ThemeHelper::getInstance()->getThemeDefaultOptions());
    $themeCustomStyle = null;

    // background image
    $backgroundImage = Helper\ThemeHelper::getInstance()->getThemeBackgroundImage();

    if(!empty($backgroundImage) && (isset($themeSettings['use_background_image']['yes']) && $themeSettings['use_background_image']['yes'] === 'yes')) {
        $themeCustomStyle .= 'body {background-image: url("' . $backgroundImage . '")}' . "\n";
    }

    // main navigation
    if(isset($themeSettings['navigation_even_cells']['yes']) && $themeSettings['navigation_even_cells']['yes'] === 'yes') {
        $themeCustomStyle .= '@media all and (min-width: 768px) {' . "\n";
        $themeCustomStyle .= '  ul.main-navigation {display:table; width:100%;}' . "\n";
        $themeCustomStyle .= '  ul.main-navigation > li {display:table-cell; text-align:center; float:none;}' . "\n";
        $themeCustomStyle .= '}' . "\n";
    }

    \wp_add_inline_style('yulai-federation', $themeCustomStyle);
}
\add_action('wp_enqueue_scripts', '\\WordPress\Themes\YulaiFederation\yf_get_theme_custom_style');

/* comment form
 * -------------------------------------------------------------------------- */
function yf_comment_form_fields($fields) {
    $commenter = \wp_get_current_commenter();

    $req = \get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true' required" : '');
    $html5 = \current_theme_supports('html5', 'comment-form') ? 1 : 0;
    $consent = empty($commenter['comment_author_email']) ? '' : ' checked="checked"';

    $fields = [
        'author' => '<div class="row"><div class="form-group comment-form-author col-md-4">'
                    . '<input class="form-control" id="author" name="author" type="text" value="' . \esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' placeholder="' . \__('Name', 'yulai-federation') . ($req ? ' *' : '') . '" />'
                    . '</div>',
        'email' => '<div class="form-group comment-form-email col-md-4">'
                    . '<input class="form-control" id="email" name="email" ' . ($html5 ? 'type="email"' : 'type="text"') . ' value="' . \esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' placeholder="' . \__('Email', 'yulai-federation') . ($req ? ' *' : '') . '" />'
                    . '</div>',
        'url' => '<div class="form-group comment-form-url col-md-4">'
                    . '	<input class="form-control" id="url" name="url" ' . ($html5 ? 'type="url"' : 'type="text"') . ' value="' . \esc_attr($commenter['comment_author_url']) . '" size="30" placeholder="' . \__('Website', 'yulai-federation') . '" />'
                    . '</div></div>',
        // GPDR compliance
        'cookies' => '<div class="row"><div class="form-group checkbox comment-form-cookies-consent col-lg-12">'
                    . '<label for="wp-comment-cookies-consent">'
                    . '<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' />'
                    . \__('Save my name, email, and website in this browser for the next time I comment.', 'yulai-federation') . '</label>'
                    . '</div></div>',
    ];

    return $fields;
}
\add_filter('comment_form_default_fields', '\\WordPress\Themes\YulaiFederation\yf_comment_form_fields');

function yf_comment_form($args) {
    $args['comment_field'] = '<div class="row"><div class="form-group comment-form-comment col-lg-12">'
        . ' <textarea class="form-control" id="comment" name="comment" cols="45" rows="8" aria-required="true" required placeholder="' . \_x('Comment', 'noun', 'yulai-federation') . '"></textarea>'
        . '</div></div>';
    $args['class_submit'] = 'btn btn-default';

    return $args;
}
\add_filter('comment_form_defaults', '\\WordPress\Themes\YulaiFederation\yf_comment_form');

function yf_move_comment_field_to_bottom($fields) {
    $comment_field = $fields['comment'];
    $gpdr_field = $fields['cookies'];
    unset($fields['comment']);
    unset($fields['cookies']);

    $fields['comment'] = $comment_field;
    $fields['cookies'] = $gpdr_field;

    return $fields;
}
\add_filter('comment_form_fields', '\\WordPress\Themes\YulaiFederation\yf_move_comment_field_to_bottom');

/**
 * Getting the "on the fly" image URLs for Meta Slider
 *
 * @global object $fly_images
 * @param string $cropped_url
 * @param string $orig_url
 * @return string
 */
function yf_metaslider_fly_image_urls($cropped_url, $orig_url) {
    $attachmentImage = \fly_get_attachment_image_src(Helper\ImageHelper::getInstance()->getAttachmentId($orig_url), 'header-image');

    return str_replace('http://', '//', $attachmentImage['src']);
}
if(\function_exists('\fly_get_attachment_image')) {
    \add_filter('metaslider_resized_image_url', '\\WordPress\Themes\YulaiFederation\yf_metaslider_fly_image_urls', 10, 2);
}

/**
 * Adding some usefull parameters to the Youtube link when using oEmbed
 *
 * @param string $html
 * @param string $url
 * @param array $args
 * @return string
 */
function yf_enable_youtube_jsapi($html) {
    if(\strstr($html, 'youtube.com/embed/')) {
        $html = \str_replace('?feature=oembed', '?feature=oembed&enablejsapi=1&origin=' . \home_url() . '&rel=0', $html);
    }

    return $html;
}
\add_filter('oembed_result', '\\WordPress\Themes\YulaiFederation\yf_enable_youtube_jsapi');

/**
 * Removing the version string from any enqueued css and js source
 *
 * @param string $src the css or js source
 * @return string
 */
function yf_remove_wp_ver_css_js($src) {
    if(strpos($src, 'ver=')) {
        $src = \remove_query_arg('ver', $src);
    }

    return $src;
}
\add_filter('style_loader_src', '\\WordPress\Themes\YulaiFederation\yf_remove_wp_ver_css_js', 9999);
\add_filter('script_loader_src', '\\WordPress\Themes\YulaiFederation\yf_remove_wp_ver_css_js', 9999);
