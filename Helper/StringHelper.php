<?php

/**
 * String Helper
 *
 * Do some funny stuff with strings that is not covered by WordPress
 */

namespace WordPress\Themes\YulaiFederation\Helper;

\defined('ABSPATH') or die();

class StringHelper {
    /**
     * instance
     *
     * static variable to keep the current (and only!) instance of this class
     *
     * @var Singleton
     */
    protected static $instance = null;

    public static function getInstance() {
        if(null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * clone
     *
     * no cloning allowed
     */
    protected function __clone() {
        ;
    }

    /**
     * constructor
     *
     * no external instanciation allowed
     */
    protected function __construct() {
        ;
    }

    public function cutString($string, $pos) {
        $string = strip_tags($string);

        if($pos < \strlen($string)) {
            $text = \substr($string, 0, $pos);

            if(($strrpos = \strrpos($text, ' ')) !== false) {
                $text = \substr($text, 0, $strrpos);
            }

            $string = $text . ' [...]';
        }

        return $string;
    }

    /**
     * Make a string camelCase
     *
     * @param string $string
     * @param boolean $ucFirst
     * @param array $noStrip
     * @return string
     */
    public function camelCase($string, $ucFirst = false, $noStrip = []) {
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
        }

        return $string;
    }

    /**
     * converts a hex color string into an array with it's respective rgb(a) values
     *
     * @param string $hex
     * @param string $alpha
     * @return array
     */
    public function hextoRgb($hex, $alpha = false) {
        $hex = \str_replace('#', '', $hex);

        if(\strlen($hex) == 6) {
            $rgb['r'] = \hexdec(\substr($hex, 0, 2));
            $rgb['g'] = \hexdec(\substr($hex, 2, 2));
            $rgb['b'] = \hexdec(\substr($hex, 4, 2));
        } elseif(\strlen($hex) == 3) {
            $rgb['r'] = \hexdec(\str_repeat(\substr($hex, 0, 1), 2));
            $rgb['g'] = \hexdec(\str_repeat(\substr($hex, 1, 1), 2));
            $rgb['b'] = \hexdec(\str_repeat(\substr($hex, 2, 1), 2));
        } else {
            $rgb['r'] = '0';
            $rgb['g'] = '0';
            $rgb['b'] = '0';
        }

        if($alpha !== false) {
            $rgb['a'] = $alpha;
        }

        return $rgb;
    }

    public function encodeMailString($string) {
        $chars = \str_split($string);
        $seed = \mt_rand(0, (int) \abs(\crc32($string) / \strlen($string)));

        foreach($chars as $key => $char) {
            $ord = \ord($char);

            if($ord < 128) { // ignore non-ascii chars
                $r = ($seed * (1 + $key)) % 100; // pseudo "random function"

                if($r > 60 && $char != '@') {
                    // plain character (not encoded), if not @-sign
                    ;
                } elseif($r < 45) {
                    $chars[$key] = '&#x' . \dechex($ord) . ';'; // hexadecimal
                } else {
                    $chars[$key] = '&#' . $ord . ';'; // decimal (ascii)
                }
            }
        }

        return \implode('', $chars);
    }
}
