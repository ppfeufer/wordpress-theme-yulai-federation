<?php

/**
 * Theme Settings
 */

namespace WordPress\Themes\YulaiFederation\Admin;

use WordPress\Themes\YulaiFederation;

\defined('ABSPATH') or die();

class ThemeSettings {
    /**
     * EVE API
     *
     * @var \WordPress\Themes\YulaiFederation\Helper\EsiHelper
     */
    private $eveApi = null;

    /**
     * Meta Slider
     * @var \WordPress\Themes\YulaiFederation\Plugins\Metaslider
     */
    private $metaSlider = null;

    /**
     * Theme Options
     *
     * @var array
     */
    private $themeOptions = null;

    /**
     *
     * @var \WordPress\Themes\YulaiFederation\Admin\SettingsApi
     */
    private $settingsApi = null;

    /**
     * Settings Filter
     *
     * @var string
     */
    private $settingsFilter = null;

    /**
     * Constructor
     */
    public function __construct() {
        $this->eveApi = YulaiFederation\Helper\EsiHelper::getInstance();
        $this->metaSlider = new YulaiFederation\Plugins\Metaslider(false);
        $this->themeOptions = \get_option('yulai_theme_options', YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions());

        // trigger the settings API
        $this->fireSettingsApi();
    }

    /**
     * Firing the Settings API
     */
    private function fireSettingsApi() {
        $this->settingsFilter = 'register_yulai_federation_theme_settings';
        $this->settingsApi = new SettingsApi($this->settingsFilter, YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions());
        $this->settingsApi->init();

        \add_filter($this->settingsFilter, [$this, 'renderSettingsPage']);
    }

    /**
     * Render the settings page
     *
     * @return array
     */
    public function renderSettingsPage() {
        $themeOptionsPage['yulai-federation-theme-settings'] = [
            'type' => 'theme',
            'menu_title' => \__('Options', 'yulai-federation'),
            'page_title' => \__('Yulai Federation Theme Settings', 'yulai-federation'),
            'option_name' => 'yulai_theme_options',
            'tabs' => [
                /**
                 * general settings tab
                 */
                'general-settings' => $this->getGeneralSettings(),

                /**
                 * background settings tab
                 */
                'background-settings' => $this->getBackgroundSettings(),

                /**
                 * slider settings tab
                 */
                'slider-settings' => $this->getSliderSettings(),

                /**
                 * performance settings tab
                 */
                'performance-settings' => $this->getPerformanceSettings()
            ]
        ];

        if($this->metaSlider->metasliderPluginExists() === false) {
            $themeOptionsPage['yulai-federation-theme-settings']['tabs']['slider-settings']['fields']['slider-warning'] = [
                'title' => \__('Meta Slider Warning', 'yulai-federation'),
                'type' => 'custom',
                'content' => \sprintf(\__('Please make sure you have the %1$s plugin installed and activated.', 'yulai-federation'), '<a href="https://de.wordpress.org/plugins/ml-slider/" target="_blank">Meta Slider</a>'),
                'callback' => null
            ];
        }

        if(\preg_match('/development/', \APPLICATION_ENV)) {
            $themeOptionsPage['yulai-federation-theme-settings']['tabs']['development'] = $this->getDevelopmentSettings();
        }

        return $themeOptionsPage;
    }

    /**
     * general settings tab
     *
     * @return array
     */
    private function getGeneralSettings() {
        return [
            'tab_title' => \__('General Settings', 'yulai-federation'),
            'tab_description' => \__('General Theme Settings', 'yulai-federation'),
            'fields' => $this->getGeneralSettingsFields()
        ];
    }

    /**
     * general settings tab fields
     *
     * @return array
     */
    private function getGeneralSettingsFields() {
        return [
            'type' => $this->getEntityTypeField(),
            'name' => $this->getEntityNameField(),
            'show_corp_logos' => $this->getShowCorpLogosField(),
            'navigation_even_cells' => $this->getEvenNavigationField(),
            'show_post_meta' => $this->getShowPostMetaField(),
        ];
    }

    /**
     * general setting "Entity Type"
     * @return array
     */
    private function getEntityTypeField() {
        return [
            'title' => \__('Entity Type', 'yulai-federation'),
            'type' => 'select',
            'choices' => [
                'alliance' => \__('Alliance', 'yulai-federation'),
                'corporation' => \__('Corporation', 'yulai-federation')
            ],
            'empty' => \__('Please Select', 'yulai-federation'),
            'description' => 'Is it a Corporation or an Alliance?'
        ];
    }

    /**
     * general setting "Entity Name"
     * @return array
     */
    private function getEntityNameField() {
        return [
            'title' => \__('Entity Name', 'yulai-federation'),
            'type' => 'text',
            'description' => \sprintf(
                \__('The Name of your Corp/Alliance %1$s', 'yulai-federation'), (!empty($this->themeOptions['name']) && !empty($this->themeOptions['type'])) ? '</p></td></tr><tr><th>' . \__('Your Logo', 'yulai-federation') . '</th><td>' . $this->eveApi->getEntityLogoByName($this->themeOptions['name'], $this->themeOptions['type'], false) : ''
            )
        ];
    }

    /**
     * general setting "Show Corp Logos"
     *
     * @return array
     */
    private function getShowCorpLogosField() {
        return [
            'title' => \__('Corp Logos', 'yulai-federation'),
            'type' => 'checkbox',
            'choices' => [
                'show' => \__('Show corp logos in menu for corp pages.', 'yulai-federation')
            ],
            'description' => \__('Only available if you are running an alliance website, so you can have the corp logos in your "Our Corporations" menu.', 'yulai-federation')
        ];
    }

    /**
     * general setting "Navigation"
     *
     * @return array
     */
    private function getEvenNavigationField() {
        return [
            'title' => \__('Navigation', 'yulai-federation'),
            'type' => 'checkbox',
            'choices' => [
                'yes' => \__('Even navigation cells in main navigation', 'yulai-federation')
            ],
            'description' => \__('Transforms the main navigation into even cells instead of random width cells. (only looks good with enough navigation items though ...)', 'yulai-federation')
        ];
    }

    /**
     * general setting "Show Post Meta"
     *
     * @return arrays
     */
    private function getShowPostMetaField() {
        return [
            'title' => \__('Post Meta', 'yulai-federation'),
            'type' => 'checkbox',
            'choices' => [
                'yes' => \__('Show post meta (categories and all that stuff) in article loop and article view.', 'yulai-federation')
            ],
            'description' => \__('If checked the post meta information, such as categories, publish time and author will be displayed in article loop and article view. (Default: on)', 'yulai-federation')
        ];
    }

    /**
     * background settings tab
     *
     * @return array
     */
    private function getBackgroundSettings() {
        return [
            'tab_title' => \__('Background Settings', 'yulai-federation'),
            'tab_description' => \__('Background Settings', 'yulai-federation'),
            'fields' => $this->getBackgroundSettingsFields()
        ];
    }

    /**
     * background settings tab fields
     *
     * @return array
     */
    private function getBackgroundSettingsFields() {
        return [
            'use_background_image' => $this->getUseBackgroundImageField(),
            'background_image' => $this->getThemeBackgroundImagesField(),
            'background_image_upload' => $this->getBackgroundImageUploadField()
        ];
    }

    /**
     * background setting "Use Background Image"
     *
     * @return array
     */
    private function getUseBackgroundImageField() {
        return [
            'title' => \__('Use Background Image', 'yulai-federation'),
            'type' => 'checkbox',
            'choices' => [
                'yes' => \__('Yes, I want to use background images on this website.', 'yulai-federation')
            ],
            'description' => \__('If this option is checked, the website will use your selected (down below) background image instead of a simple colored background.', 'yulai-federation')
        ];
    }

    /**
     * background setting "Background Image
     * @return array
     */
    private function getThemeBackgroundImagesField() {
        return [
            'title' => \__('Background Image', 'yulai-federation'),
            'type' => 'radio',
            'choices' => YulaiFederation\Helper\ThemeHelper::getInstance()->getDefaultBackgroundImages(true),
            'empty' => \__('Please Select', 'yulai-federation'),
            'description' => \__('Select one of the default Background images ...', 'yulai-federation'),
            'align' => 'horizontal'
        ];
    }

    /**
     * background setting "Image Upload"
     *
     * @return array
     */
    private function getBackgroundImageUploadField() {
        return [
            'title' => \__('', 'yulai-federation'),
            'type' => 'image',
            'description' => \__('... or upload your own', 'yulai-federation')
        ];
    }

    /**
     * slider settings tab
     *
     * @return array
     */
    private function getSliderSettings() {
        return [
            'tab_title' => \__('Slider Settings', 'yulai-federation'),
            'tab_description' => \__('Slider Settings', 'yulai-federation'),
            'fields' => $this->getSliderSettingsFields()
        ];
    }

    /**
     * slider settings tab fields
     *
     * @return array
     */
    private function getSliderSettingsFields() {
        return [
            /**
             * !!!
             * Do NOT forget to change the options key in
             * metaslider-plugin as well
             * !!!
             */
            'default_slider' => [
                'title' => \__('Default Slider on Front Page', 'yulai-federation'),
                'type' => 'select',
                'choices' => $this->metaSlider->metasliderGetOptions(),
                'description' => ($this->metaSlider->metasliderPluginExists()) ? \__('Select the default slider for your front page', 'yulai-federation') : \sprintf(\__('Please make sure you have the %1$s plugin installed and activated.', 'yulai-federation'), '<a href="https://wordpress.org/plugins/ml-slider/">Meta Slider</a>')
            ],
            'default_slider_on' => [
                'title' => \__('Pages with Slider', 'yulai-federation'),
                'type' => 'checkbox',
                'choices' => [
                    'frontpage_only' => \__('Show only on front page.', 'yulai-federation')
                ],
                'description' => \__('Show this slider only on front page in case no other slider is defined.', 'yulai-federation')
            ],
        ];
    }

    /**
     * performance settings tab
     *
     * @return array
     */
    private function getPerformanceSettings() {
        return [
            'tab_title' => \__('Performance Settings', 'yulai-federation'),
            'tab_description' => \__('Performance Settings', 'yulai-federation'),
            'fields' => $this->getPerformanceSettingsFields()
        ];
    }

    /**
     * performance tab fields
     *
     * @return array
     */
    private function getPerformanceSettingsFields() {
        return [
            'minify_html_output' => $this->getMinifyHtmlOutputField(),
            'cache' => $this->getCacheField(),
            'cron' => $this->getCronField(),
        ];
    }

    /**
     * performance tab field "HTML Output"
     *
     * @return array
     */
    private function getMinifyHtmlOutputField() {
        return [
            'title' => \__('HTML Output', 'yulai-federation'),
            'type' => 'checkbox',
            'choices' => [
                'yes' => \__('Minify HTML output?', 'yulai-federation')
            ],
            'description' => \__('By minifying the HTML output you might boost your websites performance. NOTE: this may not work on every server, so if you experience issues, turn this option off again!', 'yulai-federation')
        ];
    }

    /**
     * performance tab field "Image Cache"
     *
     * @return array
     */
    private function getCacheField() {
        return [
            'title' => \__('Image Cache', 'yulai-federation'),
            'type' => 'checkbox',
            'choices' => [
                'remote-image-cache' => \__('Use imagecache for images fetched from CCP\'s image server', 'yulai-federation')
            ],
            'description' => \__('If checked the images from CCP\'s image server will be cached locally. (Default: on)', 'yulai-federation')
        ];
    }

    /**
     * performance tab field "Cron Jobs"
     *
     * @return array
     */
    private function getCronField() {
        return [
            'title' => \__('Cron Jobs', 'yulai-federation'),
            'type' => 'checkbox',
            'choices' => [
                'cleanupThemeImageCache' => \__('Use a cronjob to clear the image cache once a day.', 'yulai-federation'),
                'cleanupTransientCache' => \__('Use a cronjob to clear the database transient cache once a day.', 'yulai-federation')
            ],
            'description' => \__('If checked a WordPress cron will be initialized to run te selected task(s). (Default: off)', 'yulai-federation')
        ];
    }

    /**
     * development settings tab
     *
     * @return array
     */
    private function getDevelopmentSettings() {
        return [
            'tab_title' => \__('Development Infos', 'yulai-federation'),
            'tab_description' => \__('Delevopment Information', 'yulai-federation'),
            'fields' => $this->getDevelopmentSettingsFields()
        ];
    }

    /**
     * development settings tab fields
     *
     * @return array
     */
    private function getDevelopmentSettingsFields() {
        return [
            'yf_theme_options_sane' => [
                'title' => \__('Options Array<br>(sane from functions.php)', 'yulai-federation'),
                'type' => 'custom',
                'content' => '<pre>' . \print_r(YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions(), true) . '</pre>',
                'callback' => null,
                'description' => \__('This are the sane options defined in functions.php via <code>\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions()</code>', 'yulai-federation')
            ],
            'yf_theme_options_from_db' => [
                'title' => \__('Options Array<br>(from DB)', 'yulai-federation'),
                'type' => 'custom',
                'content' => '<pre>' . \print_r(\get_option('yulai_theme_options'), true) . '</pre>',
                'callback' => null,
                'description' => \__('This are the options from our database via <code>\get_option(\'yulai_theme_options\')</code>', 'yulai-federation')
            ],
            'yf_theme_options_merged' => [
                'title' => \__('Options Array<br>(merged / used for Theme)', 'yulai-federation'),
                'type' => 'custom',
                'content' => '<pre>' . \print_r(\get_option('yulai_theme_options', YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions()), true) . '</pre>',
                'callback' => null,
                'description' => \__('This are the options used for the theme via <code>\get_option(\'yulai_theme_options\', \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions())</code>', 'yulai-federation')
            ]
        ];
    }
}
