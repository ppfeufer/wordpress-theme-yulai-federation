<?php

namespace WordPress\Themes\YulaiFederation\Helper;

\defined('ABSPATH') or die();

class ThemeHelper {
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
	 * Return the current DB version used for the themes settings
	 *
	 * @return string
	 */
	public function getThemeDbVersion() {
		return '20170708';
	} // END public static function getThemeDbVersion()

	/**
	 * Returns the default theme options.
	 * If you change something here, do not forget to increase the DB Version Number
	 * in yf_get_current_db_version()
	 *
	 * @return array Default Theme Options
	 */
	public function getThemeDefaultOptions() {
		$defaultOptions = [
			// generel settings tab
			'type' => '',
			'name' => '',
			'show_corp_logos' => [
				'show' => 'show'
			],
			'navigation_even_cells' => [
				'yes' => ''
			],
			'show_post_meta' => [
				'yes' => ''
			],

			// background settings tab
			'use_background_image' => [
				'yes' => 'yes'
			],
			'background_image' => 'eve-citadel.jpg',
			'background_image_upload' => '',
			'background_color' => '',

			// slider settings tab
			'default_slider' => '',
			'default_slider_on' => [
				'frontpage_only' => 'frontpage_only'
			],

			// performance settings tab
			'minifyHtmlOutput' => [
				'yes' => ''
			],
			'cache' => [
				'remote-image-cache' => 'remote-image-cache'
			],
			'cron' => [
				'cleanupThemeImageCache' => '',
				'cleanupTransientCache' => ''
			],

			// footer settings tab
			'footertext' => '',
		];

		return \apply_filters('yulai_theme_options', $defaultOptions);
	} // END public static function getThemeDefaultOptions()

	/**
	 * Returning some theme related data
	 *
	 * @param string $parameter
	 * @return string
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_get_theme/
	 */
	public function getThemeData($parameter) {
		$themeData = \wp_get_theme();

		return $themeData->get($parameter);
	} // END function getThemeData($parameter)

	/**
	 * Return the theme's javascripts
	 *
	 * @return array
	 */
	public function getThemeJavaScripts() {
		$enqueue_script = [
			/* Html5Shiv */
			'Html5Shiv' => [
				'handle' => 'html5shiv',
				'condition' => [
					'conditionKey' => 'conditional',
					'conditionValue' => 'lt IE 9'
				],
				'source' => \get_theme_file_uri('/js/html5.min.js'),
				'deps' => '',
				'version' => '',
				'in_footer' => false
			],

			/* Respond JS */
			'Respond JS' => [
				'handle' => 'respondJS',
				'condition' => [
					'conditionKey' => 'conditional',
					'conditionValue' => 'lt IE 9'
				],
				'source' => \get_theme_file_uri('/js/respond.min.js'),
				'deps' => '',
				'version' => '',
				'in_footer' => false
			],

			/* Modernizr */
			'Modernizr' => [
				'handle' => 'modernizr',
				'source' => \get_theme_file_uri('/js/modernizr.min.js'),
				'source-development' => \get_theme_file_uri('/js/modernizr.js'),
				'deps' => '',
				'version' => '',
				'in_footer' => true
			],

			/* Bootstrap's JS */
			'Bootstrap' => [
				'handle' => 'bootstrap-js',
				'source' => \get_theme_file_uri('/bootstrap/js/bootstrap.min.js'),
				'source-development' => \get_theme_file_uri('/bootstrap/js/bootstrap.js'),
				'deps' => [
					'jquery'
				],
				'version' => '3.3.7',
				'in_footer' => true
			],

			/* Bootstrap Toolkit */
			'Bootstrap Toolkit' => [
				'handle' => 'bootstrap-toolkit-js',
				'source' => \get_theme_file_uri('/bootstrap/bootstrap-toolkit/bootstrap-toolkit.min.js'),
				'source-development' => \get_theme_file_uri('/bootstrap/bootstrap-toolkit/bootstrap-toolkit.js'),
				'deps' => [
					'bootstrap-js'
				],
				'version' => '2.6.3',
				'in_footer' => true
			],

			/* Bootstrap Gallery */
			'Bootstrap Gallery' => [
				'handle' => 'bootstrap-gallery-js',
				'source' => \get_theme_file_uri('/Plugins/js/jquery.bootstrap-gallery.min.js'),
				'source-development' => \get_theme_file_uri('/Plugins/js/jquery.bootstrap-gallery.js'),
				'deps' => [
					'jquery'
				],
				'version' => \sanitize_title($this->getThemeData('Name')) . '-' . $this->getThemeData('Version'),
				'in_footer' => true
			],

			/* The main JS */
			'EVE Online' => [
				'handle' => 'eve-online-main-js',
				'source' => \get_theme_file_uri('/js/yulai-federation.min.js'),
				'source-development' => \get_theme_file_uri('/js/yulai-federation.js'),
				'deps' => [
					'jquery'
				],
				'version' => \sanitize_title($this->getThemeData('Name')) . '-' . $this->getThemeData('Version'),
				'in_footer' => true
			]
		];

		return $enqueue_script;
	} // END public static function getThemeJavaScripts()

	/**
	 * Return the theme's stylesheets
	 *
	 * @return array
	 */
	public function getThemeStyleSheets() {
		$enqueue_style = [
			/* Normalize CSS */
			'Normalize CSS' => [
				'handle' => 'normalize',
				'source' => \get_theme_file_uri('/css/normalize.min.css'),
				'source-development' => \get_theme_file_uri('/css/normalize.css'),
				'deps' => [],
				'version' => '3.0.3',
				'media' => 'all'
			],

			/* Google Font */
			'Google Font' => [
				'handle' => 'google-font',
				'source' => '//fonts.googleapis.com/css?family=Amethysta',
				'deps' => [
					'normalize'
				],
				'version' => \sanitize_title($this->getThemeData('Name')) . '-' . $this->getThemeData('Version'),
				'media' => 'all'
			],

			/* Bootstrap */
			'Bootstrap' => [
				'handle' => 'bootstrap',
				'source' => \get_theme_file_uri('/bootstrap/css/bootstrap.min.css'),
				'source-development' => \get_theme_file_uri('/bootstrap/css/bootstrap.css'),
				'deps' => [
					'normalize'
				],
				'version' => '3.3.7',
				'media' => 'all'
			],

			/* Bootstrap Addition */
			'Bootstrap Addition' => [
				'handle' => 'bootstrap-addition',
				'source' => \get_theme_file_uri('/css/bootstrap-addition.min.css'),
				'source-development' => \get_theme_file_uri('/css/bootstrap-addition.css'),
				'deps' => [
					'bootstrap'
				],
				'version' => '3.3.7',
				'media' => 'all'
			],

			/* Yulai Federation Theme Main CSS */
			'Yulai Federation Theme Styles' => [
				'handle' => 'yulai-federation',
				'source' => \get_theme_file_uri('/style.min.css'),
				'source-development' => \get_theme_file_uri('/style.css'),
				'deps' => [
					'normalize',
					'google-font',
					'bootstrap'
				],
				'version' => \sanitize_title($this->getThemeData('Name')) . '-' . $this->getThemeData('Version'),
				'media' => 'all'
			],

			/* Yulai Federation Theme Responsive CSS */
			'Yulai Federation Responsive Styles' => [
				'handle' => 'yulai-federation-responsive-styles',
				'source' => \get_theme_file_uri('/css/responsive.min.css'),
				'source-development' => \get_theme_file_uri('/css/responsive.css'),
				'deps' => [
					'yulai-federation'
				],
				'version' => \sanitize_title($this->getThemeData('Name')) . '-' . $this->getThemeData('Version'),
				'media' => 'all'
			],

			/* Adjustment to Plugins */
			'Yulai Federation Plugin Styles' => [
				'handle' => 'yulai-federation-plugin-styles',
				'source' => \get_theme_file_uri('/css/plugin-tweaks.min.css'),
				'source-development' => \get_theme_file_uri('/css/plugin-tweaks.css'),
				'deps' => [
					'yulai-federation'
				],
				'version' => \sanitize_title($this->getThemeData('Name')) . '-' . $this->getThemeData('Version'),
				'media' => 'all'
			],
		];

		return $enqueue_style;
	} // END public static function getThemeStyleSheets()

	/**
	 * Return the theme's admin stylesheets
	 *
	 * @return array
	 */
	public function getThemeAdminStyleSheets() {
		$enqueue_style = [
			/* Adjustment to the backends */
			'Yulai Federation Admin Styles' => [
				'handle' => 'yulai-federation-admin-styles',
				'source' => \get_theme_file_uri('/Admin/css/yulai-federation-admin-style.min.css'),
				'source-development' => \get_theme_file_uri('/Admin/css/yulai-federation-admin-style.css'),
				'deps' => [],
				'version' => \sanitize_title($this->getThemeData('Name')) . '-' . $this->getThemeData('Version'),
				'media' => 'all'
			],
		];

		return $enqueue_style;
	} // END public static function getThemeAdminStyleSheets()

	/**
	 * Update the options array for our theme, if needed
	 *
	 * @param string $optionsName
	 * @param string $dbVersionFieldName
	 * @param string $newDbVersion
	 * @param array $defaultOptions
	 */
	public function updateOptions($optionsName, $dbVersionFieldName, $newDbVersion, $defaultOptions) {
		$currentDbVersion = \get_option($dbVersionFieldName);

		// Check if the DB needs to be updated
		if($currentDbVersion !== $newDbVersion) {
			$currentOptions = \get_option($optionsName);

			if(\is_array($currentOptions)) {
				$newOptions = \array_merge($defaultOptions, $currentOptions);
			} else {
				$newOptions = $defaultOptions;
			} // END if(\is_array($currentOptions))

			// Update the options
			\update_option($optionsName, $newOptions);

			// Update the DB Version
			\update_option($dbVersionFieldName, $newDbVersion);
		} // END if($currentDbVersion !== $newDbVersion)
	} // END function public static function updateOptions($optionsName, $dbVersionFieldName, $newDbVersion, $defaultOptions)

	/**
	 * Alias for is_active_sidebar()
	 *
	 * @param string $sidebarPosition
	 * @return boolean
	 * @uses is_active_sidebar() Whether a sidebar is in use.
	 */
	public function hasSidebar($sidebarPosition) {
		return \is_active_sidebar($sidebarPosition);
	} // END public static function hasSidebar($sidebarPosition)

	/**
	 * Getting the default background mages that are shipped with the theme
	 *
	 * @param boolean $withThumbnail
	 * @param string $baseClass
	 * @return array
	 */
	public function getDefaultBackgroundImages($withThumbnail = false, $baseClass = null) {
		$imagePath = \get_theme_file_path('/img/background/');
		$handle = \opendir($imagePath);

		if($baseClass !== null) {
			$baseClass = '-' . $baseClass;
		} // END if($baseClass !== null)

		if($handle) {
			while(false !== ($entry = \readdir($handle))) {
				$files[$entry] = $entry;
			} // END while(false !== ($entry = readdir($handle)))

			\closedir($handle);

			// we are only looking for images
			$images = \preg_grep('/\.(jpg|jpeg|png|gif)(?:[\?\#].*)?$/i', $files);

			if($withThumbnail === true) {
				foreach($images as &$image) {
					$imageName = \ucwords(\str_replace('-', ' ', \preg_replace("/\\.[^.\\s]{3,4}$/", "", $image)));
					$image = '<figure class="bg-image' . $baseClass . '"><img src="' . \get_theme_file_uri('/img/background/' . $image) . '" style="width:100px; height:auto;" title="' . $imageName . '"><figcaption>' . $imageName . '</figcaption></figure>';
				} // END foreach($images as &$image)
			} // END if($withThumbnail === true)

			return $images;
		} // END if($handle)
	} // END public static function getDefaultBackgroundImages($withThumbnail = false, $baseClass = null)

	/**
	 * Getting the themes background image
	 *
	 * @return string
	 */
	public function getThemeBackgroundImage() {
		$themeSettings = \get_option('yulai_theme_options', $this->getThemeDefaultOptions());

		$backgroundImage = (isset($themeSettings['background_image'])) ? \get_theme_file_uri('/img/background/' . $themeSettings['background_image']) : null;
		$uploadedBackground = (empty($themeSettings['background_image_upload'])) ? false : true;

		// we have an uploaded image, so overwrite the background
		if($uploadedBackground === true) {
			$backgroundImage = \wp_get_attachment_url($themeSettings['background_image_upload']);
		} // END if($uploadedBackground === true)

		return $backgroundImage;
	} // END public static function getThemeBackgroundImage()

	public function getThemeName() {
		return 'Yulai Federation';
	} // END public static function getThemeName()
} // END class ThemeHelper
