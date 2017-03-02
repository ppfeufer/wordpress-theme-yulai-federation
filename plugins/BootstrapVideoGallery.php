<?php
/**
 * Bootstrap Video Gallery
 *
 * Pretty similar to the Bootstrap Image Gallery, but with videos
 * from Youtube and Vimeo. Since WordPress doesn't have an own video gallery
 * we can hijack as we do it with the Bootstrap Image Gallery plugin,
 * so we have to improvise here a bit.
 *
 * First we are going to set up a shortcode [videogallery id="1,2,3,4,5"]
 *
 * As you can see, this shortcode will be the overview of the videos.
 * the "id"-parameter is optinal and will be a comma separated list of pages
 * that are marked as video-page (will explain later). If no id's are given
 * it will show all video-pages as gallery.
 *
 * To mark a page as video-page we will simply add a metabox to the page
 * edit section in which you can tick a check box saying "Is video page" and
 * supply the link to the video in an input field. This plugin will take care
 * of the rest.
 *
 * So basically all you have to do is to create a page for the gallery overview
 * in which you put the shortcode, and a page for every video you want to have
 * in this gallery. Pretty simple, isn't it?
 *
 * I recommend to order the paged hierarchically.
 *		Videos
 *			Video 1
 *			Video 2
 *			Video 3
 *
 * This way the generated permalink will also be hierarchically,
 * which is nice for Google.
 *		http://yourpage.net/videos/
 *			http://yourpage.net/videos/video-1/
 *			http://yourpage.net/videos/video-2/
 *			http://yourpage.net/videos/video-3/
 *
 * Sneaky, huh?
 *
 * @author H.-Peter Pfeufer <dev@ppfeufer.de>
 */

namespace WordPress\Themes\YulaiFederation\Plugins;

use WordPress\Themes\YulaiFederation;

\defined('ABSPATH') or die();

class BootstrapVideoGallery {
	public function __construct() {
		$this->registerShortcode();
		$this->registerMetabox();
	} // END public function __construct()

	public function registerShortcode() {
		\add_shortcode('videogallery', array(
			$this,
			'shortcodeVideogallery'
		));
	} // END public function registerShortcode()

	public function shortcodeVideogallery($attributes) {
		$args = \shortcode_atts(
			array(
				'id' => '',
				'videolist' => '',
				'classes' => '',
				'per_page' => 12
			),
			$attributes
		);

		$id = $args['id'];
		$videoList = $args['videolist'];
		$classes = $args['classes'];
		$perPage = $args['per_page'];
		$idList = null;

		if(!empty($id)) {
			$idList = (\preg_match('/,( )/', $id)) ? \explode(',', $id) : array($id);
		} // END if(!empty($id))

		// loop through the pages and build the gallery code ....
		$uniqueID = \uniqid();
		$videoGalleryHtml = null;
		$videoGalleryHtml .= '<div class="gallery-row">';
		$videoGalleryHtml .= '<ul class="bootstrap-gallery bootstrap-video-gallery bootstrap-video-gallery-' . $uniqueID . ' clearfix">';

		if(empty($videoList)) {
			// assume we have a list of childpages
			$pageID = \get_queried_object_id();
			$videoPages = $this->getVideoPages($perPage);
			$pageChildren = \get_page_children($pageID, $videoPages->posts);

			if($pageChildren) {
				$childPages = $this->getVideoPagesFromChildren($pageChildren);
			} // END if($children)

			if($childPages) {
				foreach($childPages as $child) {
					$videoGalleryHtml .= '<li>';
					$videoGalleryHtml .= $child->yf_page_video_oEmbed_code;
					$videoGalleryHtml .= '<header><h2 class="video-gallery-title"><a href="' . \get_permalink($child->ID) . '">' . $child->post_title . '</a></h2></header>';

					if($child->post_content) {
						$videoGalleryHtml .= '<p>' . YulaiFederation\Helper\StringHelper::cutString($child->post_content, '140') . '</p>';
					} // END if($child->post_content)

					$videoGalleryHtml .= '</li>';
				} // END foreach($childPages as $child)

				\wp_reset_query();
			} else {
				$videoGalleryHtml = false;
			} // END if($childPages)
		} else {
			$videos = \explode(',', $videoList);
			$youtubePattern = '/https?:\/\/((m|www)\.)?youtube\.com\/watch.*/i';
			$vimeoPattern = '/https?:\/\/(.+\.)?vimeo\.com\/.*/i';

			$oEmbed = new \WP_oEmbed();
			foreach($videos as $video) {
				if(\preg_match($youtubePattern, $video) || \preg_match($vimeoPattern, $video)) {
					$provider = $oEmbed->get_provider($video);
					$videoData = $oEmbed->fetch($provider, $video);
					$videoGalleryHtml .= '<li>';
					$videoGalleryHtml .= $videoData->html;
					$videoGalleryHtml .= '<header><h2 class="video-gallery-title"><a href="' . $video . '" rel="external">' . $videoData->title . '</a></h2><span class="bootstrap-video-gallery-video-author small">' . sprintf(\__('&copy %1$s', 'yulai-federation'), $videoData->author_name) . ' (<a href="' . $videoData->author_url . '" rel=external">' . \__('Channel', 'yulai-federation') . '</a>)</span></header>';
					$videoGalleryHtml .= '</li>';
				} // END if(\preg_match($youtubePattern, $video) || \preg_match($vimeoPattern, $video))
			} // END foreach($videos as $video)
		} // END if(empty($videoList))

		$videoGalleryHtml .= '</ul>';
		$videoGalleryHtml .= '</div>';

		if(empty($classes)) {
			$classes = YulaiFederation\Helper\PostHelper::geLoopContentClasses();
		} // END if(empty($classes))

		$videoGalleryHtml .= '<script type="text/javascript">
								jQuery(document).ready(function() {
									jQuery("ul.bootstrap-video-gallery-' . $uniqueID . '").bootstrapGallery({
										"classes" : "' . $classes . '",
										"hasModal" : false
									});
								});
								</script>';

		if(isset($videoPages) && $videoPages->max_num_pages > 1) {
			$videoGalleryHtml .= '<nav id="nav-videogallery" class="navigation post-navigation clearfix" role="navigation">';
			$videoGalleryHtml .= '<h3 class="assistive-text">' . \__('Video Navigation', 'yulai-federation') . '</h3>';
			$videoGalleryHtml .= '<div class="nav-previous pull-left">';
			$videoGalleryHtml .= YulaiFederation\Helper\NavigationHelper::getNextPostsLink(\__('<span class="meta-nav">&larr;</span> Older Videos', 'yulai-federation'), 0, false, $videoPages);
			$videoGalleryHtml .= '</div>';
			$videoGalleryHtml .= '<div class="nav-next pull-right">';
			$videoGalleryHtml .= YulaiFederation\Helper\NavigationHelper::getPreviousPostsLink(\__('Newer Videos <span class="meta-nav">&rarr;</span>', 'yulai-federation'), false);
			$videoGalleryHtml .= '</div>';
			$videoGalleryHtml .= '</nav><!-- #nav-videogallery .navigation -->';
		} // END if($videoPages->max_num_pages > 1)

		return $videoGalleryHtml;
	} // END public function shortcodeVideogallery($attributes)

	public function registerMetabox() {
		\add_action('add_meta_boxes', array(
			$this,
			'metaboxVideopage'
		));

		\add_action('save_post', array(
			$this,
			'saveMetaboxData'
		));
	} // END function public function registerMetabox()

	public function metaboxVideopage() {
		\add_meta_box('yf-video-page-box', \__('Video Gallery Page?', 'yulai-federation'), array($this, 'renderVideopageMetabox'), 'page', 'side');
	} // END public function metaboxVideopage()

	public function renderVideopageMetabox($post) {
		$yf_page_is_video_gallery_page = \get_post_meta($post->ID, 'yf_page_is_video_gallery_page', true);
		$yf_page_is_video_only_list_in_parent = \get_post_meta($post->ID, 'yf_page_video_only_list_in_parent_gallery', true);
		$yf_page_video_url = \get_post_meta($post->ID, 'yf_page_video_url', true);
//		$yf_page_corp_eve_ID = \get_post_meta($post->ID, 'yf_page_corp_eve_ID', true);
		?>
		<label><strong><?php _e('Video Gallery Settings', 'yulai-federation'); ?></strong></label>
		<p class="checkbox-wrapper">
			<input id="yf_page_is_video_gallery_page" name="yf_page_is_video_gallery_page" type="checkbox" <?php \checked($yf_page_is_video_gallery_page); ?>>
			<label for="yf_page_is_video_gallery_page"><?php \_e('Is Video Gallery Page?', 'yulai-federation'); ?></label>
		</p>
		<p class="checkbox-wrapper">
			<input id="yf_page_video_only_list_in_parent_gallery" name="yf_page_video_only_list_in_parent_gallery" type="checkbox" <?php \checked($yf_page_is_video_only_list_in_parent); ?>>
			<label for="yf_page_video_only_list_in_parent_gallery"><?php \_e('Only list in it\'s parent gallery', 'yulai-federation'); ?></label>
		</p>
		<p class="checkbox-wrapper">
			<label for="yf_page_video_url"><?php _e('Video URL:', 'yulai-federation'); ?></label><br>
			<input id="yf_page_video_url" name="yf_page_video_url" type="text" value="<?php echo $yf_page_video_url; ?>">
		</p>
		<?php
		if(!empty($yf_page_video_url)) {
			?>
			<p class="checkbox-wrapper">
				<label><strong><?php \_e('Your Video', 'yulai-federation'); ?></strong></label>
				<br>
				<?php
				$oEmbed = \wp_oembed_get($yf_page_video_url);
				echo $oEmbed;
				?>
				<script type="text/javascript">
				jQuery(function($) {
					var $oEmbedVideos = $('#yf-video-page-box iframe[src*="youtube"], #yf-video-page-box iframe[src*="vimeo"]');
					$oEmbedVideos.each(function() {
						$(this).removeAttr('height').removeAttr('width').wrap('<div class="embed-video-container"></div>');
					});
				});
				</script>
			</p>
			<?php
		} // END if(!empty($yf_page_corp_eve_ID))

		\wp_nonce_field('save', '_yf_video_page_nonce');
	} // END public function renderVideopageMetabox()

	/**
	 * Save the setting
	 *
	 * @param int $postID
	 * @return boolean
	 */
	function saveMetaboxData($postID) {
		$postNonce = \filter_input(\INPUT_POST, '_yf_video_page_nonce');

		if(empty($postNonce) || !\wp_verify_nonce($postNonce, 'save')) {
			return false;
		} // END if(empty($postNonce) || !\wp_verify_nonce($postNonce, 'save'))

		if(!\current_user_can('edit_post', $postID)) {
			return false;
		} // END if(!current_user_can('edit_post', $postID))

		if(defined('DOING_AJAX')) {
			return false;
		} // END if(defined('DOING_AJAX'))

		\update_post_meta($postID, 'yf_page_video_url', \filter_input(\INPUT_POST, 'yf_page_video_url'));

		$isVideoPage = \filter_input(\INPUT_POST, 'yf_page_is_video_gallery_page') == "on";
		\update_post_meta($postID, 'yf_page_is_video_gallery_page', $isVideoPage);

		$onlyListForParent = \filter_input(\INPUT_POST, 'yf_page_video_only_list_in_parent_gallery') == "on";
		\update_post_meta($postID, 'yf_page_video_only_list_in_parent_gallery', $onlyListForParent);
	} // END function yf_corp_page_setting_save($postID)

	private function getVideoPages($postPerPage = 12) {
		global $paged;

		$queryArgs = array(
			'posts_per_page' => $postPerPage,
			'post_type' => 'page',
			'meta_key' => 'yf_page_is_video_gallery_page',
			'meta_value' => 1,
			'paged' => $paged
		);
		// Set up the objects needed

		$videoPages = new \WP_Query($queryArgs);

		return $videoPages;
	} // END private function getChildPages()

	private function getVideoPagesFromChildren($children) {
		if(!\is_array($children) || \count($children) === 0) {
			return false;
		} // END if(!is_array($children) || count($children) === 0)

		$videoPages = null;
		foreach($children as $id => $child) {
			$yf_page_is_video_gallery_page = \get_post_meta($child->ID, 'yf_page_is_video_gallery_page', true);
			$yf_page_is_video_only_list_in_parent = \get_post_meta($child->ID, 'yf_page_video_only_list_in_parent_gallery', true);
			$yf_page_video_url = \get_post_meta($child->ID, 'yf_page_video_url', true);

			if(isset($yf_page_is_video_gallery_page)) {
				$videoPages[$id] = $child;
				$videoPages[$id]->yf_page_is_video_gallery_page = $yf_page_is_video_gallery_page;
				$videoPages[$id]->yf_page_video_only_list_in_parent_gallery = $yf_page_is_video_only_list_in_parent;
				$videoPages[$id]->yf_page_video_url = $yf_page_video_url;
				$videoPages[$id]->yf_page_video_oEmbed_code = \wp_oembed_get($yf_page_video_url);
			} // END if(isset($yf_page_is_video_gallery_page))
		} // END foreach($children as $id => $child)

		return $videoPages;
	} // END private function getVideoPagesFromChildren($children)
} // END class BootstrapVideoGallery
