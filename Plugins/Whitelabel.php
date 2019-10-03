<?php

/**
 * Whitelabel Plugin
 */

namespace WordPress\Themes\YulaiFederation\Plugins;

use WordPress\Themes\YulaiFederation;

\defined('ABSPATH') or die();

class Whitelabel {
    protected $developerName = 'YF [TN-NT] Rounon Dax';
    protected $developerEmailAddress = 'rounon.dax@yulaifederation.net';
    protected $developerWebsite = 'https://yulaifederation.net';
    protected $themeBackgroundUrl = '';

    public function getDeveloperName() {
        return $this->developerName;
    }

    public function getDeveloperEmailAddress() {
        return $this->developerEmailAddress;
    }

    public function getDeveloperWebsite() {
        return $this->developerWebsite;
    }
    public function getThemeBackgroundUrl() {
        return $this->themeBackgroundUrl;
    }

    /**
     * Fire the actions to whitelabel WordPress
     *
     * Maybe edit the .htaccess file aswell
     *      RewriteRule ^login$ http://www.website.de/wp-login.php [NC,L]
     */
    function __construct() {
        $this->themeBackgroundUrl = $this->getBackgroundImage();

        /**
         * Actions
         */
        \add_action('login_head', [$this, 'customLoginLogoStyle']);
        \add_action('wp_dashboard_setup', [$this, 'addDashboardWidget']);

        /**
         * Filters
         */
        \add_filter('admin_footer_text', [$this, 'modifyAdminFooter']);
        \add_filter('login_headerurl', [$this, 'loginLogoUrl']);

        if(\version_compare(\floatval(\get_bloginfo('version')), '5.2', '<')) {
            \add_filter('login_headertitle', [$this, 'loginLogoTitle']);
        } else {
            \add_filter('login_headertext', [$this, 'loginLogoTitle']);
        }
    }

    private function getBackgroundImage() {
        return YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeBackgroundImage();
    }

    /**
     * Custom URL Title
     *
     * @return Ambigous <string, mixed>
     */
    public function loginLogoTitle() {
        return \__('Yulai Federation - NRDS Provibloc Alliance', 'yulai-federation');
    }

    /**
     * Custom URL linked by the Logo on Login page
     *
     * @return Ambigous <string, mixed, boolean>
     */
    public function loginLogoUrl() {
        return \site_url();
    }

    /**
     * Developer Info in Admin Footer
     */
    public function modifyAdminFooter($content) {
        $content .= \sprintf(' | %1$s %2$s',
            \__('Customized by:', 'yulai-federation'),
            ' <a href="' . $this->developerWebsite . '" target="_blank">' . $this->developerName . '</a>'
        );

        return $content;
    }

    /**
     * Dashboard Widget with Developer Contact Info
     */
    public function themeInfo() {
        echo '<ul>
        <li><strong>' . \__('Customized by:', 'yulai-federation') . '</strong> ' . $this->developerName . '</li>
        <li><strong>' . \__('Website:', 'yulai-federation') . '</strong> <a href="' . $this->developerWebsite . '">' . $this->developerWebsite . '</a></li>
        <li><strong>' . \__('Contact:', 'yulai-federation') . '</strong> <a href="mailto:' . $this->developerEmailAddress . '">' . $this->developerEmailAddress . '</a></li>
        </ul>';
    }

    public function addDashboardWidget() {
        \wp_add_dashboard_widget('wp_dashboard_widget', __('Developer Contact', 'yulai-federation'), [$this, 'themeInfo']);
    }

    /**
     * Custom Logo on Login Page
     */
    public function customLoginLogoStyle() {
        echo '<style type="text/css">
        body {
            background-image: url("' . $this->themeBackgroundUrl . '");
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }
        h1 a {
            background-image:url(' . \get_template_directory_uri() . '/img/yulai-logo-320.png) !important;
            background-size: 320px 320px !important;
            background-size: 20rem 20rem !important;
            height: 320px !important;
            width: 320px !important;
            height: 20rem !important;
            width: 20rem !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
        .login form {
            margin-top: 10px !important;
            margin-top: 0.625rem !important;
            background-color: rgba(255,255,255,0.7);
        }
        .login input[type="text"], .login input[type="password"] {
            background-color: rgba(251,251,251,0.5);
        }
        </style>';
    }
}
