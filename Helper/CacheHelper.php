<?php

namespace WordPress\Themes\YulaiFederation\Helper;

use WordPress\Themes\YulaiFederation;

class CacheHelper {
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

    /**
     * Getting the absolute path for the cache directory
     *
     * @return string absolute path for the cache directory
     */
    public function getThemeCacheDir() {
        return \trailingslashit(\WP_CONTENT_DIR) . 'cache/eve-online/';
    }

    /**
     * Getting the URI for the cache directory
     *
     * @return string URI for the cache directory
     */
    public function getThemeCacheUri() {
        return \trailingslashit(\WP_CONTENT_URL) . 'cache/eve-online/';
    }

    /**
     * Getting the local image cache directory
     *
     * @return string Local image cache directory
     */
    public function getImageCacheDir() {
        return \trailingslashit($this->getThemeCacheDir() . 'images');
    }

    /**
     * Getting the local image cache URI
     *
     * @return string Local image cache URI
     */
    public function getImageCacheUri() {
        return \trailingslashit($this->getThemeCacheUri() . 'images');
    }

    /**
     * creating our needed cache directories under:
     *      /wp-content/cache/themes/«theme-name»/
     */
    public function createCacheDirectory($directory = '') {
        $wpFileSystem = new \WP_Filesystem_Direct(null);
        $dirToCreate = \trailingslashit($this->getThemeCacheDir() . $directory);

        \wp_mkdir_p($dirToCreate);

        if(!$wpFileSystem->is_file($dirToCreate . '/index.php')) {
            $wpFileSystem->put_contents(
                $dirToCreate . '/index.php', '', 0644
            );
        }
    }

    /**
     * Check if a remote image has been cached locally
     *
     * @param string $cacheType The subdirectory in the image cache filesystem
     * @param string $imageName The image file name
     * @return boolean true or false
     */
    public function checkCachedImage($cacheType = null, $imageName = null) {
        $cacheDir = \trailingslashit($this->getImageCacheDir() . $cacheType);

        if(\file_exists($cacheDir . $imageName)) {
            /**
             * Check if the file is older than 24 hrs
             * If so, time to renew it
             *
             * This is just in case our cronjob doesn't run for whetever reason
             */
            if(\time() - \filemtime($cacheDir . $imageName) > 24 * 3600) {
                \unlink($cacheDir . $imageName);

                $returnValue = false;
            } else {
                $returnValue = true;
            }
        } else {
            $returnValue = false;
        }

        return $returnValue;
    }

    /**
     * Cachng a remote image locally
     *
     * @param string $cacheType The subdirectory in the image cache filesystem
     * @param string $remoteImageUrl The URL for the remote image
     */
    public function cacheRemoteImageFile($cacheType = null, $remoteImageUrl = null) {
        $cacheDir = \trailingslashit($this->getImageCacheDir() . $cacheType);
        $explodedImageUrl = \explode('/', $remoteImageUrl);
        $imageFilename = \end($explodedImageUrl);
        $explodedImageFilename = \explode('.', $imageFilename);
        $extension = \end($explodedImageFilename);

        // make sure its an image
        if($extension === 'gif' || $extension === 'jpg' || $extension === 'jpeg' || $extension === 'png') {
            // get the remote image
            $get = \wp_remote_get($remoteImageUrl);
            $imageToFetch = \wp_remote_retrieve_body($get);

            $wpFileSystem = new \WP_Filesystem_Direct(null);

            return $wpFileSystem->put_contents($cacheDir . $imageFilename, $imageToFetch, 0755);
        }
    }

    /**
     * Getting transient cache information / data
     *
     * @param string $transientName
     * @return mixed
     */
    public function getTransientCache($transientName) {
        $data = \get_transient($transientName);

        return $data;
    }

    /**
     * Setting the transient cache
     *
     * @param string $transientName cache name
     * @param mixed $data the data that is needed to be cached
     * @param type $time cache time in hours (default: 2)
     */
    public function setTransientCache($transientName, $data, $time = 2) {
        \set_transient($transientName, $data, $time * \HOUR_IN_SECONDS);
    }
}
