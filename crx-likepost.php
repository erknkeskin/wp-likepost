<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://erkankeskin.com.tr
 * @since             1.0.0
 * @package           Crx_Likepost
 *
 * @wordpress-plugin
 * Plugin Name:       CRX Likepost
 * Plugin URI:        https://erkankeskin.com.tr
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Erkan Keskin
 * Author URI:        https://erkankeskin.com.tr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       crx-likepost
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CRX_LIKEPOST_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-crx-likepost-activator.php
 */
function activate_crx_likepost() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-crx-likepost-activator.php';
	Crx_Likepost_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-crx-likepost-deactivator.php
 */
function deactivate_crx_likepost() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-crx-likepost-deactivator.php';
	Crx_Likepost_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_crx_likepost' );
register_deactivation_hook( __FILE__, 'deactivate_crx_likepost' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-crx-likepost.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_crx_likepost() {

	$plugin = new Crx_Likepost();
	$plugin->run();

	global $wpdb;
	$r = $wpdb->get_results("SHOW COLUMNS FROM `{$wpdb->prefix}posts` LIKE 'crx_post_like'");
	if (empty($r)) {
		// post table add crx_post_like field
		$wpdb->query("ALTER TABLE `{$wpdb->prefix}posts` ADD `crx_post_like` INT DEFAULT '0'");
	}

	// like data table create
	$c = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}likepost_datas(
		id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		user_ip VARCHAR(25),
		post_id BIGINT(20)
	) ENGINE=MYISAM";

	$wpdb->query($c);

}
run_crx_likepost();
