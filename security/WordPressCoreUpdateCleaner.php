<?php
/**
 * Security
 *
 * Removing files that are not needed form website root
 */

namespace WordPress\Themes\YulaiFederation\Security;

\defined('ABSPATH') or die();

class WordPressCoreUpdateCleaner {
	/**
	 * Sets up hooks, actions and filters that the plugin responds to.
	 *
	 * @since 1.0
	 */
	function __construct() {
//		\add_action('admin_init', array($this, 'init'));
		\add_action('_core_updated_successfully', array($this, 'updateCleaner'), 0, 1);
		\add_action('core_upgrade_preamble', array($this, 'updateCleaner'));
		\add_action('upgrader_pre_install', array($this, 'updateCleaner'));
		\add_action('upgrader_post_install', array($this, 'updateCleaner'));
	} // END function __construct()

	/**
	 * Sets up plugin translations.
	 *
	 * @since 1.0
	 */
	function init() {
		// Load plugin translations
//		load_plugin_textdomain('wp-core-update-cleaner', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	} // END function init()

	/**
	 * Performs the update cleaning.
	 *
	 * This function removes the unwanted files when WordPress is updated. The
	 * cleaning is performed on core update and core re-install. The function removes
	 * wp-config-sample.php, readme.html and localized versions of the readme and
	 * license files. If the files are removed successfully, the plugin outputs
	 * a response message to the core update screen letting you know which files
	 * that were removed.
	 *
	 * @since 1.0
	 * @param string New version of updated WordPress.
	 */
	function updateCleaner($new_version) {
		global $pagenow, $action;

		if('update-core.php' !== $pagenow) {
			return;
		} // END if('update-core.php' !== $pagenow)

		if('do-core-upgrade' !== $action && 'do-core-reinstall' !== $action) {
			return;
		} // END if('do-core-upgrade' !== $action && 'do-core-reinstall' !== $action)

		// Remove license, readme files
		$remove_files = array(
			'license.txt',
			'licens.html',
			'licenza.html',
			'licencia.txt',
			'licenc.txt',
			'licencia-sk_SK.txt',
			'licens-sv_SE.txt',
			'liesmich.html',
			'LEGGIMI.txt',
			'lisenssi.html',
			'olvasdel.html',
			'readme.html',
			'readme-ja.html',
			'wp-config-sample.php'
		);

		foreach($remove_files as $file) {
			if(\file_exists(\ABSPATH . $file)) {
				if(\unlink(\ABSPATH . $file)) {
					\show_message(\__('Removing', 'yulai-federation') . ' ' . $file . '...');
				} // END if(\unlink(\ABSPATH . $file))
			} // END if(\file_exists(\ABSPATH . $file))
		} // END foreach($remove_files as $file)

		// Load the updated default text localization domain for new strings
		\load_default_textdomain();

		// See do_core_upgrade()
		\show_message(\__('WordPress updated successfully') . '.');
		\show_message('<span>' . \sprintf(\__('Welcome to WordPress %1$s. <a href="%2$s">Learn more</a>.'), $new_version, \esc_url(\self_admin_url('about.php?updated'))) . '</span>');
		echo '</div>';

		// Include admin-footer.php and exit
		include(\ABSPATH . 'wp-admin/admin-footer.php');

		exit();
	} // END function updateCleaner($new_version)
} // END class WordPressCoreUpdateCleaner


if(\is_admin()) {
	new WordPressCoreUpdateCleaner();
} // END if(\is_admin())