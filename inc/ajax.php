<?php

include_once __DIR__ . '/class/suggestions-query.php';

/**
 * Respond to AJAX requests for generating tag consolidation suggestions
 *
 * @since 0.1
 */
function tdc_ajax_generate_consolidation_suggestions() {
	check_ajax_referer('tdc_ajax_nonce', 'security');

	if (isset($_POST['request'])) {
		$data = json_decode(stripslashes($_POST['request']), true);
		$query = new SuggestionsQuery($data['taxonomy']);

		$suggestions = $query->getSuggestions($data['page']);

		print json_encode(array(
			"success" => true,
			"suggestions" => $suggestions,
			"original" => $data
		));
		wp_die();
	} else {
		throw new Exception('Must specify a taxonomy to get suggestions.');
	}
}
add_action('wp_ajax_tdc_ajax_generate_consolidation_suggestions', 'tdc_ajax_generate_consolidation_suggestions');

/**
 * Respond to AJAX requests for tag consolidation suggestions
 *
 * @since 0.1
 */
function tdc_ajax_get_consolidation_suggestions() {
	check_ajax_referer('tdc_ajax_nonce', 'security');

	if (isset($_POST['request'])) {
		$data = json_decode(stripslashes($_POST['request']), true);
		$query = new SuggestionsQuery($data['taxonomy'], array('number' => 10));

		$suggestions = $query->getSuggestions($data['page']);

		print json_encode(array(
			"success" => true,
			"suggestions" => $suggestions,
			"original" => $data
		));
		wp_die();
	} else {
		throw new Exception('Must specify a taxonomy to get suggestions.');
	}
}
add_action('wp_ajax_tdc_ajax_get_consolidation_suggestions', 'tdc_ajax_get_consolidation_suggestions');

/**
 * Handle AJAX request to apply a suggestion
 *
 * @since 0.1
 */
function tdc_ajax_apply_consolidation_suggestions() {
	check_ajax_referer('tdc_ajax_nonce', 'security');

	$data = json_decode(stripslashes($_POST['request']), true);

	$args = array(
		'posts_per_page' => -1,
		'post_status' => 'any',
		'tax_query' => array(
			array(
				'taxonomy' => $data['taxonomy'],
				'field' => 'term_id',
				'terms' => $data['term_ids'],
			)
		)
	);

	$posts = get_posts($args);

	$error = false;
	foreach ($posts as $post) {
		$to_add = array((int) $data['primary_term']);
		$to_remove = array_map(function($term_id) { return (int) $term_id; }, $data['term_ids']);

		$set_result = wp_set_post_terms($post->ID, $to_add, $data['taxonomy'], true);
		if (is_wp_error($set_result))
			$error = true;

		$remove_result = wp_remove_object_terms($post->ID, $to_remove, $data['taxonomy']);
		if (is_wp_error($remove_result))
			$error = true;

		foreach ($to_remove as $term_id_to_remove) {
			$delete_result = wp_delete_term($term_id_to_remove, $data['taxonomy']);
			if (is_wp_error($delete_result))
				$error = true;
		}

		if ($error) {
			print json_encode(array(
				'success' => false,
				'message' => 'An error occurred during the consolidation. Please try again'
			));
			wp_die();
		}
	}

	print json_encode(array(
		'success' => true,
		'message' => 'Successfully consolidated terms.'
	));
	wp_die();
}
add_action('wp_ajax_tdc_ajax_apply_consolidation_suggestions', 'tdc_ajax_apply_consolidation_suggestions');

/**
 * Handle AJAX request to dismiss a suggestion
 *
 * @since 0.1
 */
function tdc_ajax_dismiss_consolidation_suggestions() {
	check_ajax_referer('tdc_ajax_nonce', 'security');

	$data = json_decode(stripslashes($_POST['request']), true);
	$result = tdc_dismiss_suggestions_for_term($data['primary_term'], $data['taxonomy']);

	if ($result) {
		print json_encode(array(
			'success' => true,
			'message' => 'Dismissed!'
		));
	} else {
		print json_encode(array(
			'success' => false,
			'message' => 'An error occurred. Please try again.'
		));
	}
	wp_die();
}
add_action('wp_ajax_tdc_ajax_dismiss_consolidation_suggestions', 'tdc_ajax_dismiss_consolidation_suggestions');
