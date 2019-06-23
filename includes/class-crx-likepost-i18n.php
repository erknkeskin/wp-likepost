<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://erkankeskin.com.tr
 * @since      1.0.0
 *
 * @package    Crx_Likepost
 * @subpackage Crx_Likepost/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Crx_Likepost
 * @subpackage Crx_Likepost/includes
 * @author     Erkan Keskin <info@erkankeskin.com.tr>
 */
class Crx_Likepost_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'crx-likepost',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
