<?php

/**
 * Render a template by specifying a filename and context.
 *
 * @param (string) $template -- the filename of the template to render.
 * @param (array) $context -- associative array of values used within the template.
 *
 * @since 0.1
 */
function tdc_render_template($template, $context=false) {
	if (!empty($context))
		extract($context);

	include TDC_TEMPLATE_DIR . '/' . $template;
}

/**
 * Returns a JSON object with some of the essential bits used in the front-end javascript
 *
 * @since 0.1
 */
function tdc_json_obj($more=array()) {
	$enabled_taxonomies = tdc_enabled_taxonomies();

	$existing = array();
	foreach ( $enabled_taxonomies as $tax ) {
		$dissmissed_for_tax = tdc_get_dismissed_suggestions( $tax );
		$existing[$tax] = ! empty( $dissmissed_for_tax );
	}

	return array_merge(array(
		'ajax_nonce' => wp_create_nonce('tdc_ajax_nonce'),
		'existing' => $existing
	), $more);
}

/**
 * Add a term to the list of dismissed suggestions
 *
 * @since 0.1
 */
function tdc_dismiss_suggestions_for_term($term_id, $taxonomy) {
	global $wpdb;

	$result = $wpdb->insert(
		$wpdb->prefix . 'tdc_dismissed_suggestions',
		array( 'term_id' => (int) $term_id, 'taxonomy' => $taxonomy ),
		array( '%d', '%s' )
	);

	return $result;
}

/**
 * Return the list of term ids for dismissed suggestions
 *
 * @since 0.1
 */
function tdc_get_dismissed_suggestions($taxonomy) {
	global $wpdb;

	$result = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT term_id from {$wpdb->prefix}tdc_dismissed_suggestions where taxonomy = %s", $taxonomy
		), ARRAY_N
	);

	$ret = array_map(function($term_id) { return $term_id[0]; }, $result);

	if ( ! empty( $result ) ) {
		return $ret;
	}
	return array();
}

/**
 * Clear out dismissed suggestions for a taxonomy
 *
 * @since 0.1
 */
function tdc_clear_dismissed_suggestions($taxonomy) {
	$wpdb;

	$result = $wpdb->delete(
		$wpdb->prefix . 'tdc_dismissed_suggestions',
		array( 'taxonomy' => $taxonomy )
	);

	return $result;
}

/**
 * Get taxonomies for which TDC should be enabled
 *
 * @since 0.1
 */
function tdc_enabled_taxonomies() {
	$taxonomies = apply_filters( 'tdc_enabled_taxonomies', array( 'post_tag', 'category' ) );

	// Post tags must always be enabled for now
	$existing = array('post_tag');
	foreach ( $taxonomies as $tax ) {
		// Only allow taxonomies that actually exist
		if ( taxonomy_exists( $tax ) ) {
			$existing[] = $tax;
		}
	}

	return $existing;
}
