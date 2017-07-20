<?php

namespace WordPress\Themes\YulaiFederation\Plugins;

use WordPress\Themes\YulaiFederation;

class ChildpageMenu {
	/**
	 * constructor
	 */
	public function __construct() {
//		require_once(\get_theme_file_path('/Plugins/Widgets/ChildpageMenuWidget.php'));

		$this->initPlugin();
	} // END public function __construct()

	/**
	 * initialze the plugin
	 */
	private function initPlugin() {
		// frontend actions
		if(!\is_admin()) {
//			$this->addStyle();
		} // END if(!\is_admin())

		$this->initWidget();
	} // END private function initPlugin()

	/**
	 * initialze the widget
	 */
	public function initWidget() {
		\add_action('widgets_init', \create_function('', 'return register_widget("WordPress\Themes\YulaiFederation\Plugins\Widgets\ChildpageMenuWidget");'));
	} // END public function initWidget()
} // END class ChildpageMenu
