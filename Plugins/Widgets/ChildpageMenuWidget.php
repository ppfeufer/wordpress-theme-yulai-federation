<?php

namespace WordPress\Themes\YulaiFederation\Plugins\Widgets;

use WordPress\Themes\YulaiFederation;
use WordPress\Themes\YulaiFederation\Plugins;

class ChildpageMenuWidget extends \WP_Widget {
	public $idBase = null;
	public $widgetName = null;

	public function __construct() {
		$this->idBase = 'yf_childpage_widget';
		$this->widgetName = \__('Childpage Menu Widget', 'yulai-federation');


		$widget_options = array(
			'classname' => 'yf-childpage-menu-widget',
			'description' => \__('Displaying the childpages as a menu in your sidebar.', 'yulai-federation')
		);

		$control_options = array();

		parent::__construct($this->idBase, $this->widgetName, $widget_options, $control_options);
	} // END public function __construct()

	/**
	 * Widget Output
	 *
	 * @param type $args
	 * @param type $instance
	 */
	/**
	 *
	 * @param type $args
	 * @param type $instance
	 */
	public function widget($args, $instance) {
		if(\is_page()) {
			$widgetData = $this->getWidgetData();

			if(!empty($widgetData)) {
				echo $args['before_widget'];
				echo '<ul class="childpages-list">' . $widgetData . '</ul>';
				echo $args['after_widget'];
			} // END if(!empty($widgetData))
		} // END if(\is_page())
	} // END public function widget($args, $instance)

	private function getWidgetData() {
		global $post;

		$returnValue = false;

		if($post->post_parent) {
			$ancestors = \get_post_ancestors($post->ID);

			$root = \count($ancestors) - 1;
			$parent = $ancestors[$root];
		} else {
			$parent = $post->ID;
		} // END if($post->post_parent)

		$returnValue = \wp_list_pages(array(
			'title_li' => '',
			'child_of' => $parent,
			'echo' => false
		));

		return $returnValue;
	} // END private function getWidgetData()
} // END class ChildpageMenuWidget extends \WP_Widget
