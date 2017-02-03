<?php
/**
 * Our Theme's namespace to keep the global namespace clear
 *
 * WordPress\Themes\YulaiFederation
 */
namespace WordPress\Themes\YulaiFederation;

/**
 * Just to make sure, if this line is not in wp-config, that our environment
 * variable is still set right.
 *
 * This is to determine between "development/staging" and "live/production" environments.
 * If you are testing this theme in your own test environment, make sure you
 * set the following in your webservers vhosts config.
 *		SetEnv APPLICATION_ENV "development"
 */
\defined('APPLICATION_ENV') || \define('APPLICATION_ENV', (\preg_match('/development/', \getenv('APPLICATION_ENV')) || \preg_match('/staging/', \getenv('APPLICATION_ENV'))) ? \getenv('APPLICATION_ENV') : 'production');

/**
 * Loading Helper Classes
 */
require_once(\get_template_directory() . '/helper/ThemeHelper.php');
require_once(\get_template_directory() . '/helper/NavigationHelper.php');
require_once(\get_template_directory() . '/helper/PostHelper.php');
require_once(\get_template_directory() . '/helper/EveApiHelper.php');
require_once(\get_template_directory() . '/helper/StringHelper.php');
require_once(\get_template_directory() . '/helper/ImageHelper.php');

/**
 * Loading Plugins
 */
require_once(\get_template_directory() . '/plugins/Metaslider.php');
require_once(\get_template_directory() . '/plugins/Shortcodes.php');
require_once(\get_template_directory() . '/plugins/BootstrapImageGallery.php');
require_once(\get_template_directory() . '/plugins/BootstrapVideoGallery.php');
require_once(\get_template_directory() . '/plugins/Corppage.php');
require_once(\get_template_directory() . '/plugins/Whitelabel.php');
require_once(\get_template_directory() . '/plugins/MoCache.php');
require_once(\get_template_directory() . '/plugins/EncodeEmailAddresses.php');
require_once(\get_template_directory() . '/plugins/helper/EdkKillboardHelper.php');
require_once(\get_template_directory() . '/plugins/helper/ZkbKillboardHelper.php');
require_once(\get_template_directory() . '/plugins/widgets/KillboardWidget.php');
require_once(\get_template_directory() . '/plugins/Killboard.php');
require_once(\get_template_directory() . '/plugins/LatestBlogPosts.php');
require_once(\get_template_directory() . '/plugins/HtmlMinify.php');

/**
 * Loading Security Classes
 */
require_once(\get_template_directory() . '/security/WordPressSecurity.php');
require_once(\get_template_directory() . '/security/WordPressCoreUpdateCleaner.php');

/**
 * Theme Options
 */
require_once(\get_template_directory() . '/admin/SettingsApi.php');
require_once(\get_template_directory() . '/admin/ThemeSettings.php');

/**
 * Initiate needed general Classes
 */
new Security\WordPressSecurity;
new Plugins\MoCache;
new Plugins\Metaslider(true);
new Plugins\Shortcodes;
new Plugins\BootstrapImageGallery;
new Plugins\BootstrapVideoGallery;
new Plugins\Corppage;
new Plugins\EncodeEmailAddresses;
new Plugins\Whitelabel;
new Plugins\Killboard(true);
new Plugins\LatestBlogPosts;

/**
 * Initiate needed Backend Classes
 */
if(\is_admin()) {
	new Admin\ThemeSettings;
	new Security\WordPressCoreUpdateCleaner;
} // END if(\is_admin())

/**
 * Maximal content width
 */
if(!isset($content_width)) {
	$content_width = 1680;
} // END if(!isset($content_width))

/**
 * Enqueue JavaScripts
 */
if(!\function_exists('yf_enqueue_scripts')) {
	function yf_enqueue_scripts() {
		/**
		 * Adds JavaScript to pages with the comment form to support
		 * sites with threaded comments (when in use).
		 */
		if(\is_singular() && \comments_open() && \get_option('thread_comments')) {
			\wp_enqueue_script('comment-reply');
		} // END if(\is_singular() && \comments_open() && \get_option('thread_comments'))

		$enqueue_script = Helper\ThemeHelper::getThemeJavaScripts();

		/**
		 * Loop through the JS array and load the scripts
		 */
		foreach($enqueue_script as $script) {
			if(\preg_match('/development/', \APPLICATION_ENV)) {
				// for external scripts we might not have a development source
				if(!isset($script['source-development'])) {
					$script['source-development'] = $script['source'];
				} // END if(!isset($script['source-development']))

				\wp_enqueue_script($script['handle'], $script['source-development'], $script['deps'], $script['version'], $script['in_footer']);
			} else {
				\wp_enqueue_script($script['handle'], $script['source'], $script['deps'], $script['version'], $script['in_footer']);
			} // END if(\preg_match('/development/', \APPLICATION_ENV))

			// conditional scripts
			if(!empty($script['condition'])) {
				\wp_script_add_data($script['handle'], $script['condition']['conditionKey'], $script['condition']['conditionValue']);
			} // END if(!empty($script['condition']))
		} // END foreach($enqueue_script as $script)
	} // END function yf_enqueue_styles()

	\add_action('wp_enqueue_scripts', '\\WordPress\Themes\YulaiFederation\yf_enqueue_scripts');
} // END if(!\function_exists('yf_enqueue_scripts'))

/**
 * Enqueue Styles
 */
if(!\function_exists('yf_enqueue_styles')) {
	function yf_enqueue_styles() {
		$enqueue_style = Helper\ThemeHelper::getThemeStyleSheets();

		/**
		 * Loop through the CSS array and load the styles
		 */
		foreach($enqueue_style as $style) {
			if(\preg_match('/development/', \APPLICATION_ENV)) {
				// for external styles we might not have a development source
				if(!isset($style['source-development'])) {
					$style['source-development'] = $style['source'];
				} // END if(!isset($style['source-development']))

				\wp_enqueue_style($style['handle'], $style['source-development'], $style['deps'], $style['version'], $style['media']);
			} else {
				\wp_enqueue_style($style['handle'], $style['source'], $style['deps'], $style['version'], $style['media']);
			} // END if(\preg_match('/development/', \APPLICATION_ENV))

			// conditional styles
			if(!empty($style['condition'])) {
				\wp_style_add_data($style['handle'], $style['condition']['conditionKey'], $style['condition']['conditionValue']);
			} // END if(!empty($script['condition']))
		} // END foreach($enqueue_style as $style)
	} // END function yf_enqueue_styles()

	\add_action('wp_enqueue_scripts', '\\WordPress\Themes\YulaiFederation\yf_enqueue_styles');
} // END if(!\function_exists('yf_enqueue_styles'))

if(!\function_exists('yf_enqueue_admin_styles')) {
	function yf_enqueue_admin_styles() {
		$enqueue_style = Helper\ThemeHelper::getThemeAdminStyleSheets();

		/**
		 * Loop through the CSS array and load the styles
		 */
		foreach($enqueue_style as $style) {
			if(\preg_match('/development/', \APPLICATION_ENV)) {
				// for external styles we might not have a development source
				if(!isset($style['source-development'])) {
					$style['source-development'] = $style['source'];
				} // END if(!isset($style['source-development']))

				\wp_enqueue_style($style['handle'], $style['source-development'], $style['deps'], $style['version'], $style['media']);
			} else {
				\wp_enqueue_style($style['handle'], $style['source'], $style['deps'], $style['version'], $style['media']);
			} // END if(\preg_match('/development/', \APPLICATION_ENV))

			// conditional styles
			if(!empty($style['condition'])) {
				\wp_style_add_data($style['handle'], $style['condition']['conditionKey'], $style['condition']['conditionValue']);
			} // END if(!empty($script['condition']))
		} // END foreach($enqueue_style as $style)
	} // END function yf_enqueue_admin_styles()

	\add_action('admin_init', '\\WordPress\Themes\YulaiFederation\yf_enqueue_admin_styles');
} // END if(!function_exists('\yf_enqueue_styles'))

/**
 * Theme Setup
 */
function yf_theme_setup() {
	/**
	 * Check if options need to be updated
	 */
	Helper\ThemeHelper::updateOptions('yulai_theme_options', 'yulai_theme_db_version', Helper\ThemeHelper::getThemeDbVersion(), Helper\ThemeHelper::getThemeDefaultOptions());

	/**
	 * Loading out textdomain
	 */
	\load_theme_textdomain('yulai-federation', \get_template_directory() . '/l10n');

	\add_theme_support('automatic-feed-links');
	\add_theme_support('post-thumbnails');
	\add_theme_support('post-formats', array(
		'aside',
		'image',
		'gallery',
		'link',
		'quote',
		'status',
		'video',
		'audio',
		'chat'
	));

	/**
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	\add_theme_support('html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	));

	\register_nav_menus(array(
		'main-menu' => __('Main Menu', 'yulai-federation'),
		'footer-menu' => __('Footer Menu', 'yulai-federation'),
		'header-menu' => __('Header Menu', 'yulai-federation'),
	));

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
		\fly_add_image_size('post-loop-thumbnail', 705, 395, true);
	} else {
		\add_image_size('header-image', 1680, 500, true);
		\add_image_size('post-loop-thumbnail', 705, 395, true);
	} // END if(\function_exists('\fly_add_image_size'))

	// Register Custom Navigation Walker
	require_once(\get_template_directory() .'/addons/BootstrapMenuWalker.php');

	/**
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	\add_editor_style(array(
		'css/editor-style.css',
		'genericons/genericons.css',
		yf_fonts_url()
	));
} // END function yf_theme_setup()
\add_action('after_setup_theme', '\\WordPress\Themes\YulaiFederation\yf_theme_setup');

/**
 * Remove integrated gallery styles in the content area of standard gallery shortcode.
 * style in css.
 */
\add_filter('gallery_style', \create_function('$a', 'return "<div class=\'gallery\'>";'));

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
if(!\function_exists('yf_fonts_url')) {
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
			$font_families = array();

			if('off' !== $source_sans_pro) {
				$font_families[] = 'Source Sans Pro:300,400,700,300italic,400italic,700italic';

				if('off' !== $bitter) {
					$font_families[] = 'Bitter:400,700';

					$query_args = array(
						'family' => \urlencode(\implode( '|', $font_families)),
						'subset' => \urlencode('latin,latin-ext'),
					);
					$fonts_url = \add_query_arg($query_args, 'https://fonts.googleapis.com/css');
				} // END if('off' !== $bitter)
			} // END if('off' !== $source_sans_pro)
		} // END if('off' !== $source_sans_pro || 'off' !== $bitter)

		return $fonts_url;
	} // END function yf_fonts_url()
} // END if(!\function_exists('yf_fonts_url'))

/**
 * Adding the clearfix CSS class to every paragraph in .entry-content
 */
if(!\function_exists('yf_paragraph_clearfix')) {
	function yf_paragraph_clearfix($content) {
		return \preg_replace('/<p([^>]+)?>/', '<p$1 class="clearfix">', $content);
	} // END function yf_paragraph_clearfix($content)

//	\add_filter('the_content', '\\WordPress\Themes\YulaiFederation\yf_paragraph_clearfix');
} // END if(!\function_exists('yf_paragraph_clearfix'))

/**
 * Picking up teh first paragraph from the_content
 */
if(!\function_exists('yf_first_paragraph')) {
	function yf_first_paragraph($content) {
		return \preg_replace('/<p([^>]+)?>/', '<p$1 class="intro">', $content, 1);
	} // END function yf_first_paragraph($content)

//	\add_filter('the_content', '\\WordPress\Themes\YulaiFederation\yf_first_paragraph');
} // END if(!\function_exists('yf_first_paragraph'))

/**
 * Adding a CSS class to the excerpt
 * @param string $excerpt
 * @return string
 */
if(!\function_exists('yf_add_class_to_excerpt')) {
	function yf_add_class_to_excerpt($excerpt) {
		return \str_replace('<p', '<p class="excerpt"', $excerpt);
	} // END function yf_add_class_to_excerpt($excerpt)

	\add_filter('the_excerpt', '\\WordPress\Themes\YulaiFederation\yf_add_class_to_excerpt');
} // END if(!\function_exists('yf_add_class_to_excerpt'))

/**
 * Define theme's widget areas.
 */
function yf_widgets_init() {
	\register_sidebar(
		array(
			'name' => \__('Page Sidebar', 'yulai-federation'),
			'id' => 'sidebar-page',
			'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
			'after_widget' => "</div></aside>",
			'before_title' => '<h4 class="widget-title">',
			'after_title' => '</h4>',
		)
	);

	\register_sidebar(
		array(
			'name' => \__('Post Sidebar', 'yulai-federation'),
			'id' => 'sidebar-post',
			'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
			'after_widget' => "</div></aside>",
			'before_title' => '<h4 class="widget-title">',
			'after_title' => '</h4>',
		)
	);

	\register_sidebar(
		array(
			'name' => \__('General Sidebar', 'yulai-federation'),
			'id' => 'sidebar-general',
			'description' => \__('General sidebar that is always right from the topic, below the side specific sidebars', 'yulai-federation'),
			'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
			'after_widget' => "</div></aside>",
			'before_title' => '<h4 class="widget-title">',
			'after_title' => '</h4>',
		)
	);

	\register_sidebar(
		array(
			'name' => \__('Home Column 1', 'yulai-federation'),
			'id' => 'home-column-1',
			'description' => \__('Home Column 1', 'yulai-federation'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2>',
			'after_title' => '</h2>'
		)
	);

	\register_sidebar(
		array(
			'name' => \__('Home Column 2', 'yulai-federation'),
			'id' => 'home-column-2',
			'description' => \__('Home Column 2', 'yulai-federation'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2>',
			'after_title' => '</h2>'
		)
	);

	\register_sidebar(
		array(
			'name' => \__('Home Column 3', 'yulai-federation'),
			'id' => 'home-column-3',
			'description' => \__('Home Column 3', 'yulai-federation'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2>',
			'after_title' => '</h2>'
		)
	);

	\register_sidebar(
		array(
			'name' => \__('Home Column 4', 'yulai-federation'),
			'id' => 'home-column-4',
			'description' => \__('Home Column 4', 'yulai-federation'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2>',
			'after_title' => '</h2>'
		)
	);

	\register_sidebar(
		array(
			'name' => \__('Footer Column 1', 'yulai-federation'),
			'id' => 'footer-column-1',
			'description' => \__('Footer Column 1', 'yulai-federation'),
			'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div></aside>',
			'before_title' => '<h4>',
			'after_title' => '</h4>'
		)
	);

	\register_sidebar(
		array(
			'name' => \__('Footer Column 2', 'yulai-federation'),
			'id' => 'footer-column-2',
			'description' => \__('Footer Column 2', 'yulai-federation'),
			'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div></aside>',
			'before_title' => '<h4>',
			'after_title' => '</h4>'
		)
	);

	\register_sidebar(
		array(
			'name' => \__('Footer Column 3', 'yulai-federation'),
			'id' => 'footer-column-3',
			'description' => \__('Footer Column 3', 'yulai-federation'),
			'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div></aside>',
			'before_title' => '<h4>',
			'after_title' => '</h4>'
		)
	);

	\register_sidebar(
		array(
			'name' => \__('Footer Column 4', 'yulai-federation'),
			'id' => 'footer-column-4',
			'description' => \__('Footer Column 4', 'yulai-federation'),
			'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div></aside>',
			'before_title' => '<h4>',
			'after_title' => '</h4>'
		)
	);

	// header widget sidebar
	\register_sidebar(
		array(
			'name' => \__('Header Widget Area', 'yulai-federation'),
			'id' => 'header-widget-area',
			'description' => \__('Header Widget Area', 'yulai-federation'),
			'before_widget' => '<aside><div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div></aside>',
			'before_title' => '<h4>',
			'after_title' => '</h4>'
		)
	);
} // END function yf_widgets_init()
\add_action('init', '\\WordPress\Themes\YulaiFederation\yf_widgets_init');

/**
 * Replaces the excerpt "more" text by a link
 */
function yf_excerpt_more($more) {
	return ' ... <br/><a class="read-more" href="'. \get_permalink(\get_the_ID()) . '">'.__('Read More', 'yulai-federation').'</a>';
} // END function yf_excerpt_more($more)
\add_filter('excerpt_more', '\\WordPress\Themes\YulaiFederation\yf_excerpt_more');

/**
 * prevent scrolling when using more-tag
 */
function yf_remove_more_link_scroll($link) {
	$link = \preg_replace('|#more-[0-9]+|', '', $link);

	return $link;
} // END function yf_remove_more_link_scroll($link)
\add_filter('the_content_more_link', '\\WordPress\Themes\YulaiFederation\yf_remove_more_link_scroll');

/**
 * Adds custom classes to the array of body classes.
 */
function yf_body_classes($classes) {
	if(!\is_multi_author()) {
		$classes[] = 'single-author';
	} // END if(!is_multi_author())

	return $classes;
} // END function yf_body_classes($classes)
\add_filter('body_class', '\\WordPress\Themes\YulaiFederation\yf_body_classes');

/**
 * Add post ID attribute to image attachment pages prev/next navigation.
 */
function yf_enhanced_image_navigation($url) {
	global $post;

	if(\wp_attachment_is_image($post->ID)) {
		$url = $url . '#main';
	} // END if(wp_attachment_is_image($post->ID))

	return $url;
} // END function yf_enhanced_image_navigation($url)
\add_filter('attachment_link', '\\WordPress\Themes\YulaiFederation\yf_enhanced_image_navigation');

/**
 * Define default page titles.
 */
function yf_wp_title($title, $sep) {
	global $paged, $page;

	if(\is_feed()) {
		return $title;
	} // END if(is_feed())

	// Add the site name.
	$title .= \get_bloginfo('name');

	// Add the site description for the home/front page.
	$site_description = \get_bloginfo('description', 'display');
	if($site_description && (\is_home() || \is_front_page())) {
		$title = "$title $sep $site_description";
	} // END if($site_description && (is_home() || is_front_page()))

	// Add a page number if necessary.
	if($paged >= 2 || $page >= 2) {
		$title = $title . ' ' . $sep  . ' ' . \sprintf(\__('Page %s', 'yulai-federation'), \max($paged, $page));
	} // END if($paged >= 2 || $page >= 2)

	return $title;
} // END function yf_wp_title($title, $sep)
\add_filter('wp_title', '\\WordPress\Themes\YulaiFederation\yf_wp_title', 10, 2);

/**
 * Link Pages
 * @author toscho
 * @link http://wordpress.stackexchange.com/questions/14406/how-to-style-current-page-number-wp-link-pages
 * @param  array $args
 * @return void
 * Modification of wp_link_pages() with an extra element to highlight the current page.
 */
function yf_link_pages($args = array()) {
	$defaults = array(
		'before' => '<p>' . __('Pages:'),
		'after' => '</p>',
		'before_link' => '',
		'after_link' => '',
		'current_before' => '',
		'current_after' => '',
		'link_before' => '',
		'link_after' => '',
		'pagelink' => '%',
		'echo' => 1
	);

	$r = \wp_parse_args($args, $defaults);
	$r = \apply_filters('wp_link_pages_args', $r);

	\extract($r, \EXTR_SKIP);

	global $page, $numpages, $multipage, $more, $pagenow;

	if(!$multipage) {
		return;
	} // END if(!$multipage)

	$output = $before;

	for($i = 1; $i < ($numpages + 1); $i++)	{
		$j = \str_replace( '%', $i, $pagelink );
		$output .= ' ';

		if($i != $page || (!$more && 1 == $page)) {
			$output .= $before_link . \_wp_link_page($i) . $link_before . $j . $link_after . '</a>' . $after_link;
		} else {
			$output .= $current_before . $link_before .'<a>' . $j . '</a>' . $link_after . $current_after;
		} // END if($i != $page || (!$more && 1 == $page))
	} // END for($i = 1; $i < ($numpages + 1); $i++)

	echo $output . $after;
} // END function yf_link_pages($args = array())

function yf_bootstrapDebug() {
	$script = '<script type="text/javascript">'
			. 'jQuery(function($) {'
			. '		(function($, document, window, viewport) {'
			. '			console.log(\'Current breakpoint:\', viewport.current());'
			. '			$("footer").append("<span class=\"viewport-debug\"><span class=\"viewport-debug-inner\"></span></span>");'
			. '			$(".viewport-debug-inner").html(viewport.current().toUpperCase());'
			. '			$(window).resize(viewport.changed(function() {'
			. '				console.log(\'Breakpoint changed to:\', viewport.current());'
			. '				$(".viewport-debug-inner").html(viewport.current().toUpperCase());'
			. '			}));'
			. '		})(jQuery, document, window, ResponsiveBootstrapToolkit);'
			. '});'
			. '</script>';

	echo $script;
} // END function yf_bootstrapDebug()
if(\preg_match('/development/', \APPLICATION_ENV)) {
	\add_action('wp_footer', '\\WordPress\Themes\YulaiFederation\yf_bootstrapDebug', 99);
} // END if(\preg_match('/development/', \APPLICATION_ENV))

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
	$themeSettings = \get_option('yulai_theme_options', Helper\ThemeHelper::getThemeDefaultOptions());
	$themeCustomStyle = null;

	// background image
	$backgroundImage = Helper\ThemeHelper::getThemeBackgroundImage();

	if(!empty($backgroundImage) && (isset($themeSettings['use_background_image']['yes']) && $themeSettings['use_background_image']['yes'] === 'yes')) {
		$themeCustomStyle .= 'body {background-image: url("' . $backgroundImage . '")}' . "\n";
	} // END if(!empty($backgroundImage) && (isset($themeSettings['use_background_image']['yes']) && $themeSettings['use_background_image']['yes'] === 'yes'))

	// main navigation
	if(isset($themeSettings['navigation_even_cells']['yes']) && $themeSettings['navigation_even_cells']['yes'] === 'yes') {
		$themeCustomStyle .= '@media all and (min-width: 768px) {' . "\n";
		$themeCustomStyle .= '	ul.main-navigation {display:table; width:100%;}' . "\n";
		$themeCustomStyle .= '	ul.main-navigation > li {display:table-cell; text-align:center; float:none;}' . "\n";
		$themeCustomStyle .= '}' . "\n";
	} // END if(isset($themeSettings['navigation_even_cells']['yes']) && $themeSettings['navigation_even_cells']['yes'] === 'yes')

	\wp_add_inline_style('yulai-federation', $themeCustomStyle);
} // END function yf_get_theme_custom_style()
\add_action('wp_enqueue_scripts', '\\WordPress\Themes\YulaiFederation\yf_get_theme_custom_style');

/* comment form
 * -------------------------------------------------------------------------- */
function yf_comment_form_fields($fields) {
	$commenter = \wp_get_current_commenter();

	$req = \get_option('require_name_email');
	$aria_req = ($req ? " aria-required='true' required" : '');
	$html5 = \current_theme_supports('html5', 'comment-form') ? 1 : 0;

	$fields =  array(
		'author' => '<div class="row"><div class="form-group comment-form-author col-md-4">'
					. '	<input class="form-control" id="author" name="author" type="text" value="' . \esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' placeholder="' . \__('Name') . ($req ? ' *' : '') . '" />'
					. '</div>',
		'email' => '<div class="form-group comment-form-email col-md-4">'
					. '	<input class="form-control" id="email" name="email" ' . ($html5 ? 'type="email"' : 'type="text"') . ' value="' . \esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' placeholder="' . \__('Email') . ($req ? ' *' : '') . '" />'
					. '</div>',
		'url' => '<div class="form-group comment-form-url col-md-4">'
					. '	<input class="form-control" id="url" name="url" ' . ($html5 ? 'type="url"' : 'type="text"') . ' value="' . \esc_attr($commenter['comment_author_url']) . '" size="30" placeholder="' . \__('Website') . '" />'
					. '</div></div>'
	);

	return $fields;
} // END function yf_comment_form_fields($fields)
\add_filter('comment_form_default_fields', '\\WordPress\Themes\YulaiFederation\yf_comment_form_fields');

function yf_comment_form($args) {
	$args['comment_field'] = '<div class="row"><div class="form-group comment-form-comment col-lg-12">'
							. '	<textarea class="form-control" id="comment" name="comment" cols="45" rows="8" aria-required="true" required placeholder="' . \_x('Comment', 'noun') . '"></textarea>'
							. '</div></div>';
	$args['class_submit'] = 'btn btn-default';

	return $args;
} // END function yf_comment_form($args)
\add_filter('comment_form_defaults', '\\WordPress\Themes\YulaiFederation\yf_comment_form');

function yf_move_comment_field_to_bottom($fields) {
	$comment_field = $fields['comment'];
	unset($fields['comment']);

	$fields['comment'] = $comment_field;
	return $fields;
} // END function yf_move_comment_field_to_bottom($fields)
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
	$attachmentImage = \fly_get_attachment_image_src(Helper\ImageHelper::getAttachmentId($orig_url), 'header-image');

	return str_replace('http://', '//', $attachmentImage['src']);
} // END function yf_metaslider_fly_image_urls($cropped_url, $orig_url)
if(\function_exists('\fly_get_attachment_image')) {
	\add_filter('metaslider_resized_image_url', '\\WordPress\Themes\YulaiFederation\yf_metaslider_fly_image_urls', 10, 2);
} // END if(\function_exists('\fly_get_attachment_image'))

/**
 * Adding some usefull parameters to the Youtube link when using oEmbed
 *
 * @param string $html
 * @param string $url
 * @param array $args
 * @return string
 */
function yf_enable_youtube_jsapi($html, $url, $args) {
	if(\strstr($html, 'youtube.com/embed/')) {
		$html = \str_replace('?feature=oembed', '?feature=oembed&enablejsapi=1&origin=' . \get_bloginfo('url') . '&rel=0', $html);
	} // END if(\strstr($html, 'youtube.com/embed/'))

	return $html;
} // END function yf_enable_youtube_jsapi($html, $url, $args)
\add_filter('oembed_result', '\\WordPress\Themes\YulaiFederation\yf_enable_youtube_jsapi');