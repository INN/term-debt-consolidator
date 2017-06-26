<?php
/**
 * Plugin Name: Term Debt Consolidator
 * Plugin URI: TKTK
 * Description: This plugin will look through your tags and categories to suggest similar terms that might be consolidated.
 * Author: INN
 * Version: 0.1.0
 * Author URI: http://nerds.inn.org/
 * License: GPLv2
 */

/**
 * Plugin set up
 */
function tdc_init() {
	define('TDC_PLUGIN_FILE', __FILE__);
	define('TDC_PLUGIN_DIR_URI', plugins_url(basename(__DIR__), __DIR__));
	define('TDC_PLUGIN_DIR', __DIR__);
	define('TDC_TEMPLATE_DIR', TDC_PLUGIN_DIR . '/templates');
	define('TDC_VERSION', '0.1.0');

	$includes = array(
		'inc/functions.php',
		'inc/assets.php',
		'inc/pages.php',
		'inc/ajax.php'
	);

	foreach ($includes as $include)
		include_once TDC_PLUGIN_DIR . '/' . $include;

}
add_action('init', 'tdc_init');

/**
 * On plugin activation, make sure the tdc_dismissed_suggestions table is created.
 */
function tdc_plugin_activate() {
	global $wpdb;

	$dismissed_suggestions_table = $wpdb->prefix . "tdc_dismissed_suggestions";

	$result = $wpdb->query("
		CREATE TABLE IF NOT EXISTS `" . $dismissed_suggestions_table . "` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`term_id` bigint(20) unsigned NOT NULL,
			`taxonomy` varchar(450) NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `term_id` (`term_id`)
		) ENGINE=InnoDB;");
}
register_activation_hook( __FILE__, 'tdc_plugin_activate' );
