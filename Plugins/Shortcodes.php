<?php
/**
 * Theme Shortcodes Plugin
 */

namespace WordPress\Themes\YulaiFederation\Plugins;

\defined('ABSPATH') or die();

class Shortcodes {
	public function __construct() {
//		$this->changeWpAuto();
		$this->addShortcodesToWidgets();
		$this->registerShortcodes();
	} // END public function __construct()

	public function registerShortcodes() {
		\add_shortcode('two_columns_one', array($this, 'shortcodeTwoColumnsOne'));
		\add_shortcode('three_columns_one', array($this, 'shortcodeThreeColumnsOne'));
		\add_shortcode('three_columns_two', array($this, 'shortcodeThreeColumnsTwo'));
		\add_shortcode('four_columns_one', array($this, 'shortcodeFourColumnsOne'));
		\add_shortcode('four_columns_two', array($this, 'shortcodeFourColumnsTwo'));
		\add_shortcode('four_columns_three', array($this, 'shortcodeFourColumnsThree'));
		\add_shortcode('divider', array($this, 'shortcodeDivider'));
//		\add_shortcode('button', array($this, 'shortcodeButton'));
		\add_shortcode('credits', array($this, 'shortcodeCredits'));
	} // END public function registerShortcodes()

	public function shortcodeTwoColumnsOne($atts, $content = null) {
		$args = \shortcode_atts(
			array(
				'position' => ''
			),
			$atts
		);

		switch($args['position']) {
			case 'first':
				$prefix = '<div class="row">';
				$suffix = '';
				break;

			case 'last':
				$prefix = '';
				$suffix = '</div>';
				break;

			default:
				$prefix = '';
				$suffix = '';
				break;
		}

		$html = $prefix . '<div class="col-md-6">' . $this->removeAutopInShortcode($content) . '</div>' . $suffix;

		return $html;
	} // END public function shortcodeTwoColumnsOne($atts, $content = null)

	public function shortcodeThreeColumnsOne($atts, $content = null) {
		$args = \shortcode_atts(
			array(
				'position' => ''
			),
			$atts
		);

		switch($args['position']) {
			case 'first':
				$prefix = '<div class="row">';
				$suffix = '';
				break;

			case 'last':
				$prefix = '';
				$suffix = '</div>';
				break;

			default:
				$prefix = '';
				$suffix = '';
				break;
		}

		return $prefix . '<div class="col-md-4">' . $this->removeAutopInShortcode($content) . '</div>' . $suffix;
	} // END public function shortcodeThreeColumnsOne($atts, $content = null)

	public function shortcodeThreeColumnsTwo($atts, $content = null) {
		$args = \shortcode_atts(
			array(
				'position' => ''
			),
			$atts
		);

		switch($args['position']) {
			case 'first':
				$prefix = '<div class="row">';
				$suffix = '';
				break;

			case 'last':
				$prefix = '';
				$suffix = '</div>';
				break;

			default:
				$prefix = '';
				$suffix = '';
				break;
		}

		return $prefix . '<div class="col-md-8">' . $this->removeAutopInShortcode($content) . '</div>' . $suffix;
	} // END public function shortcodeThreeColumnsTwo($atts, $content = null)

	public function shortcodeFourColumnsOne($atts, $content = null) {
		$args = \shortcode_atts(
			array(
				'position' => ''
			),
			$atts
		);

		switch($args['position']) {
			case 'first':
				$prefix = '<div class="row">';
				$suffix = '';
				break;

			case 'last':
				$prefix = '';
				$suffix = '</div>';
				break;

			default:
				$prefix = '';
				$suffix = '';
				break;
		}

		return $prefix . '<div class="col-md-3">' . $this->removeAutopInShortcode($content) . '</div>' . $suffix;
	} // END public function shortcodeFourColumnsOne($atts, $content = null)

	public function shortcodeFourColumnsTwo($atts, $content = null) {
		$args = \shortcode_atts(
			array(
				'position' => ''
			),
			$atts
		);

		switch($args['position']) {
			case 'first':
				$prefix = '<div class="row">';
				$suffix = '';
				break;

			case 'last':
				$prefix = '';
				$suffix = '</div>';
				break;

			default:
				$prefix = '';
				$suffix = '';
				break;
		}

		return $prefix . '<div class="col-md-6">' . $this->removeAutopInShortcode($content) . '</div>' . $suffix;
	} // END public function shortcodeFourColumnsTwo($atts, $content = null)

	public function shortcodeFourColumnsThree($atts, $content = null) {
		$args = \shortcode_atts(
			array(
				'position' => ''
			),
			$atts
		);

		switch($args['position']) {
			case 'first':
				$prefix = '<div class="row">';
				$suffix = '';
				break;

			case 'last':
				$prefix = '';
				$suffix = '</div>';
				break;

			default:
				$prefix = '';
				$suffix = '';
				break;
		}

		return $prefix . '<div class="col-md-9">' . $this->removeAutopInShortcode($content) . '</div>' . $suffix;
	} // END public function shortcodeFourColumnsThree($atts, $content = null)

	public function shortcodeDivider($atts, $content = null) {
		return '<div class="divider"></div>';
	} // END public function shortcodeDivider($atts, $content = null)

//	public function shortcodeButton($atts, $content = null) {
//		$attributes = \shortcode_atts(array(
//			'type' => 'standard',
//			'link' => '#',
//			'target' => '_self',
//			'size' => '',
//		), $atts);
//
//		$type = (!empty($attributes['type'])) ? ' btn-' . $attributes['type'] : '';
//		$link = $attributes['link'];
//		$target = $attributes['target'];
//		$size = (!empty($attributes['size'])) ? ' btn-' . $attributes['size'] : '';
//
//		$output = '<a class="btn ' . $type . $size . '" href="' . $link . '" target="' . $target . '"><span>' . $this->removeAutopInShortcode($content) . '</span></a>';
//
//		return $output;
//	} // END public function shortcodeButton($atts, $content = null)

	public function shortcodeCredits($atts, $content = null) {
		$attributes = \shortcode_atts(array(
			'headline' => 'h4'
		), $atts);

		$headlineOpen = '<' . $attributes['headline'] . '>';
		$headlineClose = '</' . $attributes['headline'] . '>';

		$output = '<div class="article-credits clearfix"><header>' . $headlineOpen . \__('Credits:', 'yulai-federation') . $headlineClose . '</header>' . $this->removeAutopInShortcode($content) . '</div>';

		return $output;
	} // END public function shortcodeCredits($atts, $content = null)

	public function changeWpAuto() {
		\remove_filter('the_content', 'wpautop');
		\add_filter('the_content', 'wpautop', 99);
		\add_filter('the_content', 'shortcode_unautop', 100);
	} // END public function changeWpAuto()

	public function addShortcodesToWidgets() {
		\add_filter('widget_text', 'do_shortcode');
	} // END public function addShortcodesToWidgets()

	public function removeAutopInShortcode($content) {
		$content = \do_shortcode(\shortcode_unautop($content));
		$content = \preg_replace('#^<\/p>|^<br \/>|<p>$#', '', $content);

		return $content;
	} // END public function removeAutopInShortcode($content)
} // END class Shortcodes
