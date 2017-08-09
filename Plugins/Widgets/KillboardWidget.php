<?php
/**
 * Killboard Widget
 */

namespace WordPress\Themes\YulaiFederation\Plugins\Widgets;

use WordPress\Themes\YulaiFederation;

class KillboardWidget extends \WP_Widget {
	/**
	 * Root ID for all widgets of this type.
	 *
	 * @since 2.8.0
	 * @access public
	 * @var mixed|string
	 */
	public $id_base;

	/**
	 * Name for this widget type.
	 *
	 * @since 2.8.0
	 * @access public
	 * @var string
	 */
	public $name;

	private $kbDB = null;

	private $themeSettings = null;
	private $plugin = null;
	private $pluginHelper = null;
	private $pluginSettings = null;
	private $entityID = null;
	private $eveApi = null;

//	private $eveApi = null;

	public function __construct() {
		$this->plugin = new YulaiFederation\Plugins\Killboard;
		$this->pluginHelper = new YulaiFederation\Plugins\Helper\EdkKillboardHelper;
		$this->eveApi = new \WordPress\Themes\YulaiFederation\Helper\EveApiHelper;

		$this->pluginSettings = \get_option('yulai_federation_theme_killboard_plugin_options', $this->plugin->getDefaultPluginOptions());
		$this->themeSettings = \get_option('yulai_theme_options', \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions());
		$this->entityID = $this->eveApi->getEveIdFromName($this->themeSettings['name']);

		$this->kbDB = $this->pluginHelper->db;

		$widget_options = array(
			'classname' => 'yulai-federation-killboard-widget',
			'description' => \__('Displaying the latest kills (and maybe losses if you are tough enough) in your sidebar.', 'yulai-federation')
		);

		$control_options = array();

		parent::__construct('yulai_federation_killboard_widget', __('EVE Killboard Widget', 'yulai-federation'), $widget_options, $control_options);
	} // END public function __construct($id_base, $name, $widget_options = array(), $control_options = array())

	/**
	 * The widgets settings form
	 *
	 * @param type $instance
	 */
	public function form($instance) {
		/**
		 * Standardwerte
		 *
		 * @var array
		 */
		$instance = \wp_parse_args((array) $instance, array(
			'yulai-federation-killboard-widget-title' => '',
			'yulai-federation-killboard-widget-number-of-kills' => (!empty($this->pluginSettings['number_of_kills'])) ? $this->pluginSettings['number_of_kills'] : 5,
			'yulai-federation-killboard-widget-show-losses' => ($this->pluginSettings['show_losses']['yes']) ? true : false
		));

		$showLosses = $instance['yulai-federation-killboard-widget-show-losses'] ? 'checked="checked"' : '';

		// Database Warning
		if((isset($this->pluginSettings['killmail_source']) && $this->pluginSettings['killmail_source'] === 'local') && !$this->kbDB) {
			echo '<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . \__('Database Warning / Not Configured', 'yulai-federation') . '</strong></p>';
			echo '<p>' . sprintf(\__('Please make sure you have your Killboard Database configured in your %1$s.', 'yulai-federation'), '<a href="' . admin_url('options-general.php?page=yulai-federation-theme-killboard-plugin-settings') . '">Plugin Settings</a>') . '</p>';
			echo '<p style="clear:both;"></p>';
		} else {
			// Titel
			echo '<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . \__('Title', 'yulai-federation') . '</strong></p>';
			echo '<p><input id="' . $this->get_field_id('yulai-federation-killboard-widget-title') . '" name="' . $this->get_field_name('yulai-federation-killboard-widget-title') . '" type="text" value="' . $instance['yulai-federation-killboard-widget-title'] . '"></p>';
			echo '<p style="clear:both;"></p>';

			// Number of kills
			echo '<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . \__('Number of kills to show', 'yulai-federation') . '</strong></p>';
			echo '<p><input id="' . $this->get_field_id('yulai-federation-killboard-widget-number-of-kills') . '" name="' . $this->get_field_name('yulai-federation-killboard-widget-number-of-kills') . '" type="text" value="' . $instance['yulai-federation-killboard-widget-number-of-kills'] . '"></p>';
			echo '<p style="clear:both;"></p>';

			// Show losses (not yet implemented)
			if(isset($this->pluginSettings['killmail_source']) && $this->pluginSettings['killmail_source'] === 'zkillboard') {
				echo '<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . \__('Losses', 'yulai-federation') . '</strong></p>';
				echo '<p><label><input class="checkbox" type="checkbox" ' . $showLosses . ' id="' . $this->get_field_id('yulai-federation-killboard-widget-show-losses') . '" name="' . $this->get_field_name('yulai-federation-killboard-widget-show-losses') . '"> <span>' . \__('Show losses as well?', 'yulai-federation') . '</span></label></p>';
				echo '<p style="clear:both;"></p>';
			}
		} // END if(!$this->kbDB)
	} // END public function form($instance)

	/**
	 * Update Widget Setting
	 *
	 * @param type $new_instance
	 * @param type $old_instance
	 */
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/**
		 * Standrdwerte setzen
		 *
		 * @var array
		 */
		$new_instance = \wp_parse_args((array) $new_instance, array(
			'yulai-federation-killboard-widget-title' => '',
			'yulai-federation-killboard-widget-number-of-kills' => $this->pluginSettings['number_of_kills'],
//			'yulai-federation-killboard-widget-show-losses' => ($this->pluginSettings['show_losses']['yes']) ? true : false
		));

		/**
		 * Sanitize the stuff rom our widget's form
		 *
		 * @var array
		 */
		$instance['yulai-federation-killboard-widget-title'] = (string) \esc_html($new_instance['yulai-federation-killboard-widget-title']);
		$instance['yulai-federation-killboard-widget-number-of-kills'] = (int) $new_instance['yulai-federation-killboard-widget-number-of-kills'];
//		$instance['yulai-federation-killboard-widget-show-losses'] = $new_instance['yulai-federation-killboard-widget-show-losses'] ? 1 : 0;

		/**
		 * return new settings for saving them
		 */
		return $instance;
	} // END public function update($new_instance, $old_instance)

	/**
	 * Widget Output
	 *
	 * @param type $args
	 * @param type $instance
	 */
	public function widget($args, $instance) {
			echo $args['before_widget'];

			$title = (empty($instance['yulai-federation-killboard-widget-title'])) ? '' : \apply_filters('yulai-federation-killboard-widget-title', $instance['yulai-federation-killboard-widget-title']);

			if(!empty($title)) {
				echo $args['before_title'] . $title . $args['after_title'];
			} // END if(!empty($title))

		if((isset($this->pluginSettings['killmail_source']) && $this->pluginSettings['killmail_source'] === 'local') && $this->kbDB) {
			echo $this->getEdkWidgetData($instance);
		} // END if((isset($this->pluginSettings['killmail_source']) && $this->pluginSettings['killmail_source'] === 'local') && $this->kbDB)

		if(isset($this->pluginSettings['killmail_source']) && $this->pluginSettings['killmail_source'] === 'zkillboard') {
			echo $this->getZkbWidgetData($instance);
		}
			echo $args['after_widget'];
	} // END public function widget($args, $instance)

	private function getEdkWidgetData($instance) {
		$killList = $this->pluginHelper->getKillList($instance['yulai-federation-killboard-widget-number-of-kills']);

		if(!empty($killList) && is_array($killList)) {
			$widgetHtml = null;

			foreach($killList as $kill) {
				$stringInvolved = ($kill->involved - 1 === 0) ? '' : ' (+' . ($kill->involved - 1) . ')';

				$widgetHtml .= '<div class="row killboard-entry">'
							. '	<div class="col-xs-4 col-sm-12 col-md-12 col-lg-5">'
							. '		<figure>'
							. '			<a href="' . $kill->killboardLink . '" rel="external">'
							.				$kill->victimImage
							. '			</a>'
							. '		</figure>'
							. '	</div>'
							. '	<div class="col-xs-8 col-sm-12 col-md-12 col-lg-7">'
							. '		<ul>'
							. '			<li>Pilot: ' . $kill->plt_name . '</li>'
							. '			<li>Ship: ' . $kill->shp_name . '</li>'
							. '			<li>ISK lost: ' . $kill->isk_loss_formatted . '</li>'
							. '			<li>System: ' . $kill->sys_name . '</li>'
							. '			<li>Killed by: ' . $kill->fbplt_name . $stringInvolved . '</li>'
							. '		</ul>'
							. '	</div>'
							. '</div>';
			} // END foreach($killList as $kill)
		} // END if(!empty($killList) && is_array($killList))

		return $widgetHtml;
	} // END private function getWidgetData($instance)

	private function getZkbWidgetData($instance) {
		$killList = YulaiFederation\Plugins\Helper\ZkbKillboardHelper::getInstance()->getKillList($instance['yulai-federation-killboard-widget-number-of-kills']);

		if(!empty($killList) && is_array($killList)) {
			$widgetHtml = null;

			foreach($killList as $killmail) {
				$countAttackers = \count($killmail->attackers);
				$stringInvolved = ($countAttackers - 1 === 0) ? '' : ' (+' . ($countAttackers - 1) . ')';

				$killType = ' kill-list-kill-mail';
				if($killmail->victim->corporationID === (int) $this->entityID || $killmail->victim->allianceID === (int) $this->entityID) {
					$killType = ' kill-list-loss-mail';
				} // END if($killmail->victim->corporationID === $this->entityID || $killmail->victim->allianceID === $this->entityID)

				$widgetHtml .= '<div class="row killboard-entry' . $killType . '">'
							. '	<div class="col-xs-4 col-sm-12 col-md-12 col-lg-5">'
							. '		<figure>'
							. '			<a href="' . YulaiFederation\Plugins\Helper\ZkbKillboardHelper::getInstance()->getKillboardLink($killmail->killID) . '" rel="external">'
							.				YulaiFederation\Plugins\Helper\ZkbKillboardHelper::getInstance()->getVictimImage($killmail->victim)
							. '			</a>'
							. '		</figure>'
							. '	</div>'
							. '	<div class="col-xs-8 col-sm-12 col-md-12 col-lg-7">'
							. '		<ul>'
							. '			<li>' . YulaiFederation\Plugins\Helper\ZkbKillboardHelper::getInstance()->getVictimType($killmail->victim) . ': ' . YulaiFederation\Plugins\Helper\ZkbKillboardHelper::getInstance()->getVictimName($killmail->victim) . '</li>'
							. '			<li>' . YulaiFederation\Plugins\Helper\ZkbKillboardHelper::getInstance()->getVictimShipType($killmail->victim) . ': ' . YulaiFederation\Plugins\Helper\ZkbKillboardHelper::getInstance()->getVictimShip($killmail->victim) . '</li>'
							. '			<li>ISK lost: ' . YulaiFederation\Plugins\Helper\ZkbKillboardHelper::getInstance()->getIskLoss($killmail->zkb) . '</li>'
							. '			<li>System: ' . YulaiFederation\Plugins\Helper\ZkbKillboardHelper::getInstance()->getSystem($killmail->solarSystemID) . '</li>'
							. '			<li>Killed by: ' . YulaiFederation\Plugins\Helper\ZkbKillboardHelper::getInstance()->getFinalBlow($killmail->attackers) . $stringInvolved . '</li>'
							. '		</ul>'
							. '	</div>'
							. '</div>';
			} // END foreach($array as $killmail)
		} // END if(!empty($killList) && is_array($killList))

		return $widgetHtml;
	} // END private function getZkillboardWidgetData($instance)
} // END class KillboardWidget extends \WP_Widgets
