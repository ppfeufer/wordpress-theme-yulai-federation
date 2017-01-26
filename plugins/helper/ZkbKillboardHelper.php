<?php
/**
 * Killboard Widget
 */

namespace WordPress\Themes\EveOnline\Plugins\Helper;

use WordPress\Themes\EveOnline;

class ZkbKillboardHelper {
	private $plugin = null;
	private $pluginSettings = null;
	private $themeSettings = null;
	private $eveApi = null;

	public $db = null;

	public function __construct() {
		$this->plugin = new EveOnline\Plugins\Killboard;
		$this->eveApi = new EveOnline\Helper\EveApiHelper;

		$this->pluginSettings = \get_option('eve_theme_killboard_plugin_options', $this->plugin->getDefaultPluginOptions());
		$this->themeSettings = \get_option('eve_theme_options', EveOnline\eve_get_options_default());
	} // END public function __construct()

	public function getKillList($count) {
		$query = 'SELECT kll.kll_id,
						kll.kll_isk_loss AS isk_loss,
						kll.kll_ship_id AS shp_id,
						plt.plt_name,
						plt.plt_externalid,
						sys.sys_name,
						fbplt.plt_name AS fbplt_name,
						inv.typeName AS shp_name,
						sys.sys_sec
					FROM kb3_kills kll
					INNER JOIN kb3_invtypes inv ON (inv.typeID = kll.kll_ship_id)
					INNER JOIN kb3_pilots plt ON (plt.plt_id = kll.kll_victim_id)
					INNER JOIN kb3_pilots fbplt ON (fbplt.plt_id = kll.kll_fb_plt_id)
					INNER JOIN kb3_systems sys ON (sys.sys_id = kll.kll_system_id)
					ORDER BY kll_timestamp DESC
					LIMIT 0, ' . $count . ';';
		$resultLastKills = $this->db->get_results($query, \OBJECT);

		// get the number of involved pilots
		foreach($resultLastKills as &$kill) {
			$kill->involved = $this->db->get_var('SELECT count(*) ipc FROM kb3_inv_detail WHERE ind_kll_id =' . $kill->kll_id . ';');
			$kill->killboardLink = $this->getKillboardLinkToKill($kill->kll_id);
			$kill->isk_loss_formatted = $this->sanitizeIskLoss($kill->isk_loss);

			/**
			 * Overwrite Victim Image if its a Citadel
			 */
			if(\in_array($kill->shp_name, $this->plugin->getStructureNames())) {
				$kill->victimImage = $this->getStructureImage($kill->shp_id);
			} else {
				$kill->victimImage = $this->getVictimImage($kill->plt_name, $kill->shp_id);
			} // END if(\in_array($kill->shp_name, $citadelNames))
		} // END foreach($resultLastKills as &$kill)

		return $resultLastKills;
	} // END public function getKillList($count)

	private function getVictimImage($victimName, $shipID, $size = 512) {
		if(\preg_match('/Control Tower/', $victimName)) {
			$victimImage = "http://image.eveonline.com/Render/" . $shipID . "_" . $size . ".png";
		} else {
			$victimImage = $this->eveApi->getCharacterImageByName($victimName, true, $size);
		} // END if(preg_match('/Control Tower/', $kill->shp_name))

		return $victimImage;
	} // END private function getVictimImage($victimName, $shipID, $size = 512)

	private function getStructureImage($shipID, $size = 512) {
		$victimImage = "http://image.eveonline.com/Render/" . $shipID . "_" . $size . ".png";

		return $victimImage;
	} // END private function getCitadelImage($shipID, $size = 512)

	private function getKillboardLinkToKill($killID) {
		return $this->killboardUri . '?a=kill_detail&kll_id=' . $killID;
	} // END private function getKillboardLinkToKill($killID)

	private function sanitizeIskLoss($isk) {
		if($isk < 1000) {
			$isk = \number_format($isk, 0);
		} elseif(($isk/1000) < 1000) {
			$isk = \number_format(($isk/1000), 0) . 'K';
		} elseif(($isk/1000/1000) < 1000) {
			$isk = \number_format(($isk/1000/1000), 0) . 'M';
		} else {
			$isk = \number_format(($isk/1000/1000/1000), 0, '.', ',') . 'B';
		} // END if($isk < 1000)

		return $isk;
	} // END private function sanitizeIskLoss($isk)
} // END class ZkbKillboardHelper