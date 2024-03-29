<?php

/**
 * Class Name: Cron
 */

namespace WordPress\Themes\YulaiFederation\Addons;

use WordPress\Themes\YulaiFederation;

class Cron {
    private $themeOptions = null;
    public $cronEvents = [];

    public function __construct() {
        $this->themeOptions = \get_option('yulai_theme_options', YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions());
        $this->cronEvents = $this->getThemeCronEvents();
    }

    /**
     * Returning all known theme crons as an array
     *
     * @return array Themes Cron Events with their respective hooks
     */
    public function getThemeCronEvents() {
        return [
            // Daily Image Cache Cleanup
            'Cleanup Image Cache' => [
                'hook' => 'cleanupThemeImageCache',
                'recurrence' => 'daily'
            ],
            'Cleanup Transient Database Cache' => [
                'hook' => 'cleanupTransientCache',
                'recurrence' => 'daily'
            ]
        ];
    }

    /**
     * Initializing all the stuff
     */
    public function init() {
        // Managing the crons action hooks
        foreach($this->cronEvents as $cronEvent) {
            /**
             * Only add the cron if the theme settings say so or else remove them
             */
            if(!empty($this->themeOptions['cron'][$cronEvent['hook']])) {
                \add_action($cronEvent['hook'], [
                    $this,
                    'cron' . \ucfirst($cronEvent['hook'])
                ]);
            } else {
                $this->removeCron($cronEvent['hook']);
            }
        }

        \add_action('switch_theme', [$this, 'removeAllCrons'], 10, 2);

        $this->scheduleCronEvents();
    }

    /**
     * Removing all known theme crons
     */
    public function removeAllCrons() {
        foreach($this->cronEvents as $cronEvent) {
            // removing $cronEvent
            $this->removeCron($cronEvent['hook']);
        }
    }

    /**
     * Remove a single cron job
     *
     * @param string $cronEvent Hook of the cron to remove
     */
    public function removeCron($cronEvent = null) {
        \wp_clear_scheduled_hook($cronEvent);
    }

    /**
     * schedule the cron jobs
     */
    public function scheduleCronEvents() {
        foreach($this->cronEvents as $cronEvent) {
            if(!\wp_next_scheduled($cronEvent['hook']) && !empty($this->themeOptions['cron'][$cronEvent['hook']])) {
                \wp_schedule_event(\time(), $cronEvent['recurrence'], $cronEvent['hook']);
            }
        }
    }

    /**
     * Cron Job: cronCleanupImageCache
     * Schedule: Daily
     */
    public function cronCleanupThemeImageCache() {
        $imageCacheDirectory = YulaiFederation\Helper\CacheHelper::getInstance()->getImageCacheDir();

        YulaiFederation\Helper\FilesystemHelper::getInstance()->deleteDirectoryRecursive($imageCacheDirectory, false);
    }

    /**
     * Cron Job: cleanupTransientCache
     * Schedule: Daily
     *
     * @global type $wpdb
     */
    public function cronCleanupTransientCache() {
        global $wpdb;

        $wpdb->query('DELETE FROM `' . $wpdb->prefix . 'options' . '` WHERE `option_name` LIKE (\'_transient_%\');');
        $wpdb->query('DELETE FROM `' . $wpdb->prefix . 'options' . '` WHERE `option_name` LIKE (\'_site_transient_%\');');
    }
}
