<?php
/**
 * Corppage Plugin
 */

namespace WordPress\Themes\YulaiFederation\Plugins;

use WordPress\Themes\YulaiFederation;

\defined('ABSPATH') or die();

class Corppage {
    /**
     * EVE API
     *
     * @var \WordPress\Themes\YulaiFederation\Helper\EsiHelper
     */
    private $eveApi = null;

    public function __construct() {
        $this->eveApi = YulaiFederation\Helper\EsiHelper::getInstance();

        $this->registerMetaBoxes();
        $this->registerShortcodes();
    }

    public function registerShortcodes() {
        \add_shortcode('corplist', [
            $this,
            'shortcodeCorplist'
        ]);
    }

    public function shortcodeCorplist($attributes) {
        $args = \shortcode_atts([
            'type' => 'boxes'
            ], $attributes);

        /**
         * Not used at this moment
         */
//        $type = $args['type'];

        $corpPages = $this->getCorporationPages();
        $corplistHTML = null;

        if($corpPages !== false) {
            $corplistHTML = $this->getCorporationPagesLoop($corpPages);
        } // END if($corpPages !== false)

        return $corplistHTML;
    }

    private function getCorporationPagesLoop($corpPages) {
        $uniqueID = \uniqid();
        $corplistHTML = '<div class="gallery-row row">';
        $corplistHTML .= '<ul class="bootstrap-gallery bootstrap-corporationlist bootstrap-corporationlist-' . $uniqueID . ' clearfix">';

        foreach($corpPages as $page) {
            if(!empty($page->post_content)) {
                $corplistHTML .= $this->getCorporationPageLoopItem($page);
            }
        }

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

        $corpLogo = \sprintf(
            $this->eveApi->getImageServerEndpoint('corporation') . '?size=256',
            $corpID
        );

        $corplistHTML = '<li>';
        $corplistHTML .= '<figure><a href="' . \get_permalink($page->ID) . '"><img src="' . $corpLogo . '" alt="' . $page->post_title . '"></a></figure>';
        $corplistHTML .= '<header><h2 class="corporationlist-title"><a href="' . \get_permalink($page->ID) . '">' . $page->post_title . '</a></h2></header>';
        $corplistHTML .= '<p>' . YulaiFederation\Helper\StringHelper::getInstance()->cutString(\strip_shortcodes($page->post_content), '200') . '</p>';
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

        $result = new \WP_Query([
            'post_type' => 'page',
            'meta_key' => 'yf_page_is_corp_page',
            'meta_value' => 1,
            'posts_per_page' => -1,
            'orderby' => 'post_title',
            'order' => 'ASC'
        ]);

        if($result) {
            $returnValue = $result->posts;
        }

        return $returnValue;
    }

    public function registerMetaBoxes() {
        \add_action('add_meta_boxes', [$this, 'addMetaBox']);
        \add_action('save_post', [$this, 'savePageSettings']);
    }

    public function addMetaBox() {
        \add_meta_box('yf-corp-page-box', __('Corp Page?', 'yulai-federation'), [$this, 'renderMetaBox'], 'page', 'side');
    }

    public function renderMetaBox($post) {
        $isCorpPage = \get_post_meta($post->ID, 'yf_page_is_corp_page', true);
        $showCorpLogo = \get_post_meta($post->ID, 'yf_page_show_corp_info', true);
        $corpName = \get_post_meta($post->ID, 'yf_page_corp_name', true);
        $corpID = \get_post_meta($post->ID, 'yf_page_corp_eve_ID', true);
        ?>
        <label><strong><?php _e('Corp Page Settings', 'yulai-federation'); ?></strong></label>
        <p class="checkbox-wrapper">
            <input id="yf_page_is_corp_page" name="yf_page_is_corp_page" type="checkbox" <?php \checked($isCorpPage); ?>>
            <label for="yf_page_is_corp_page"><?php _e('Is Corp Page?', 'yulai-federation'); ?></label>
        </p>
        <p class="checkbox-wrapper">
            <input id="yf_page_show_corp_info" name="yf_page_show_corp_info" type="checkbox" <?php \checked($showCorpLogo); ?>>
            <label for="yf_page_show_corp_info"><?php \_e('Show Corp Information at the beginning of your page\'s content?', 'yulai-federation'); ?></label>
        </p>
        <p class="checkbox-wrapper">
            <label for="yf_page_corp_name"><?php _e('Corporation Name:', 'yulai-federation'); ?></label><br>
            <input id="yf_page_corp_name" name="yf_page_corp_name" type="text" value="<?php echo $corpName; ?>">
        </p>
        <?php
        if(!empty($corpID)) {
            ?>
            <p class="checkbox-wrapper">
                <label for="yf_page_corp_ID"><?php _e('Corporation ID', 'yulai-federation'); ?></label>
                <input id="yf_page_corp_ID" name="yf_page_corp_ID" type="text" value="<?php echo \esc_html($corpID); ?>" disabled>
            </p>
            <p class="checkbox-wrapper">
                <label><strong><?php _e('Corporation Logo', 'yulai-federation'); ?></strong></label>
                <br>
                <?php
                $corpLogoPath = \sprintf(
                    $this->eveApi->getImageServerEndpoint('corporation') . '?size=256',
                    $corpID
                );
                ?>
                <img src="<?php echo $corpLogoPath; ?>" alt="<?php echo $corpName; ?>">
            </p>
            <?php
        }

        \wp_nonce_field('save', '_yf_corp_page_nonce');
    }

    public function savePageSettings($postID) {
        $postNonce = \filter_input(\INPUT_POST, '_yf_corp_page_nonce');

        if(empty($postNonce) || !\wp_verify_nonce($postNonce, 'save')) {
            return false;
        }

        if(!\current_user_can('edit_post', $postID)) {
            return false;
        }

        if(\defined('DOING_AJAX')) {
            return false;
        }

        $isCorpPage = \filter_input(\INPUT_POST, 'yf_page_is_corp_page') === 'on';
        $showCorpInfo = '';
        $corpName = '';
        $corpID = '';

        /**
         * only if we really have a corp page ....
         */
        if(!empty($isCorpPage)) {
            $showCorpInfo = \filter_input(\INPUT_POST, 'yf_page_show_corp_info') === 'on';
            $corpName = \filter_input(\INPUT_POST, 'yf_page_corp_name');
            $corpID = $this->eveApi->getEveIdFromName(\stripslashes(\filter_input(\INPUT_POST, 'yf_page_corp_name')), 'corporation');
        }

        \update_post_meta($postID, 'yf_page_corp_name', $corpName);
        \update_post_meta($postID, 'yf_page_is_corp_page', $isCorpPage);
        \update_post_meta($postID, 'yf_page_show_corp_info', $showCorpInfo);
        \update_post_meta($postID, 'yf_page_corp_eve_ID', $corpID);
    }

    public static function getCorprationLogo($corpPageID) {
        $corpName = \get_post_meta($corpPageID, 'yf_page_corp_name', true);
        $corpID = \get_post_meta($corpPageID, 'yf_page_corp_eve_ID', true);

        $imagePath = \sprintf(
            $this->eveApi->getImageServerEndpoint('corporation') . '?size=128',
            $corpID
        );

        if($imagePath !== false) {
            $html = '<div class="eve-corp-page-corp-logo eve-image eve-corporation-logo-container"><figure><img src="' . $imagePath . '" class="eve-corporation-logo" alt="' . \esc_html($corpName) . '" width="128">';
            $html .= '</figure></div>';

            return $html;
        } // END if($imagePath !== false)

        return false;
    }

//    public static function getCorporationInformations($corpPageID) {
//        $eveApi = YulaiFederation\Helper\EsiHelper::getInstance();
//
//        $corpName = \get_post_meta($corpPageID, 'yf_page_corp_name', true);
//        $corpID = \get_post_meta($corpPageID, 'yf_page_corp_eve_ID', true);
//        $corpInfo = \get_post_meta($corpPageID, 'yf_page_corp_informations', true);
//        $corpInfoCacheTime = \get_post_meta($corpPageID, 'yf_page_corp_informations_cache_time', true);
//        $now = \mktime();
//
//        if($now - $corpInfoCacheTime > 3600 * 24) {
//
//        }
//    }
}
