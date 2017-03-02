<?php
/**
 * EVE Killboard Theme Plugin for fetching Killmails from EDK killboard software
 */

namespace WordPress\Themes\YulaiFederation\Plugins;

use WordPress\Themes\YulaiFederation;

\defined('ABSPATH') or die();

class Killboard {
	private $themeSettings = null;
	private $pluginSettings = null;
//	private $kbDB = null;

	private $settingsApi = null;
	private $settingsFilter = null;

	/**
	 * constructor
	 */
	public function __construct($init = false) {
		if($init === true) {
			$this->initPlugin();
		} // END if($init === true)
	} // END public function __construct()

	private function initPlugin() {
		$this->themeSettings = \get_option('yulai_theme_options', YulaiFederation\Helper\ThemeHelper::getThemeDefaultOptions());
		$this->pluginSettings = \get_option('yulai_theme_killboard_plugin_options', $this->getDefaultPluginOptions());

		// backend actions
		if(\is_admin()) {
			$this->fireSettingsApi();
		} // END if(\is_admin())

		// frontend actions
		if(!\is_admin()) {
			$this->addStyle();
		} // END if(!\is_admin())

		// common actions
		$this->initWidget();
	} // END private function initPlugin()

	public function initWidget() {
		\add_action('widgets_init', \create_function('', 'return register_widget("WordPress\Themes\YulaiFederation\Plugins\Widgets\KillboardWidget");'));
	} // END public function initWidget()

	public function addStyle() {
		if(!\is_admin()) {
			\add_action('wp_enqueue_scripts', array($this, 'enqueueStyle'));
		} // END if(!\is_admin())
	} // END public function addStyle()

	public function enqueueStyle() {
		if(\preg_match('/development/', \APPLICATION_ENV)) {
			\wp_enqueue_style('yulai-federation-killboard', \get_template_directory_uri() . '/plugins/css/killboard-widget.css');
		} else {
			\wp_enqueue_style('yulai-federation-killboard', \get_template_directory_uri() . '/plugins/css/killboard-widget.min.css');
		} // END if(\preg_match('/development/', \APPLICATION_ENV))
	} // END public function enqueueStyle()

	public function getDefaultPluginOptions() {
		$defaultOptions = array(
			// generel settings tab
			'number_of_kills' => 5,
//			'show_losses' => array(
//				'yes' => 'yes'
//			),
			'killboard_db_host' => 'localhost',
			'killboard_db_name' => '',
			'killboard_db_user' => '',
			'killboard_db_password' => '',
			'killboard_domain' => ''
		);

		return \apply_filters('yulai-federation_theme_killboard_plugin_options', $defaultOptions);
	} // END private function getDefaultPluginOptions()

	/**
	 * Our Plugin settings
	 */
	private function fireSettingsApi() {
		$this->settingsFilter = 'register_yulai_federation_theme_killboard_plugin_settings';
		$this->settingsApi = new YulaiFederation\Admin\SettingsApi($this->settingsFilter, $this->getDefaultPluginOptions());
		$this->settingsApi->init();

		\add_filter($this->settingsFilter, array($this, 'renderSettingsPage'));
	} // END private function fireSettingsApi()

	public function renderSettingsPage() {
		$pluginOptionsPage['yulai-federation-theme-killboard-plugin-settings'] = array(
			'type' => 'plugin',
			'menu_title' => \__('Killboard Settings', 'yulai-federation'),
			'page_title' => \__('Killboard Settings', 'yulai-federation'),
			'option_name' => 'yulai_federation_theme_killboard_plugin_options',
			'tabs' => array(
				/**
				 * general settings
				 */
				'general-settings' => $this->getGeneralSettings(),

				/**
				 * database settings tab
				 */
				'database-settings' => $this->getDatabaseSettings()
			)
		);

		if(\preg_match('/development/', \APPLICATION_ENV)) {
			$pluginOptionsPage['yulai-federation-theme-killboard-plugin-settings']['tabs']['development'] = $this->getDevelopmentSettings();
		} // END if(\preg_match('/development/', \APPLICATION_ENV))

		return $pluginOptionsPage;
	} // END public function renderSettingsPage()

	private function getGeneralSettings() {
		return array(
			'tab_title' => \__('General Settings', 'yulai-federation'),
			'tab_description' => \__('Killboard General Settings', 'yulai-federation'),
			'fields' => $this->getGeneralTabFields()
		);
	} // END private function getGeneralSettings()s

	private function getGeneralTabFields() {
		return array(
			'number_of_kills' => array(
				'type' => 'text',
				'title' => \__('Number Of Kills', 'yulai-federation'),
				'description' => \__('Number of kills to show', 'yulai-federation'),
				'default' => 5
			),
//			'show_losses' => array(
//				'type' => 'checkbox',
//				'title' => \__('Show Losses', 'yulai-federation'),
//				'choices' => array(
//					'yes' => \__('Show your losses as well?', 'yulai-federation')
//				),
//				'default' => 'yes',
//				'description' => 'Only if you are tough enough :-P'
//			),
		);
	} // END private function getGeneralTabFields()

	private function getDatabaseSettings() {
		return array(
			'tab_title' => \__('Database Settings', 'yulai-federation'),
			'tab_description' => \__('Killboard Database Settings', 'yulai-federation'),
			'fields' => $this->getDatabaseTabFields()
		);
	} // END private function getDatabaseSettings()

	private function getDatabaseTabFields() {
		return array(
			'killboard_db_host' => array(
				'type' => 'text',
				'title' => \__('DB Host', 'yulai-federation'),
				'default' => 'localhost'
			),
			'killboard_db_name' => array(
				'type' => 'text',
				'title' => \__('DB Name', 'yulai-federation'),
			),
			'killboard_db_user' => array(
				'type' => 'text',
				'title' => \__('DB User', 'yulai-federation'),
			),
			'killboard_db_password' => array(
				'type' => 'password',
				'title' => \__('DB Password', 'yulai-federation'),
			)
		);
	} // END private function getDatabaseTabFields()

	private function getDevelopmentSettings() {
		return array(
			'tab_title' => \__('Development Infos', 'yulai-federation'),
			'tab_description' => \__('Delevopment Information', 'yulai-federation'),
			'fields' => $this->getDevelopmentTabFields()
		);
	}

	private function getDevelopmentTabFields() {
		return array(
			'plugin_options_sane' => array(
				'type' => 'custom',
				'content' => '<pre>' . \print_r($this->getDefaultPluginOptions(), true) . '</pre>',
				'title' => \__('Options Array<br>(sane from functions.php)', 'yulai-federation'),
				'callback' => null,
				'description' => \__('This are the sane options defined in plugin file via <code>$this->getDefaultPluginOptions()</code>', 'yulai-federation')
			),
			'plugin_options_from_db' => array(
				'type' => 'custom',
				'content' => '<pre>' . \print_r(\get_option('yulai_federation_theme_killboard_plugin_options'), true) . '</pre>',
				'title' => \__('Options Array<br>(from DB)', 'yulai-federation'),
				'callback' => null,
				'description' => \__('This are the options from our database via <code>\get_option(\'yulai_federation_theme_killboard_plugin_options\')</code>', 'yulai-federation')
			),
			'plugin_options_merged' => array(
				'type' => 'custom',
				'content' => '<pre>' . \print_r(\get_option('yulai_federation_theme_killboard_plugin_options', $this->getDefaultPluginOptions()), true) . '</pre>',
				'title' => \__('Options Array<br>(merged / used for Theme)', 'yulai-federation'),
				'callback' => null,
				'description' => \__('This are the options used for the theme via <code>\get_option(\'eve_theme_killboard_plugin_options\', $this->getDefaultPluginOptions())</code>', 'yulai-federation')
			)
		);
	} // END private function getDevelopmentTabFields()

	/**
	 * If the victim is not a pilot, we have to resort to this "hack"
	 *
	 * @return array
	 */
	public static function getStructureNames() {
		return array(
			// Citadels
			'Astrahus',
			'Fortizar',
			'Keepstar',

			// POS Tower
			'Amarr Control Tower',
			'Amarr Control Tower Small',
			'Amarr Control Tower Medium',
			'Caldari Control Tower',
			'Caldari Control Tower Small',
			'Caldari Control Tower Medium',
			'Gallente Control Tower',
			'Gallente Control Tower Small',
			'Gallente Control Tower Medium',
			'Minmatar Control Tower',
			'Minmatar Control Tower Small',
			'Minmatar Control Tower Medium',

			// POS Modules
			'Ion Field Projection Battery',
			'Jump Bridge',
			'Moon Harvesting Array',
			'Phase Inversion Battery',

			// Artillery
			'Small Artillery Battery',
			'Medium Artillery Battery',
			'Large Artillery Battery',

			// AutoCannon
			'Small AutoCannon Battery',
			'Domination Small AutoCannon Battery',
			'Medium AutoCannon Battery',
			'Large AutoCannon Battery',

			// Beam Laser
			'Small Beam Laser Battery',
			'Medium Beam Laser Battery',
			'Large Beam Laser Battery',

			// Pulse Laser
			'Small Pulse Laser Battery',
			'Medium Pulse Laser Battery',
			'Large Pulse Laser Battery',

			'Silo',
			'Spatial Destabilization Battery',
			'Stasis Webification Battery',
			'Warp Disruption Battery',
			'Warp Scrambling Battery',

			// Orbital Modules
			'Customs Office'
		);
	} // END public static function getStructureNames()
} // END class Killboard
