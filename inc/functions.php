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
	// TODO: $taxonomy should be something the user chooses
	$taxonomy = 'post_tags';
	$dismissed = tdc_get_dismissed_suggestions( $taxonomy );

	return array_merge(array(
		'ajax_nonce' => wp_create_nonce('tdc_ajax_nonce'),
		'existing' => empty( $dismissed )
	), $more);
}

/**
 * Add a term to the list of dismissed suggestions
 *
 * @since 0.1
 */
function tdc_dismiss_suggestions_for_term($term_id, $taxonomy) {
	$existing = get_option('tdc_dismissed_term_ids_for_' . $taxonomy, array());
	$existing[] = (int) $term_id;
	return update_option('tdc_dismissed_term_ids_for_' . $taxonomy, array_unique($existing));
}

/**
 * Return the list of term ids for dismissed suggestions
 *
 * @since 0.1
 */
function tdc_get_dismissed_suggestions($taxonomy) {
	return get_option('tdc_dismissed_term_ids_for_' . $taxonomy, array());
}

/**
 * Clear out dismissed suggestions for a taxonomy
 *
 * @since 0.1
 */
function tdc_clear_dismissed_suggestions($taxonomy) {
	return delete_option('tdc_dismissed_term_ids_for_' . $taxonomy);
}
