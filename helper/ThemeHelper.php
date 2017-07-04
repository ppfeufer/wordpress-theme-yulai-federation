<?php

namespace WordPress\Themes\YulaiFederation\Helper;

\defined('ABSPATH') or die();

class ThemeHelper {
	/**
	 * Return the current DB version used for the themes settings
	 *
	 * @return string
	 */
	public static function getThemeDbVersion() {
		return '20170616';
	} // END public static function getThemeDbVersion()

	/**
	 * Returns the default theme options.
	 * If you change something here, do not forget to increase the DB Version Number
	 * in yf_get_current_db_version()
	 *
	 * @return array Default Theme Options
	 */
	public static function getThemeDefaultOptions() {
		$defaultOptions = array(
			// generel settings tab
			'type' => '',
			'name' => '',
			'show_corp_logos' => array(
				'show' => 'show'
			),
			'navigation_even_cells' => array(
				'yes' => ''
			),
			'show_post_meta' => array(
				'yes' => ''
			),

			// background settings tab
			'use_background_image' => array(
				'yes' => 'yes'
			),
			'background_image' => 'eve-citadel.jpg',
			'background_image_upload' => '',
			'background_color' => '',

			// slider settings tab
			'default_slider' => '',
			'default_slider_on' => array(
				'frontpage_only' => 'frontpage_only'
			),

			// performance settings tab
			'minifyHtmlOutput' => array(
				'yes' => ''
			),
			'cache' => array(
				'remote-image-cache' => 'remote-image-cache'
			),
			'cron' => array(
				'cleanupThemeImageCache' => ''
			),

			// footer settings tab
			'footertext' => '',
		);

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
	public static function getThemeData($parameter) {
		$themeData = \wp_get_theme();

		return $themeData->get($parameter);
	} // END function getThemeData($parameter)

	/**
	 * Return the theme's javascripts
	 *
	 * @return array
	 */
	public static function getThemeJavaScripts() {
		$enqueue_script = array(
			/* Html5Shiv */
			'Html5Shiv' => array(
				'handle' => 'html5shiv',
				'condition' => array(
					'conditionKey' => 'conditional',
					'conditionValue' => 'lt IE 9'
				),
				'source' => \get_theme_file_uri('/js/html5.min.js'),
				'deps' => '',
				'version' => '',
				'in_footer' => false
			),

			/* Respond JS */
			'Respond JS' => array(
				'handle' => 'respondJS',
				'condition' => array(
					'conditionKey' => 'conditional',
					'conditionValue' => 'lt IE 9'
				),
				'source' => \get_theme_file_uri('/js/respond.min.js'),
				'deps' => '',
				'version' => '',
				'in_footer' => false
			),

			/* Modernizr */
			'Modernizr' => array(
				'handle' => 'modernizr',
				'source' => \get_theme_file_uri('/js/modernizr.min.js'),
				'source-development' => \get_theme_file_uri('/js/modernizr.js'),
				'deps' => '',
				'version' => '',
				'in_footer' => true
			),

			/* Bootstrap's JS */
			'Bootstrap' => array(
				'handle' => 'bootstrap-js',
				'source' => \get_theme_file_uri('/bootstrap/js/bootstrap.min.js'),
				'source-development' => \get_theme_file_uri('/bootstrap/js/bootstrap.js'),
				'deps' => array(
					'jquery'
				),
				'version' => '3.3.7',
				'in_footer' => true
			),

			/* Bootstrap Toolkit */
			'Bootstrap Toolkit' => array(
				'handle' => 'bootstrap-toolkit-js',
				'source' => \get_theme_file_uri('/bootstrap/bootstrap-toolkit/bootstrap-toolkit.min.js'),
				'source-development' => \get_theme_file_uri('/bootstrap/bootstrap-toolkit/bootstrap-toolkit.js'),
				'deps' => array(
					'bootstrap-js'
				),
				'version' => '2.6.3',
				'in_footer' => true
			),

			/* Bootstrap Gallery */
			'Bootstrap Gallery' => array(
				'handle' => 'bootstrap-gallery-js',
				'source' => \get_theme_file_uri('/plugins/js/jquery.bootstrap-gallery.min.js'),
				'source-development' => \get_theme_file_uri('/plugins/js/jquery.bootstrap-gallery.js'),
				'deps' => array(
					'jquery'
				),
				'version' => \sanitize_title(self::getThemeData('Name')) . '-' . self::getThemeData('Version'),
				'in_footer' => true
			),

			/* The main JS */
			'EVE Online' => array(
				'handle' => 'eve-online-main-js',
				'source' => \get_theme_file_uri('/js/yulai-federation.min.js'),
				'source-development' => \get_theme_file_uri('/js/yulai-federation.js'),
				'deps' => array(
					'jquery'
				),
				'version' => \sanitize_title(self::getThemeData('Name')) . '-' . self::getThemeData('Version'),
				'in_footer' => true
			)
		);

		return $enqueue_script;
	} // END public static function getThemeJavaScripts()

	/**
	 * Return the theme's stylesheets
	 *
	 * @return array
	 */
	public static function getThemeStyleSheets() {
		$enqueue_style = array(
			/* Normalize CSS */
			'Normalize CSS' => array(
				'handle' => 'normalize',
				'source' => \get_theme_file_uri('/css/normalize.min.css'),
				'source-development' => \get_theme_file_uri('/css/normalize.css'),
				'deps' => array(),
				'version' => '3.0.3',
				'media' => 'all'
			),

			/* Google Font */
			'Google Font' => array(
				'handle' => 'google-font',
				'source' => '//fonts.googleapis.com/css?family=Amethysta',
				'deps' => array(
					'normalize'
				),
				'version' => \sanitize_title(self::getThemeData('Name')) . '-' . self::getThemeData('Version'),
				'media' => 'all'
			),

			/* Bootstrap */
			'Bootstrap' => array(
				'handle' => 'bootstrap',
				'source' => \get_theme_file_uri('/bootstrap/css/bootstrap.min.css'),
				'source-development' => \get_theme_file_uri('/bootstrap/css/bootstrap.css'),
				'deps' => array(
					'normalize'
				),
				'version' => '3.3.7',
				'media' => 'all'
			),

			/* Bootstrap Addition */
			'Bootstrap Addition' => array(
				'handle' => 'bootstrap-addition',
				'source' => \get_theme_file_uri('/css/bootstrap-addition.min.css'),
				'source-development' => \get_theme_file_uri('/css/bootstrap-addition.css'),
				'deps' => array(
					'bootstrap'
				),
				'version' => '3.3.7',
				'media' => 'all'
			),

			/* Yulai Federation Theme Main CSS */
			'Yulai Federation Theme Styles' => array(
				'handle' => 'yulai-federation',
				'source' => \get_theme_file_uri('/style.min.css'),
				'source-development' => \get_theme_file_uri('/style.css'),
				'deps' => array(
					'normalize',
					'google-font',
					'bootstrap'
				),
				'version' => \sanitize_title(self::getThemeData('Name')) . '-' . self::getThemeData('Version'),
				'media' => 'all'
			),

			/* Yulai Federation Theme Responsive CSS */
			'Yulai Federation Responsive Styles' => array(
				'handle' => 'yulai-federation-responsive-styles',
				'source' => \get_theme_file_uri('/css/responsive.min.css'),
				'source-development' => \get_theme_file_uri('/css/responsive.css'),
				'deps' => array(
					'yulai-federation'
				),
				'version' => \sanitize_title(self::getThemeData('Name')) . '-' . self::getThemeData('Version'),
				'media' => 'all'
			),

			/* Adjustment to Plugins */
			'Yulai Federation Plugin Styles' => array(
				'handle' => 'yulai-federation-plugin-styles',
				'source' => \get_theme_file_uri('/css/plugin-tweaks.min.css'),
				'source-development' => \get_theme_file_uri('/css/plugin-tweaks.css'),
				'deps' => array(
					'yulai-federation'
				),
				'version' => \sanitize_title(self::getThemeData('Name')) . '-' . self::getThemeData('Version'),
				'media' => 'all'
			),
		);

		return $enqueue_style;
	} // END public static function getThemeStyleSheets()

	/**
	 * Return the theme's admin stylesheets
	 *
	 * @return array
	 */
	public static function getThemeAdminStyleSheets() {
		$enqueue_style = array(
			/* Adjustment to the backends */
			'Yulai Federation Admin Styles' => array(
				'handle' => 'yulai-federation-admin-styles',
				'source' => \get_theme_file_uri('/admin/css/yulai-federation-admin-style.min.css'),
				'source-development' => \get_theme_file_uri('/admin/css/yulai-federation-admin-style.css'),
				'deps' => array(),
				'version' => \sanitize_title(self::getThemeData('Name')) . '-' . self::getThemeData('Version'),
				'media' => 'all'
			),
		);

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
	public static function updateOptions($optionsName, $dbVersionFieldName, $newDbVersion, $defaultOptions) {
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
	public static function hasSidebar($sidebarPosition) {
		return \is_active_sidebar($sidebarPosition);
	} // END public static function hasSidebar($sidebarPosition)

	/**
	 * Getting the default background mages that are shipped with the theme
	 *
	 * @param boolean $withThumbnail
	 * @param string $baseClass
	 * @return array
	 */
	public static function getDefaultBackgroundImages($withThumbnail = false, $baseClass = null) {
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
	public static function getThemeBackgroundImage() {
		$themeSettings = \get_option('yulai_theme_options', ThemeHelper::getThemeDefaultOptions());

		$backgroundImage = (isset($themeSettings['background_image'])) ? \get_theme_file_uri('/img/background/' . $themeSettings['background_image']) : null;
		$uploadedBackground = (empty($themeSettings['background_image_upload'])) ? false : true;

		// we have an uploaded image, so overwrite the background
		if($uploadedBackground === true) {
			$backgroundImage = \wp_get_attachment_url($themeSettings['background_image_upload']);
		} // END if($uploadedBackground === true)

		return $backgroundImage;
	} // END public static function getThemeBackgroundImage()

	public static function getThemeName() {
		return 'Yulai Federation';
	} // END public static function getThemeName()
} // END class ThemeHelper
