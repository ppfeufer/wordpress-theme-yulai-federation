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
	private $string = null;

	public function __construct() {
		$this->string = new YulaiFederation\Helper\String;

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
				'id' => ''
			),
			$attributes
		);
		$id = $args['id'];
		$idList = null;

		if(!empty($id)) {
			$idList = (\preg_match('/,( )/', $id)) ? \explode(',', $id) : array($id);
		} // END if(!empty($id))

		$childPages = $this->getChildPages();

		// loop through the pages and build the gallery code ....
		if($childPages) {
			$uniqueID = \uniqid();
			$videoGalleryHtml = null;
			$videoGalleryHtml .= '<div class="gallery-row">';
			$videoGalleryHtml .= '<ul class="bootstrap-gallery bootstrap-video-gallery bootstrap-video-gallery-' . $uniqueID . ' clearfix">';

			foreach($childPages as $child) {
				$videoGalleryHtml .= '<li>';
				$videoGalleryHtml .= $child->yf_page_video_oEmbed_code;
				$videoGalleryHtml .= '<header><h4><a href="' . \get_permalink($child->ID) . '">' . $child->post_title . '</a></h4></header>';

				if($child->post_content) {
					$videoGalleryHtml .= '<p>' . $this->string->cutString($child->post_content, '140') . '</p>';
				} // END if($child->post_content)

				$videoGalleryHtml .= '</li>';
			} // END foreach($childPages as $child)

			$videoGalleryHtml .= '</ul>';
			$videoGalleryHtml .= '</div>';

			$videoGalleryHtml .= '<script type="text/javascript">
									jQuery(document).ready(function() {
										jQuery("ul.bootstrap-video-gallery-' . $uniqueID . '").bootstrapGallery({
											"classes" : "' . YulaiFederation\yf_get_loopContentClasses() . '",
											"hasModal" : false
										});
									});
									</script>';

			return $videoGalleryHtml;
		} // END if($childPages)

		return false;
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
		if(empty($_POST['_yf_video_page_nonce']) || !\wp_verify_nonce($_POST['_yf_video_page_nonce'], 'save')) {
			return false;
		} // END if(empty($_POST['_yf_video_page_nonce']) || !wp_verify_nonce($_POST['_yf_video_page_nonce'], 'save'))

		if(!\current_user_can('edit_post', $postID)) {
			return false;
		} // END if(!current_user_can('edit_post', $postID))

		if(defined('DOING_AJAX')) {
			return false;
		} // END if(defined('DOING_AJAX'))

		\update_post_meta($postID, 'yf_page_video_url', $_POST['yf_page_video_url']);

		$isVideoPage = \filter_input(\INPUT_POST, 'yf_page_is_video_gallery_page') == "on";
		\update_post_meta($postID, 'yf_page_is_video_gallery_page', $isVideoPage);

		$onlyListForParent = \filter_input(\INPUT_POST, 'yf_page_video_only_list_in_parent_gallery') == "on";
		\update_post_meta($postID, 'yf_page_video_only_list_in_parent_gallery', $onlyListForParent);
	} // END function yf_corp_page_setting_save($postID)

	private function getChildPages() {
		$pageObject = \get_queried_object();
		$pageID = \get_queried_object_id();

		$pageChildren = null;
		// Set up the objects needed
		$wpQuery = new \WP_Query();

		// Filter through all pages and find Portfolio's children
		$children = \get_page_children($pageID, $wpQuery->query(array(
			'posts_per_page' => -1,
			'post_type' => 'page',
			'meta_key' => 'yf_page_is_video_gallery_page',
			'meta_value' => 1,
		)));

		if($children) {
			$videoGalleryChildren = $this->getVideoPagesFromChildren($children);

			return $videoGalleryChildren;
		} // END if($children)

		return false;
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

// Start the show ...
new BootstrapVideoGallery();