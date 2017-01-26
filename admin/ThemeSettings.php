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

	public function __construct() {
		$this->eveApi = new YulaiFederation\Helper\EveApiHelper;
		$this->metaSlider = new YulaiFederation\Plugins\Metaslider(false);
		$this->themeOptions = \get_option('yulai_theme_options', YulaiFederation\yf_get_options_default());

		// trigger the settings API
		$this->fireSettingsApi();
	} // END public function __construct()

	private function fireSettingsApi() {
		$this->settingsFilter = 'register_yulai_federation_theme_settings';
		$this->settingsApi = new SettingsApi($this->settingsFilter, YulaiFederation\yf_get_options_default());
		$this->settingsApi->init();

		\add_filter($this->settingsFilter, array($this, 'renderSettingsPage'));
	} // END private function fireSettingsApi()

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
				'slider-settings' => $this->getSliderSettings()
			)
		);

		if($this->metaSlider->metasliderPluginExists() === false) {
			$themeOptionsPage['yulai-federation-theme-settings']['tabs']['slider-settings']['fields']['slider-warning']  = array(
				'type' => 'custom',
				'title' => \__('Meta Slider Warning', 'yulai-federation'),
				'content' => \sprintf(\__('Please make sure you have the %1$s plugin installed and activated.', 'yulai-federation'), '<a href="https://de.wordpress.org/plugins/ml-slider/" target="_blank">Meta Slider</a>'),
				'callback' => null
			);
		} // END if($this->metaSlider->metasliderPluginExists() === false)

		if(\preg_match('/development/', \APPLICATION_ENV)) {
			$themeOptionsPage['yulai-federation-theme-settings']['tabs']['development'] = $this->getDevelopmentSettings();
		} // END if(\preg_match('/development/', \APPLICATION_ENV))

		return $themeOptionsPage;
	} // END public function renderSettingsPage()

	private function getGeneralSettings() {
		return array(
			'tab_title' => \__('General Settings', 'yulai-federation'),
			'tab_description' => \__('General Theme Settings', 'yulai-federation'),
			'fields' => $this->getGeneralSettingsFields()
		);
	} // END private function getGeneralSettings()

	private function getGeneralSettingsFields() {
		return array(
			'type' => array(
				'type' => 'select',
				'choices' => array(
					'alliance' => \__('Alliance', 'yulai-federation'),
					'corporation' => \__('Corporation', 'yulai-federation')
				),
				'empty' => \__('Please Select', 'yulai-federation'),
				'title' => \__('Entity Type', 'yulai-federation'),
				'description' => 'Is it a Corporation or an Alliance?'
			),
			'name' => array(
				'type' => 'text',
				'title' => \__('Entity Name', 'yulai-federation'),
				'description' => \sprintf(\__('The Name of your Corp/Alliance %1$s', 'yulai-federation'),
					(!empty($this->themeOptions['name'])) ? '</p></td></tr><tr><th>' . \__('Your Logo', 'yulai-federation') . '</th><td>' . $this->eveApi->getEntityLogoByName($this->themeOptions['name'], false) : ''
				)
			),
			'show_corp_logos' => array(
				'type' => 'checkbox',
				'title' => \__('Corp Logos', 'yulai-federation'),
				'choices' => array(
					'show' => \__('Show corp logos in menu for corp pages.', 'yulai-federation')
				),
				'description' => \__('Only available if you are running an alliance website, so you can have the corp logos in your "Our Corporations" menu.', 'yulai-federation')
			),
			'navigation_even_cells' => array(
				'type' => 'checkbox',
				'title' => \__('Navigation', 'yulai-federation'),
				'choices' => array(
					'yes' => \__('Even navigation cells in main navigation', 'yulai-federation')
				),
				'description' => \__('Transforms the main navigation into even cells instead of random width cells. (only looks good with enough navigation items though ...)', 'yulai-federation')
			),
		);
	} // END private function getGeneralSettingsFields()

	private function getBackgroundSettings() {
		return array(
			'tab_title' => \__('Background Settings', 'yulai-federation'),
			'tab_description' => \__('Background Settings', 'yulai-federation'),
			'fields' => $this->getBackgroundSettingsFields()
		);
	} // END private function getBackgroundSettings()

	private function getBackgroundSettingsFields() {
		return array(
			'use_background_image' => array(
				'type' => 'checkbox',
				'title' => \__('Use Background Image', 'yulai-federation'),
				'choices' => array(
					'yes' => \__('Yes, I want to use background images on this website.', 'yulai-federation')
				),
				'description' => \__('If this option is checked, the website will use your selected (down below) background image instead of a simple colored background.', 'yulai-federation')
			),
			'background_image' => array(
				'type' => 'radio',
				'choices' => YulaiFederation\yf_get_default_background_images(true),
				'empty' => \__('Please Select', 'yulai-federation'),
				'title' => \__('Background Image', 'yulai-federation'),
				'description' => \__('Select one of the default Background images ...', 'yulai-federation'),
				'align' => 'horizontal'
			),
			'background_image_upload' => array(
				'type' => 'image',
				'title' => \__('', 'yulai-federation'),
				'description' => \__('... or upload your own', 'yulai-federation')
			)
		);
	} // END private function getBackgroundSettingsFields()

	private function getSliderSettings() {
		return array(
			'tab_title' => \__('Slider Settings', 'yulai-federation'),
			'tab_description' => \__('Slider Settings', 'yulai-federation'),
			'fields' => $this->getSliderSettingsFields()
		);
	} // END private function getSliderSettings()

	private function getSliderSettingsFields() {
		return array(
			/**
			 * !!!
			 * Do NOT forget to change the options key in
			 * metaslider-plugin as well
			 * !!!
			 */
			'default_slider' => array(
				'type' => 'select',
				'title' => \__('Default Slider on Front Page', 'yulai-federation'),
				'choices' => $this->metaSlider->metasliderGetOptions(),
				'description' => ($this->metaSlider->metasliderPluginExists()) ? \__('Select the default slider for your front page', 'yulai-federation') : \sprintf(\__('Please make sure you have the %1$s plugin installed and activated.', 'yulai-federation'), '<a href="https://wordpress.org/plugins/ml-slider/">Meta Slider</a>')
			),
			'default_slider_on' => array(
				'type' => 'checkbox',
				'title' => \__('Pages with Slider', 'yulai-federation'),
				'choices' => array(
					'frontpage_only' => \__('Show only on front page.', 'yulai-federation')
				),
				'description' => \__('Show this slider only on front page in case no other slider is defined.', 'yulai-federation')
			),
		);
	} // END private function getSliderSettingsFields()

	private function getDevelopmentSettings() {
		return array(
			'tab_title' => \__('Development Infos', 'yulai-federation'),
			'tab_description' => \__('Delevopment Information', 'yulai-federation'),
			'fields' => $this->getDevelopmentSettingsFields()
		);
	} // END private function getDevelopmentSettings()

	private function getDevelopmentSettingsFields() {
		return array(
			'yf_theme_options_sane' => array(
				'type' => 'custom',
				'content' => '<pre>' . \print_r(YulaiFederation\yf_get_options_default(), true) . '</pre>',
				'title' => \__('Options Array<br>(sane from functions.php)', 'yulai-federation'),
				'callback' => null,
				'description' => \__('This are the sane options defined in functions.php via <code>YulaiFederation\yf_get_options_default()</code>', 'yulai-federation')
			),
			'yf_theme_options_from_db' => array(
				'type' => 'custom',
				'content' => '<pre>' . \print_r(\get_option('yulai_theme_options'), true) . '</pre>',
				'title' => \__('Options Array<br>(from DB)', 'yulai-federation'),
				'callback' => null,
				'description' => \__('This are the options from our database via <code>\get_option(\'yulai_theme_options\')</code>', 'yulai-federation')
			),
			'yf_theme_options_merged' => array(
				'type' => 'custom',
				'content' => '<pre>' . \print_r(\get_option('yulai_theme_options', YulaiFederation\yf_get_options_default()), true) . '</pre>',
				'title' => \__('Options Array<br>(merged / used for Theme)', 'yulai-federation'),
				'callback' => null,
				'description' => \__('This are the options used for the theme via <code>\get_option(\'yulai_theme_options\', \WordPress\Themes\YulaiFederation\yf_get_options_default())</code>', 'yulai-federation')
			)
		);
	} // END private function getDevelopmentSettingsFields()
} // END class ThemeSettings