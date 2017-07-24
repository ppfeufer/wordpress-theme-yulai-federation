<?php

namespace WordPress\Themes\YulaiFederation\Helper;

\defined('ABSPATH') or die();

class ImageHelper {
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

//	public static function getAttachmentId($url) {
	public function getAttachmentId($url) {
		$attachment_id = 0;
		$dir = \wp_upload_dir();

		if(\strpos($url, $dir['baseurl'] . '/') !== false) { // Is URL in uploads directory?
			$file = \basename($url);
			$query_args = array(
				'post_type' => 'attachment',
				'post_status' => 'inherit',
				'fields' => 'ids',
				'meta_query' => array(
					array(
						'value' => $file,
						'compare' => 'LIKE',
						'key' => '_wp_attachment_metadata',
					),
				)
			);

			$query = new \WP_Query($query_args);

			if($query->have_posts()) {
				foreach($query->posts as $post_id) {
					$meta = \wp_get_attachment_metadata($post_id);
					$original_file = \basename($meta['file']);
					$cropped_image_files = \wp_list_pluck($meta['sizes'], 'file');

					if($original_file === $file || \in_array($file, $cropped_image_files)) {
						$attachment_id = $post_id;

						break;
					} // END if($original_file === $file || \in_array($file, $cropped_image_files))
				} // END foreach($query->posts as $post_id)
			} // END if($query->have_posts())
		} // END if(\strpos($url, $dir['baseurl'] . '/') !== false)

		return $attachment_id;
	} // END public static function getAttachmentId($url)

	/**
	 * Getting the cached URL for a remote image
	 *
	 * @param string $cacheType The subdirectory in the image cache filesystem
	 * @param string $remoteImageUrl The URL for the remote image
	 * @return string The cached Image URL
	 */
//	public static function getLocalCacheImageUriForRemoteImage($cacheType = null, $remoteImageUrl = null) {
	public function getLocalCacheImageUriForRemoteImage($cacheType = null, $remoteImageUrl = null) {
		$themeOptions = \get_option('yulai_theme_options', ThemeHelper::getInstance()->getThemeDefaultOptions());
		$returnValue = $remoteImageUrl;

		// Check if we should use image cache
		if(!empty($themeOptions['cache']['remote-image-cache'])) {
			$explodedImageUrl = \explode('/', $remoteImageUrl);
			$imageFilename = \end($explodedImageUrl);
			$cachedImage = CacheHelper::getInstance()->getImageCacheUri() . $cacheType . '/' . $imageFilename;

			// if we don't have the image cached already
			if(CacheHelper::getInstance()->checkCachedImage($cacheType, $imageFilename) === false) {
				/**
				 * Check if the content dir is writable and cache the image.
				 * Otherwise set the remote image as return value.
				 */
				if(\is_dir(CacheHelper::getInstance()->getImageCacheDir() . $cacheType) && \is_writable(CacheHelper::getInstance()->getImageCacheDir() . $cacheType)) {
					if(CacheHelper::getInstance()->cacheRemoteImageFile($cacheType, $remoteImageUrl) === true) {
						$returnValue = $cachedImage;
					} // END if(CacheHelper::cacheRemoteImageFile($cacheType, $remoteImageUrl) === true)
				} // END if(\is_dir(CacheHelper::vgetImageCacheDir() . $cacheType) && \is_writable(CacheHelper::getInstance()->getImageCacheDir() . $cacheType))
			} else {
				$returnValue = $cachedImage;
			} // END if(CacheHelper::getInstance()->checkCachedImage($cacheType, $imageName) === false)
		} // END if(!empty($themeOptions['cache']['remote-image-cache']))

		return $returnValue;
	} // END public static function getLocalCacheImageUri($cacheType = null, $remoteImageUrl = null)
} // END class ImageHelper
