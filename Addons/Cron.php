<?php

/**
 * Class Name: Cron
 */

namespace WordPress\Themes\YulaiFederation\Addons;

use WordPress\Themes\YulaiFederation;
use function add_action;
use function get_option;
use function time;
use function ucfirst;
use function wp_clear_scheduled_hook;
use function wp_next_scheduled;
use function wp_schedule_event;

class Cron {
    public array $cronEvents = [];
    private $themeOptions;

    public function __construct() {
        $this->themeOptions = get_option('yulai_theme_options', YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions());
        $this->cronEvents = $this->getThemeCronEvents();
    }

    /**
     * Returning all known theme crons as an array
     *
     * @return array Themes Cron Events with their respective hooks
     */
    public function getThemeCronEvents(): array {
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
    public function init(): void {
        // Managing the crons action hooks
        foreach ($this->cronEvents as $cronEvent) {
            /**
             * Only add the cron if the theme settings say so or else remove them
             */
            if (!empty($this->themeOptions['cron'][$cronEvent['hook']])) {
                add_action($cronEvent['hook'], [
                    $this,
                    'cron' . ucfirst($cronEvent['hook'])
                ]);
            } else {
                $this->removeCron($cronEvent['hook']);
            }
        }

        add_action('switch_theme', [$this, 'removeAllCrons'], 10, 2);

        $this->scheduleCronEvents();
    }

    /**
     * Remove a single cron job
     *
     * @param string|null $cronEvent Hook of the cron to remove
     */
    public function removeCron(string $cronEvent = null): void {
        wp_clear_scheduled_hook($cronEvent);
    }

    /**
     * Schedule the cron jobs
     */
    public function scheduleCronEvents(): void {
        foreach ($this->cronEvents as $cronEvent) {
            if (!empty($this->themeOptions['cron'][$cronEvent['hook']]) && !wp_next_scheduled($cronEvent['hook'])) {
                wp_schedule_event(time(), $cronEvent['recurrence'], $cronEvent['hook']);
            }
        }
    }

    /**
     * Removing all known theme crons
     */
    public function removeAllCrons(): void {
        foreach ($this->cronEvents as $cronEvent) {
            // removing $cronEvent
            $this->removeCron($cronEvent['hook']);
        }
    }

    /**
     * Cron Job: cronCleanupImageCache
     * Schedule: Daily
     */
    public function cronCleanupThemeImageCache(): void {
        $imageCacheDirectory = YulaiFederation\Helper\CacheHelper::getInstance()->getImageCacheDir();

        YulaiFederation\Helper\FilesystemHelper::getInstance()->deleteDirectoryRecursive($imageCacheDirectory, false);
    }

    /**
     * Cron Job: cleanupTransientCache
     * Schedule: Daily
     *
     * @global $wpdb
     */
    public function cronCleanupTransientCache(): void {
        global $wpdb;

        $wpdb->query('DELETE FROM `' . $wpdb->prefix . 'options' . '` WHERE `option_name` LIKE (\'_transient_%\');');
        $wpdb->query('DELETE FROM `' . $wpdb->prefix . 'options' . '` WHERE `option_name` LIKE (\'_site_transient_%\');');
    }
}
