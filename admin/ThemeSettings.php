<?php
/**
 * Theme Settings
 */

namespace WordPress\Themes\YulaiFederation\Admin;

use WordPress\Themes\YulaiFederation;

\defined('ABSPATH') or die();

class ThemeSettings {
	private $eveApi = null;
	private $metaSlider = null;
	private $themeOptions = null;

	private $settingsApi = null;
	private $settingsFilter = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->eveApi = new YulaiFederation\Helper\EveApiHelper;
		$this->metaSlider = new YulaiFederation\Plugins\Metaslider(false);
		$this->themeOptions = \get_option('yulai_theme_options', YulaiFederation\Helper\ThemeHelper::getThemeDefaultOptions());

		// trigger the settings API
		$this->fireSettingsApi();
	} // END public function __construct()

	/**
	 * Firing the Settings API
	 */
	private function fireSettingsApi() {
		$this->settingsFilter = 'register_yulai_federation_theme_settings';
		$this->settingsApi = new SettingsApi($this->settingsFilter, YulaiFederation\Helper\ThemeHelper::getThemeDefaultOptions());
		$this->settingsApi->init();

		\add_filter($this->settingsFilter, array($this, 'renderSettingsPage'));
	} // END private function fireSettingsApi()

	/**
	 * Render the settings page
	 *
	 * @return array
	 */
	public function renderSettingsPage() {
		$themeOptionsPage['yulai-federation-theme-settings'] = array(
			'type' => 'theme',
			'menu_title' => \__('Options', 'yulai-federation'),
			'page_title' => \__('Yulai Federation Theme Settings', 'yulai-federation'),
			'option_name' => 'yulai_theme_options',
			'tabs' => array(
				/**
				 * general settings tab
				 */
				'general-settings' => $this->getGeneralSettings(),

				/**
				 * background settings tab
				 */
				'background-settings' => $this->getBackgroundSettings(),

				/**
				 * slider settings tab
				 */
				'slider-settings' => $this->getSliderSettings(),

				/**
				 * performance settings tab
				 */
				'performance-settings' => $this->getPerformanceSettings()
			)
		);

		if($this->metaSlider->metasliderPluginExists() === false) {
			$themeOptionsPage['yulai-federation-theme-settings']['tabs']['slider-settings']['fields']['slider-warning']  = array(
				'title' => \__('Meta Slider Warning', 'yulai-federation'),
				'type' => 'custom',
				'content' => \sprintf(\__('Please make sure you have the %1$s plugin installed and activated.', 'yulai-federation'), '<a href="https://de.wordpress.org/plugins/ml-slider/" target="_blank">Meta Slider</a>'),
				'callback' => null
			);
		} // END if($this->metaSlider->metasliderPluginExists() === false)

		if(\preg_match('/development/', \APPLICATION_ENV)) {
			$themeOptionsPage['yulai-federation-theme-settings']['tabs']['development'] = $this->getDevelopmentSettings();
		} // END if(\preg_match('/development/', \APPLICATION_ENV))

		return $themeOptionsPage;
	} // END public function renderSettingsPage()

	/**
	 * general settings tab
	 *
	 * @return array
	 */
	private function getGeneralSettings() {
		return array(
			'tab_title' => \__('General Settings', 'yulai-federation'),
			'tab_description' => \__('General Theme Settings', 'yulai-federation'),
			'fields' => $this->getGeneralSettingsFields()
		);
	} // END private function getGeneralSettings()

	/**
	 * general settings tab fields
	 *
	 * @return array
	 */
	private function getGeneralSettingsFields() {
		return array(
			'type' => $this->getEntityTypeField(),
			'name' => $this->getEntityNameField(),
			'show_corp_logos' => $this->getShowCorpLogosField(),
			'navigation_even_cells' => $this->getEvenNavigationField(),
			'show_post_meta' => $this->getShowPostMetaField(),
		);
	} // END private function getGeneralSettingsFields()

	/**
	 * general setting "Entity Type"
	 * @return array
	 */
	private function getEntityTypeField() {
		return array(
			'title' => \__('Entity Type', 'yulai-federation'),
			'type' => 'select',
			'choices' => array(
				'alliance' => \__('Alliance', 'yulai-federation'),
				'corporation' => \__('Corporation', 'yulai-federation')
			),
			'empty' => \__('Please Select', 'yulai-federation'),
			'description' => 'Is it a Corporation or an Alliance?'
		);
	} // END private function getEntityTypeField()

	/**
	 * general setting "Entity Name"
	 * @return array
	 */
	private function getEntityNameField() {
		return array(
			'title' => \__('Entity Name', 'yulai-federation'),
			'type' => 'text',
			'description' => \sprintf(\__('The Name of your Corp/Alliance %1$s', 'yulai-federation'),
				(!empty($this->themeOptions['name'])) ? '</p></td></tr><tr><th>' . \__('Your Logo', 'yulai-federation') . '</th><td>' . $this->eveApi->getEntityLogoByName($this->themeOptions['name'], false) : ''
			)
		);
	} // END private function getEntityNameField()

	/**
	 * general setting "Show Corp Logos"
	 *
	 * @return array
	 */
	private function getShowCorpLogosField() {
		return array(
			'title' => \__('Corp Logos', 'yulai-federation'),
			'type' => 'checkbox',
			'choices' => array(
				'show' => \__('Show corp logos in menu for corp pages.', 'yulai-federation')
			),
			'description' => \__('Only available if you are running an alliance website, so you can have the corp logos in your "Our Corporations" menu.', 'yulai-federation')
		);
	} // END private function getShowCorpLogosField()

	/**
	 * general setting "Navigation"
	 *
	 * @return array
	 */
	private function getEvenNavigationField() {
		return array(
			'title' => \__('Navigation', 'yulai-federation'),
			'type' => 'checkbox',
			'choices' => array(
				'yes' => \__('Even navigation cells in main navigation', 'yulai-federation')
			),
			'description' => \__('Transforms the main navigation into even cells instead of random width cells. (only looks good with enough navigation items though ...)', 'yulai-federation')
		);
	} // END private function getEvenNavigationField()

	/**
	 * general setting "Show Post Meta"
	 *
	 * @return arrays
	 */
	private function getShowPostMetaField() {
		return array(
			'title' => \__('Post Meta', 'yulai-federation'),
			'type' => 'checkbox',
			'choices' => array(
				'yes' => \__('Show post meta (categories and all that stuff) in article loop and article view.', 'yulai-federation')
			),
			'description' => \__('If checked the post meta information, such as categories, publish time and author will be displayed in article loop and article view. (Default: on)', 'yulai-federation')
		);
	} // END private function getShowPostMetaField()

	/**
	 * background settings tab
	 *
	 * @return array
	 */
	private function getBackgroundSettings() {
		return array(
			'tab_title' => \__('Background Settings', 'yulai-federation'),
			'tab_description' => \__('Background Settings', 'yulai-federation'),
			'fields' => $this->getBackgroundSettingsFields()
		);
	} // END private function getBackgroundSettings()

	/**
	 * background settings tab fields
	 *
	 * @return array
	 */
	private function getBackgroundSettingsFields() {
		return array(
			'use_background_image' => $this->getUseBackgroundImageField(),
			'background_image' => $this->getThemeBackgroundImagesField(),
			'background_image_upload' => $this->getBackgroundImageUploadField()
		);
	} // END private function getBackgroundSettingsFields()

	/**
	 * background setting "Use Background Image"
	 *
	 * @return array
	 */
	private function getUseBackgroundImageField() {
		return array(
			'title' => \__('Use Background Image', 'yulai-federation'),
			'type' => 'checkbox',
			'choices' => array(
				'yes' => \__('Yes, I want to use background images on this website.', 'yulai-federation')
			),
			'description' => \__('If this option is checked, the website will use your selected (down below) background image instead of a simple colored background.', 'yulai-federation')
		);
	} // END private function getUseBackgroundImageField()

	/**
	 * background setting "Background Image
	 * @return array
	 */
	private function getThemeBackgroundImagesField() {
		return array(
			'title' => \__('Background Image', 'yulai-federation'),
			'type' => 'radio',
			'choices' => YulaiFederation\Helper\ThemeHelper::getDefaultBackgroundImages(true),
			'empty' => \__('Please Select', 'yulai-federation'),
			'description' => \__('Select one of the default Background images ...', 'yulai-federation'),
			'align' => 'horizontal'
		);
	} // END private function getThemeBackgroundImagesField()

	/**
	 * background setting "Image Upload"
	 *
	 * @return array
	 */
	private function getBackgroundImageUploadField() {
		return array(
			'title' => \__('', 'yulai-federation'),
			'type' => 'image',
			'description' => \__('... or upload your own', 'yulai-federation')
		);
	} // END private function getBackgroundImageUploadField()

	/**
	 * slider settings tab
	 *
	 * @return array
	 */
	private function getSliderSettings() {
		return array(
			'tab_title' => \__('Slider Settings', 'yulai-federation'),
			'tab_description' => \__('Slider Settings', 'yulai-federation'),
			'fields' => $this->getSliderSettingsFields()
		);
	} // END private function getSliderSettings()

	/**
	 * slider settings tab fields
	 *
	 * @return array
	 */
	private function getSliderSettingsFields() {
		return array(
			/**
			 * !!!
			 * Do NOT forget to change the options key in
			 * metaslider-plugin as well
			 * !!!
			 */
			'default_slider' => array(
				'title' => \__('Default Slider on Front Page', 'yulai-federation'),
				'type' => 'select',
				'choices' => $this->metaSlider->metasliderGetOptions(),
				'description' => ($this->metaSlider->metasliderPluginExists()) ? \__('Select the default slider for your front page', 'yulai-federation') : \sprintf(\__('Please make sure you have the %1$s plugin installed and activated.', 'yulai-federation'), '<a href="https://wordpress.org/plugins/ml-slider/">Meta Slider</a>')
			),
			'default_slider_on' => array(
				'title' => \__('Pages with Slider', 'yulai-federation'),
				'type' => 'checkbox',
				'choices' => array(
					'frontpage_only' => \__('Show only on front page.', 'yulai-federation')
				),
				'description' => \__('Show this slider only on front page in case no other slider is defined.', 'yulai-federation')
			),
		);
	} // END private function getSliderSettingsFields()

	/**
	 * performance settings tab
	 *
	 * @return array
	 */
	private function getPerformanceSettings() {
		return array(
			'tab_title' => \__('Performance Settings', 'yulai-federation'),
			'tab_description' => \__('Performance Settings', 'yulai-federation'),
			'fields' => $this->getPerformanceSettingsFields()
		);
	} // END private function getSliderSettings()

	/**
	 * performance tab fields
	 *
	 * @return array
	 */
	private function getPerformanceSettingsFields() {
		return array(
			'minify_html_output' => $this->getMinifyHtmlOutputField(),
			'cache' => $this->getCacheField(),
			'cron' => $this->getCronField(),
		);
	} // END private function getSliderSettingsFields()

	/**
	 * performance tab field "HTML Output"
	 *
	 * @return array
	 */
	private function getMinifyHtmlOutputField() {
		return array(
			'title' => \__('HTML Output', 'yulai-federation'),
			'type' => 'checkbox',
			'choices' => array(
				'yes' => \__('Minify HTML output?', 'yulai-federation')
			),
			'description' => \__('By minifying the HTML output you might boost your websites performance. NOTE: this may not work on every server, so if you experience issues, turn this option off again!', 'yulai-federation')
		);
	} // END private function getMinifyHtmlOutputField()

	/**
	 * performance tab field "Image Cache"
	 *
	 * @return array
	 */
	private function getCacheField() {
		return array(
			'title' => \__('Image Cache', 'yulai-federation'),
			'type' => 'checkbox',
			'choices' => array(
				'remote-image-cache' => \__('Use imagecache for images fetched from CCP\'s image server', 'yulai-federation')
			),
			'description' => \__('If checked the images from CCP\'s image server will be cached locally. (Default: on)', 'yulai-federation')
		);
	} // END private function getCacheField()

	/**
	 * performance tab field "Cron Jobs"
	 *
	 * @return array
	 */
	private function getCronField() {
		return array(
			'title' => \__('Cron Jobs', 'yulai-federation'),
			'type' => 'checkbox',
			'choices' => array(
				'cleanupThemeImageCache' => \__('Use a cronjob to clear the image cache once a day.', 'yulai-federation')
			),
			'description' => \__('If checked a WordPress cron will be initialized to clean up the image cache once a day. (Default: off)', 'yulai-federation')
		);
	} // END private function getCronField()

	/**
	 * development settings tab
	 *
	 * @return array
	 */
	private function getDevelopmentSettings() {
		return array(
			'tab_title' => \__('Development Infos', 'yulai-federation'),
			'tab_description' => \__('Delevopment Information', 'yulai-federation'),
			'fields' => $this->getDevelopmentSettingsFields()
		);
	} // END private function getDevelopmentSettings()

	/**
	 * development settings tab fields
	 *
	 * @return array
	 */
	private function getDevelopmentSettingsFields() {
		return array(
			'yf_theme_options_sane' => array(
				'title' => \__('Options Array<br>(sane from functions.php)', 'yulai-federation'),
				'type' => 'custom',
				'content' => '<pre>' . \print_r(YulaiFederation\Helper\ThemeHelper::getThemeDefaultOptions(), true) . '</pre>',
				'callback' => null,
				'description' => \__('This are the sane options defined in functions.php via <code>\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getThemeDefaultOptions()</code>', 'yulai-federation')
			),
			'yf_theme_options_from_db' => array(
				'title' => \__('Options Array<br>(from DB)', 'yulai-federation'),
				'type' => 'custom',
				'content' => '<pre>' . \print_r(\get_option('yulai_theme_options'), true) . '</pre>',
				'callback' => null,
				'description' => \__('This are the options from our database via <code>\get_option(\'yulai_theme_options\')</code>', 'yulai-federation')
			),
			'yf_theme_options_merged' => array(
				'title' => \__('Options Array<br>(merged / used for Theme)', 'yulai-federation'),
				'type' => 'custom',
				'content' => '<pre>' . \print_r(\get_option('yulai_theme_options', YulaiFederation\Helper\ThemeHelper::getThemeDefaultOptions()), true) . '</pre>',
				'callback' => null,
				'description' => \__('This are the options used for the theme via <code>\get_option(\'yulai_theme_options\', \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getThemeDefaultOptions())</code>', 'yulai-federation')
			)
		);
	} // END private function getDevelopmentSettingsFields()
} // END class ThemeSettings
