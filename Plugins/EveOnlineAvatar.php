<?php

namespace WordPress\Themes\YulaiFederation\Plugins;

use WordPress\Themes\YulaiFederation;

class EveOnlineAvatar {
	/**
	 * EVE API
	 *
	 * @var \WordPress\Themes\YulaiFederation\Helper\EsiHelper
	 */
	private $eveApi = null;

	/**
	 * constructor
	 */
	public function __construct() {
		$this->eveApi = YulaiFederation\Helper\EsiHelper::getInstance();

		$this->init();
	} // END public function __construct()

	/**
	 * init
	 */
	public function init() {
		\add_filter('get_avatar', [$this, 'eveCharacterAvatar'], 1, 5);
		\add_filter('bp_core_fetch_avatar', [$this, 'fetchEveCharacterAvatar'], 1, 2);
		\add_filter('bp_core_fetch_avatar_url', [$this, 'fetchEveCharacterAvatar'], 1, 2);
		\add_filter('user_profile_picture_description', \create_function('$desc', 'return "' . \__('If you set your nickname to your pilot\'s name, you EVE avatar will be used here.', 'yulai-federation') . '";'));
	} // END public function init()

	/**
	 * Setting the EVE avatar if there is one
	 *
	 * @param string $content
	 * @param object $idOrEmail
	 * @return string
	 */
	public function eveCharacterAvatar($content, $idOrEmail) {
		$returnValue = $content;

		if(\preg_match("/gravatar.com\/avatar/", $content)) {
			// get user login
			if(\is_numeric($idOrEmail)) {
				$id = (int) $idOrEmail;
				$user = \get_userdata($id);
			} elseif(\is_object($idOrEmail)) {
				if(!empty($idOrEmail->user_id)) {
					$id = (int) $idOrEmail->user_id;
					$user = \get_userdata($id);
				} elseif(!empty($idOrEmail->comment_author_email)) {
					// Let's see if we can find an EVE Online Avatar
					if(!empty($idOrEmail->comment_author)) {
						$eveImage = $this->eveApi->getCharacterImageByName($idOrEmail->comment_author, false);

						if(!\is_null($eveImage)) {
							return $eveImage;
						} // END if(!\is_null($eveImage))
					} // END if(!empty($id_or_email->comment_author))

					// Nope, no EVE Online Avatar available
					return $content;
				} // END if(!empty($id_or_email->user_id))
			} else {
				$user = \get_user_by('email', $idOrEmail);
			} // END if(\is_numeric($id_or_email))

			if(!empty($user->nickname)) {
				$eveImage = $this->eveApi->getCharacterImageByName($user->nickname, false);
			} // END if(!empty($user->nickname))

			if(!empty($eveImage)) {
				$returnValue = $eveImage;
			} // END if(!empty($eveImage))
		} // END if(\preg_match("/gravatar.com\/avatar/", $content))

		return $returnValue;
	} // END public function eveCharacterAvatar($content, $id_or_email)

	/**
	 * Getting the EVE avatar if there is one
	 *
	 * @param string $content
	 * @param array $params
	 * @return string
	 */
	public function fetchEveCharacterAvatar($content, array $params) {
		$returnValue = $content;

		if(\is_array($params) && $params['object'] == 'user' ) {
			$returnValue = $this->eveCharacterAvatar($content, $params['item_id']);
		} // END if(\is_array($params) && $params['object'] == 'user' )

		return $returnValue;
	} // END public function fetchEveCharacterAvatar($content, $params)
} // END class EveOnlineAvatar
