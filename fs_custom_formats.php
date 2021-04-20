<?php
/**
 * @package FS Custom Formats
 * @version 1.0.1
 */
/*
Plugin Name: FS Custom Formats
Plugin URI: http://wordpress.org/plugins/
Description: This plugin is powered by Faire-Savoir. It allows to use Custom Formats.
Author: Faire Savoir
Version: 1.0.1
Author URI: http://www.faire-savoir.com
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('Fs_custom_formats') ) :

class Fs_custom_formats {


	/** @var string The plugin version number */
	var $version = '1.0.1';

	var $plugin_id = 'fs_custom_formats';
	var $plugin_name = 'FS - Custom Formats';

	function __construct(){
		$path = plugin_dir_path( __FILE__ );
		define('FS_CUSTOM_FORMATS_PLUGIN_PATH',$path);

		$plugins_dir = plugin_dir_path( __DIR__ );
		if ( file_exists($plugins_dir.'plugin-update-checker/plugin-update-checker.php') ) {
			require $plugins_dir.'plugin-update-checker/plugin-update-checker.php';
			$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
				'https://github.com/Faire-savoir/'.$this->plugin_id,
				__FILE__,
				$this->plugin_id
			);
			
			$myUpdateChecker->getVcsApi()->enableReleaseAssets();

			add_filter('puc_request_info_result-'.$this->plugin_id,['tis_wp_core','puc_modify_plugin_render']);
			add_filter('puc_view_details_link_position-'.$this->plugin_id,['tis_wp_core','puc_modify_link_position']);
		}

		add_action('init', [$this, 'init_plugin']);

	}

	function init_plugin(){
		// We include all format one by one.

		// init field
		include_once(FS_CUSTOM_FORMATS_PLUGIN_PATH.'formats/helper.php');

		// text format
		include_once(FS_CUSTOM_FORMATS_PLUGIN_PATH.'formats/text.php');

		// media format
		include_once(FS_CUSTOM_FORMATS_PLUGIN_PATH.'formats/media.php');

		// list format
		include_once(FS_CUSTOM_FORMATS_PLUGIN_PATH.'formats/list.php');

		// social format
		include_once(FS_CUSTOM_FORMATS_PLUGIN_PATH.'formats/social.php');

		// date format
		include_once(FS_CUSTOM_FORMATS_PLUGIN_PATH.'formats/date.php');
	}

}

new Fs_custom_formats();

endif; // class_exists check

