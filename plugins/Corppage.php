<?php
/**
 * Corppage Plugin
 */

namespace WordPress\Themes\YulaiFederation\Plugins;

use WordPress\Themes\YulaiFederation;

\defined('ABSPATH') or die();

class Corppage {
	private $eveApi = null;

	public function __construct() {
		$this->eveApi = new YulaiFederation\Helper\EveApiHelper;

		$this->registerMetaBoxes();
		$this->registerShortcodes();
	} // END public function __construct()

	public function registerShortcodes() {
		\add_shortcode('corplist', array(
			$this,
			'shortcodeCorplist'
		));
	} // END public function registerShortcodes()

	public function shortcodeCorplist($attributes) {
		$args = \shortcode_atts(
			array(
				'type' => 'boxes'
			),
			$attributes
		);

		/**
		 * Not used at this moment
		 */
//		$type = $args['type'];

		$corpPages = $this->getCorporationPages();
		$corplistHTML = null;

		if($corpPages !== false) {
			$corplistHTML = $this->getCorporationPagesLoop($corpPages);
		} // END if($corpPages !== false)

		return $corplistHTML;
	} // END public function shortcodeCorplist($attributes)

	private function getCorporationPagesLoop($corpPages) {
		$uniqueID = \uniqid();
		$corplistHTML = '<div class="gallery-row row">';
		$corplistHTML .= '<ul class="bootstrap-gallery bootstrap-corporationlist bootstrap-corporationlist-' . $uniqueID . ' clearfix">';

		foreach($corpPages as $page) {
			if(!empty($page->post_content)) {
				$corplistHTML .= $this->getCorporationPageLoopItem($page);
			} // END if(!empty($page->post_content))
		} // END foreach($corpPages as $page)

		$corplistHTML .= '</ul>';
		$corplistHTML .= '</div>';

		$corplistHTML .= '<script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery("ul.bootstrap-corporationlist-' . $uniqueID . '").bootstrapGallery({
									"classes" : "col-lg-3 col-md-4 col-sm-6 col-xs-12",
									"hasModal" : false
								});
							});
							</script>';

		return $corplistHTML;
	}

	private function getCorporationPageLoopItem($page) {
		$corpID = \get_post_meta($page->ID, 'yf_page_corp_eve_ID', true);

		$corpLogo = YulaiFederation\Helper\ImageHelper::getLocalCacheImageUriForRemoteImage('corporation', $this->eveApi->getImageServerEndpoint('corporation') . $corpID . '_256.png');

		$corplistHTML = '<li>';
		$corplistHTML .= '<figure><a href="' . \get_permalink($page->ID) . '"><img src="' . $corpLogo . '" alt="' . $page->post_title . '"></a></figure>';
		$corplistHTML .= '<header><h2 class="corporationlist-title"><a href="' . \get_permalink($page->ID) . '">' . $page->post_title . '</a></h2></header>';

		$corplistHTML .= '<p>' . YulaiFederation\Helper\StringHelper::cutString(strip_shortcodes($page->post_content), '200') . '</p>';

		$corplistHTML .= '</li>';

		return $corplistHTML;
	}

	/**
	 * Getting the corporation pages from our DB
	 *
	 * @return object or boolean
	 */
	private function getCorporationPages() {
		$returnValue = false;

		$result = new \WP_Query(array(
			'post_type' => 'page',
			'meta_key' => 'yf_page_is_corp_page',
			'meta_value' => 1,
			'posts_per_page' => -1,
			'orderby' => 'post_title',
			'order' => 'ASC'
		));

		if($result) {
			$returnValue =  $result->posts;
		} // END if($result)

		return $returnValue;
	} // END public function getCorporationPages()

	public function registerMetaBoxes() {
		\add_action('add_meta_boxes', array($this, 'addMetaBox'));
		\add_action('save_post', array($this, 'savePageSettings'));
	} // END public function registerMetaBoxes()

	public function addMetaBox() {
		\add_meta_box('yf-corp-page-box', __('Corp Page?', 'yulai-federation'), array($this, 'renderMetaBox'), 'page', 'side');
	} // END public function addMetaBox()

	public function renderMetaBox($post) {
		$yf_page_is_corp_page = \get_post_meta($post->ID, 'yf_page_is_corp_page', true);
		$yf_page_corp_name = \get_post_meta($post->ID, 'yf_page_corp_name', true);
		$yf_page_corp_eve_ID = \get_post_meta($post->ID, 'yf_page_corp_eve_ID', true);
		?>
		<label><strong><?php _e('Corp Page Settings', 'yulai-federation'); ?></strong></label>
		<p class="checkbox-wrapper">
			<input id="yf_page_is_corp_page" name="yf_page_is_corp_page" type="checkbox" <?php \checked($yf_page_is_corp_page); ?>>
			<label for="yf_page_is_corp_page"><?php _e('Is Corp Page?', 'yulai-federation'); ?></label>
		</p>
		<p class="checkbox-wrapper">
			<label for="yf_page_corp_name"><?php _e('Corporation Name:', 'yulai-federation'); ?></label><br>
			<input id="yf_page_corp_name" name="yf_page_corp_name" type="text" value="<?php echo $yf_page_corp_name; ?>">
		</p>
		<?php
		if(!empty($yf_page_corp_eve_ID)) {
			?>
			<p class="checkbox-wrapper">
				<label for="yf_page_corp_ID"><?php _e('Corporation ID', 'yulai-federation'); ?></label>
				<input id="yf_page_corp_ID" name="yf_page_corp_ID" type="text" value="<?php echo \esc_html($yf_page_corp_eve_ID); ?>" disabled>
			</p>
			<p class="checkbox-wrapper">
				<label><strong><?php _e('Corporation Logo', 'yulai-federation'); ?></strong></label>
				<br>
				<?php
				$corpLogoPath = YulaiFederation\Helper\ImageHelper::getLocalCacheImageUriForRemoteImage('corporation', $this->eveApi->getImageServerEndpoint('corporation') . $yf_page_corp_eve_ID . '_256.png');
				?>
				<img src="<?php echo $corpLogoPath; ?>" alt="<?php echo $yf_page_corp_name; ?>">
			</p>
			<?php
		} // END if(!empty($yf_page_corp_eve_ID))

		\wp_nonce_field('save', '_yf_corp_page_nonce');
	} // END public function renderMetaBox($post)

	public function savePageSettings($postID) {
		$postNonce = \filter_input(\INPUT_POST, '_yf_corp_page_nonce');

		if(empty($postNonce) || !\wp_verify_nonce($postNonce, 'save')) {
			return false;
		} // END if(empty($postNonce) || !\wp_verify_nonce($postNonce, 'save'))

		if(!\current_user_can('edit_post', $postID)) {
			return false;
		} // END if(!\current_user_can('edit_post', $postID))

		if(defined('DOING_AJAX')) {
			return false;
		} // END if(defined('DOING_AJAX'))

		\update_post_meta($postID, 'yf_page_corp_name', \filter_input(\INPUT_POST, 'yf_page_corp_name'));

		$corpID = $this->eveApi->getEveIdFromName(\stripslashes(\filter_input(\INPUT_POST, 'yf_page_corp_name')));
		\update_post_meta($postID, 'yf_page_corp_eve_ID', \esc_html($corpID));

		$isCorpPage = \filter_input(INPUT_POST, 'yf_page_is_corp_page') == "on";
		\update_post_meta($postID, 'yf_page_is_corp_page', $isCorpPage);(\stripslashes(\filter_input(\INPUT_POST, 'yf_page_corp_name')));

		\update_post_meta($postID, 'yf_page_corp_eve_ID', \esc_html($corpID));
	} // END public function savePageSettings($postID)
} // END class Corppage
