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
		'inc/consolidator.php'
	);

	foreach ($includes as $include)
		include_once TDC_PLUGIN_DIR . '/' . $include;
}
add_action('init', 'tdc_init');


function tdc_admin_menu() {
	add_menu_page(
		'Term Debt Consolidator',
		'Term Debt Consolidator',
		'edit_posts',
		'tdc-suggestions',
		'tdc_suggestions_page',
		'dashicons-networking'
	);
}
add_action('admin_menu', 'tdc_admin_menu');
