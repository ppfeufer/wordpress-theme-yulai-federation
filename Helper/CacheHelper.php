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
		return \trailingslashit(\WP_CONTENT_DIR) . 'cache/themes/' . \sanitize_title(ThemeHelper::getInstance()->getThemeName());
	} // END public static function getThemeCacheDir()

	/**
	 * Getting the URI for the cache directory
	 *
	 * @return string URI for the cache directory
	 */
	public function getThemeCacheUri() {
		return \trailingslashit(\WP_CONTENT_URL) . 'cache/themes/' . \sanitize_title(ThemeHelper::getInstance()->getThemeName());
	} // END public static function getThemeCacheUri()

	/**
	 * Getting the local image cache directory
	 *
	 * @return string Local image cache directory
	 */
	public function getImageCacheDir() {
		return \trailingslashit($this->getThemeCacheDir() . '/images');
	} // END public static function getImageCacheDir()

	/**
	 * Getting the local image cache URI
	 *
	 * @return string Local image cache URI
	 */
	public function getImageCacheUri() {
		return \trailingslashit($this->getThemeCacheUri() . '/images');
	} // END public static function getImageCacheUri()

	/**
	 * Getting the URI for the EVE API cache directory
	 *
	 * @return string URI for the EVE API cache directory
	 */
	public function getEveApiCacheDir() {
		return \trailingslashit($this->getThemeCacheDir() . '/eve-api');
	} // END public static function getEveApiCacheDir()

	/**
	 * Getting the local EVE API cache URI
	 *
	 * @return string Local EVE API cache URI
	 */
	public function getEveApiCacheUri() {
		return \trailingslashit($this->getThemeCacheUri() . '/eve-api');
	} // END public static function getEveApiCacheUri()

	/**
	 * creating our needed cache directories under:
	 *		/wp-content/cache/themes/«theme-name»/
	 */
	public function createCacheDirectory($directory = '') {
		$wpFileSystem =  new \WP_Filesystem_Direct(null);

		if($wpFileSystem->is_writable($wpFileSystem->wp_content_dir())) {
			if(!$wpFileSystem->is_dir(\trailingslashit($this->getThemeCacheDir()) . $directory)) {
				$wpFileSystem->mkdir(\trailingslashit($this->getThemeCacheDir()) . $directory, 0755);
			} // END if(!$wpFileSystem->is_dir(\trailingslashit($this->getThemeCacheDir()) . $directory))
		} // END if($wpFileSystem->is_writable($wpFileSystem->wp_content_dir()))
	} // END public static function createCacheDirectories()

	/**
	 * Chek if a remote image has been cached locally
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
			} // END if(\time() - \filemtime($cacheDir . $imageName) > 2 * 3600)
		} else {
			$returnValue = false;
		} // END if(\file_exists($cacheDir . $imageName))

		return $returnValue;
	} // END public static function checkCachedImage($cacheType = null, $imageName = null)

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
		} // END if($extension === 'gif' || $extension === 'jpg' || $extension === 'jpeg' || $extension === 'png')
	} // END public static function cacheRemoteImageFile($cacheType = null, $remoteImageUrl = null)
} // END class CacheHelper
