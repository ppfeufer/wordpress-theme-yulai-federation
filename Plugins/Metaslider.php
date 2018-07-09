<?php
/**
 * Utilizing the ML-Slider Plugin in our theme
 */

namespace WordPress\Themes\YulaiFederation\Plugins;

use WordPress\Themes\YulaiFederation;

\defined('ABSPATH') or die();

class Metaslider {
    public function __construct() {
        ;
    }

    public function init() {
        $this->registerMetaBox();
    }

    public function registerMetaBox() {
        \add_action('add_meta_boxes', [$this, 'addMetaBox']);
        \add_action('save_post', [$this, 'saveMetaBox']);
        \add_action('yf_render_header_slider', [$this, 'renderSlider']);
    }

    /**
     * Add Meta Slider Box to page settings
     */
    public function addMetaBox() {
        $returnValue = false;

        if($this->metasliderPluginExists()) {
            \add_meta_box('yf-metaslider-page-slider', \__('Page Meta Slider', 'yulai-federation'), [$this, 'renderMetaBox'], 'page', 'side');

            $returnValue = true;
        }

        return $returnValue;
    }

    /**
     * Render the Meta Slider Box
     *
     * @param object $post
     */
    public function renderMetaBox($post) {
        $returnValue = false;

        if($this->metasliderPluginExists()) {
            $metaslider = \get_post_meta($post->ID, 'yf_metaslider_slider', true);

            // Default stretch setting to theme setting.
            $metaslider_stretch = 0;

            $options = $this->metasliderGetOptions();

            if(\metadata_exists('post', $post->ID, 'yf_metaslider_slider_stretch')) {
                $metaslider_stretch = \get_post_meta($post->ID, 'yf_metaslider_slider_stretch', true);
            }
            ?>
            <label><strong><?php \_e('Display Page Meta Slider', 'yulai-federation'); ?></strong></label>
            <p>
                <select name="yf_page_metaslider">
                    <?php
                    foreach($options as $id => $name) {
                        ?>
                        <option value="<?php echo \esc_attr($id); ?>" <?php \selected($metaslider, $id); ?>><?php echo \esc_html($name); ?></option>
                        <?php
                    } // END foreach($options as $id => $name)
                    ?>
                </select>
            </p>
            <p class="checkbox-wrapper">
                <input id="yf_page_metaslider_stretch" name="yf_page_metaslider_stretch" type="checkbox" <?php \checked($metaslider_stretch); ?> />
                <label for="yf_page_metaslider_stretch"><?php \_e('Stretch Page Meta Slider', 'yulai-federation'); ?></label>
            </p>
            <?php
            \wp_nonce_field('save', '_yf_metaslider_nonce');

            $returnValue = true;
        }

        return $returnValue;
    }

    public function saveMetaBox($post_id) {
        $postNonce = \filter_input(\INPUT_POST, '_yf_metaslider_nonce');

        if(empty($postNonce) || !\wp_verify_nonce($postNonce, 'save')) {
            return false;
        }

        if(!\current_user_can('edit_post', $post_id)) {
            return false;
        }

        if(\defined('DOING_AJAX')) {
            return false;
        }

        \update_post_meta($post_id, 'yf_metaslider_slider', \sanitize_title(\filter_input(\INPUT_POST, 'yf_page_metaslider')));

        $slider_stretch = \filter_input(\INPUT_POST, 'yf_page_metaslider_stretch') == "on";
        \update_post_meta($post_id, 'yf_metaslider_slider_stretch', $slider_stretch);
    }

    /**
     * Getting the options
     *
     * @return string
     */
    function metasliderGetOptions() {
        $options = ['' => __('None', 'yulai-federation')];

        if($this->metasliderPluginExists()) {
            $sliders = \get_posts([
                'post_type' => 'ml-slider',
                'numberposts' => 200,
            ]);

            foreach($sliders as $slider) {
                $options[\sanitize_title('metaSlider_ID_' . $slider->ID)] = \__('Slider: ', 'yulai-federation') . $slider->post_title;
            }
        }

        return $options;
    }

    /**
     * Check if the main plugin actually is installed and is active
     *
     * @return boolean
     */
    public function metasliderPluginExists() {
        return \class_exists('\MetaSliderPlugin');
    }

    public function renderSlider() {
        if($this->metasliderPluginExists()) {
            /**
             * Check if a slider is set for this page
             */
            $page_id = \get_the_ID();
            $page_slider = \get_post_meta($page_id, 'yf_metaslider_slider', true);

            /**
             * No slider set, check for our default slider
             */
            if(empty($page_slider)) {
                $themeOptions = \get_option('yulai_theme_options', YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions());

                if(!empty($themeOptions['default_slider'])) {
                    if(!\is_front_page() && isset($themeOptions['default_slider_on']['frontpage_only'])) {
                        return false;
                    }

                    $page_slider = $themeOptions['default_slider'];
                } else {
                    /**
                     * No slider set at all, not even a defalt one
                     */
                    return false;
                }
            }

            /**
             * Render it
             */
            if(\substr(\sanitize_title($page_slider), 0, 14) == 'metaslider_id_') {
                $slider_id = \intval(\preg_replace('/metaslider_id_/', '', $page_slider));
                $slider_stretch = \get_post_meta($page_id, 'yf_metaslider_slider_stretch', true);
                $sliderHtml = null;

                if($slider_stretch === '') {
                    /**
                     * We'll default to false, this way it is determined by
                     * the slider's own settings
                     */
                    $slider_stretch = 0;
                }

                if($slider_stretch == 1) {
                    $sliderHtml = '<div class="meta-slider slider-' . $slider_id . '" data-stretch="true">';
                } else {
                    $sliderHtml = '<div class="meta-slider slider-' . $slider_id . '">';
                }

                $sliderHtml .= \do_shortcode('[metaslider id=' . $slider_id . ']');
                $sliderHtml .= '</div>';

                echo $sliderHtml;
            } else {
                /**
                 * Wrong format
                 */
                return false;
            }

            return true;
        }

        return false;
    }
}
