<?php

/**
 * Enqueue TDC assets
 *
 * @since 0.1
 */
function tdc_enqueue_assets() {
	wp_register_style('tdc-admin-ui-css',
		'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css',
		false, TDC_VERSION, false
	);

	wp_register_style('tdc-common', TDC_PLUGIN_DIR_URI . '/assets/css/style.css', array('tdc-admin-ui-css'));

	wp_register_script('tdc-suggestions', TDC_PLUGIN_DIR_URI . '/assets/js/suggestions.js',
		array('underscore', 'backbone', 'jquery-ui-progressbar'), TDC_VERSION, true);

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		if ($page == 'tdc-suggestions') {
			wp_enqueue_style('tdc-common');
			wp_enqueue_script('tdc-suggestions');
		}
	}
}
add_action('admin_enqueue_scripts', 'tdc_enqueue_assets');
