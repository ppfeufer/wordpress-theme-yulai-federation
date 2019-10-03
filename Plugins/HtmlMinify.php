<?php

namespace WordPress\Themes\YulaiFederation\Plugins;

use WordPress\Themes\YulaiFederation;

class HtmlMinify {
    /**
     * compress css
     *
     * @var boolean
     */
    protected $compress_css = true;

    /**
     * compress javascript
     *
     * @var boolean
     */
    protected $compress_js = true;

    /**
     * add info comment
     *
     * @var boolean
     */
    protected $info_comment = true;

    /**
     * remove comments
     *
     * @var boolean
     */
    protected $remove_comments = true;

    /**
     * the html
     *
     * @var string
     */
    protected $html;

    public function __construct($html) {
        if(!empty($html)) {
            $this->parseHTML($html);
        }
    }

    public function __toString() {
        return $this->html;
    }

    protected function bottomComment($raw, $compressed) {
        $raw = \strlen($raw);
        $compressed = \strlen($compressed);
        $savings = ($raw - $compressed) / $raw * 100;
        $savings = \round($savings, 2);

        return '<!--HTML compressed, size saved ' . $savings . '%. From ' . $raw . ' bytes, now ' . $compressed . ' bytes-->';
    }

    protected function minifyHTML($html) {
        // These have to be removed right away ....
        if($this->remove_comments) {
            // Remove any HTML comments, except MSIE conditional comments
            $html = \preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);

            // Remove any JS single line comments starting with //
            $html = \preg_replace('/\/\/ (.*)\n/', ' ', $html);
        }

        $pattern = '/<(?<script>script).*?<\/script\s*>|<(?<style>style).*?<\/style\s*>|<!(?<comment>--).*?-->|<(?<tag>[\/\w.:-]*)(?:".*?"|\'.*?\'|[^\'">]+)*>|(?<text>((<[^!\/\w.:-])?[^<]*)+)|/si';
        \preg_match_all($pattern, $html, $matches, \PREG_SET_ORDER);

        $overriding = false;
        $raw_tag = false;

        // Variable reused for output
        $html = '';

        foreach($matches as $token) {
            $tag = (isset($token['tag'])) ? \strtolower($token['tag']) : null;

            $content = $token[0];

            if(\is_null($tag)) {
                if(!empty($token['script'])) {
                    $strip = $this->compress_js;
                } else if(!empty($token['style'])) {
                    $strip = $this->compress_css;
                } else if($content == '<!--wp-html-compression no compression-->') {
                    $overriding = !$overriding;

                    // Don't print the comment
                    continue;
                } else if($this->remove_comments) {
                    if(!$overriding && $raw_tag != 'textarea') {
                        // Remove any HTML comments, except MSIE conditional comments
                        $content = \preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $content);

                        // Remove any JS single or multiline comments like /* comment */
                        $content = \preg_replace('/\/\*(.*)\*\//', ' ', $content);
                    }
                }
            } else {
                if($tag == 'pre' || $tag == 'textarea') {
                    $raw_tag = $tag;
                } else if($tag == '/pre' || $tag == '/textarea') {
                    $raw_tag = false;
                } else {
                    if($raw_tag || $overriding) {
                        $strip = false;
                    } else {
                        $strip = true;

                        // Remove any empty attributes, except:
                        // action, alt, content, src
                        $content = \preg_replace('/(\s+)(\w++(?<!\baction|\balt|\bcontent|\bsrc)="")/', '$1', $content);

                        // Remove any space before the end of self-closing XHTML tags
                        // JavaScript excluded
                        $content = \str_replace(' />', '/>', $content);
                    }
                }
            }

            if($strip) {
                $content = $this->removeWhiteSpace($content);
            }

            $html .= $content;
        }

        return $html;
    }

    public function parseHTML($html) {
        $this->html = $this->minifyHTML($html);

        if($this->info_comment) {
            $this->html .= "\n" . $this->bottomComment($html, $this->html);
        }
    }

    protected function removeWhiteSpace($str) {
        $str = \str_replace("\t", ' ', $str);
        $str = \str_replace("\n", '', $str);
        $str = \str_replace("\r", '', $str);

        while(\stristr($str, '  ')) {
            $str = \str_replace('  ', ' ', $str);
        }

        return $str;
    }
}
