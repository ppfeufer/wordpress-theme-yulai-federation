<?php
/**
 * String Helper
 *
 * Do some funny stuff with strings that is not covered by WordPress
 */

namespace WordPress\Themes\YulaiFederation\Helper;

\defined('ABSPATH') or die();

class String {
	public function cutString($string, $pos) {
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
} // END class String