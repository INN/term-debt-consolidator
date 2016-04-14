<?php

/**
 * Render the main TDC suggestions page
 *
 * @since 0.1
 */
function tdc_suggestions_page($taxonomy='post_tag') {
	$enabled_taxonomies = tdc_enabled_taxonomies();

	$existing = array();
	$taxonomies = array();
	foreach ( $enabled_taxonomies as $tax ) {
		$dissmissed_for_tax = tdc_get_dismissed_suggestions( $tax );
		$existing[$tax] = ! empty( $dissmissed_for_tax );
		$taxonomies[$tax] = get_taxonomy( $tax );
	}

	tdc_render_template( 'suggestions.php', array(
		'existing' => $existing,
		'default' => $taxonomy,
		'taxonomies' => $taxonomies
	) );
}
