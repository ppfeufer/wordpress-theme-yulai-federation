<?php
/**
 * String Helper
 *
 * Do some funny stuff with strings that is not covered by WordPress
 */

namespace WordPress\Themes\YulaiFederation\Helper;

\defined('ABSPATH') or die();

class StringHelper {
	public static function cutString($string, $pos) {
		$string = strip_tags($string);

		if($pos < \strlen($string)) {
			$text = \substr($string, 0, $pos);

			if(($strrpos = \strrpos($text,' ')) !== false) {
				$text = \substr($text, 0, $strrpos);
			} // END if(($strrpos = strrpos($text,' ')) !== false)

			$string = $text . ' [...]';
		} // END if($pos < strlen($string))

		return $string;
	} // END function cutString($string, $pos)

	/**
	 * Make a string camelCase
	 *
	 * @param string $string
	 * @param boolean $ucFirst
	 * @param array $noStrip
	 * @return string
	 */
	public static function camelCase($string, $ucFirst = false, $noStrip = array()) {
		// First we make sure all is lower case
		$string = \strtolower($string);

		// non-alpha and non-numeric characters become spaces
		$string = \preg_replace('/[^a-z0-9' . \implode('', $noStrip) . ']+/i', ' ', $string);
		$string = \trim($string);

		// uppercase the first character of each word
		$string = \ucwords($string);
		$string = \str_replace(' ', '', $string);

		if($ucFirst === false) {
			$string = \lcfirst($string);
		} // END if($ucFirst === false)

		return $string;
	} // END public static function camelCase($string, $ucFirst = false, $noStrip = array())
} // END class StringHelper