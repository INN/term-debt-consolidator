<?php

// TODO: figure out how to batch the analysis so that requests don't timeout.

class SuggestionsQuery {

	public $all_terms;

	public function __construct($taxonomy='category', $options=array()) {
		$this->taxonomy = $taxonomy;
		$defaults = array(
			'hide_empty' => false,
			'offset' => 0,
			'number' => 100,
		);
		$this->options = wp_parse_args($options, $defaults);
		$this->all_terms = get_terms(array($this->taxonomy), array('hide_empty' => false));
	}

	public function getTerms($page=1) {
		$this->options['offset'] = $page - 1;
		return get_terms(array($this->taxonomy), $this->options);
	}

	public function termsAreSimilar($a, $b) {
		$distance = levenshtein($a, $b);
		$a_metaphone_key = metaphone($a, 2);

		if ($distance <= 2 && $distance >= 0) {
			if (metaphone($b, 2) == $a_metaphone_key) {
				return true;
			}
		}
		return false;
	}

	public function getSuggestionsForTerm($term) {
		$similar = array($term);

		foreach ($this->all_terms as $subidx => $term_to_consider) {
			if ($term_to_consider->term_id == $term->term_id)
				continue;

			if ($this->termsAreSimilar($term->name, $term_to_consider->name)) {
				$term_to_consider->url = get_term_link($term_to_consider, $this->taxonomy);
				$similar[] = $term_to_consider;
			}
		}

		return $similar;
	}

	public function getSuggestions($page=1) {
		$terms = $this->getTerms($page);
		$groups = array();

		if (empty($terms))
			return false;

		foreach ($terms as $idx => $term) {
			$term->url = get_term_link($term, $this->taxonomy);
			$similar = $this->getSuggestionsForTerm($term);
			usort($similar, array($this, 'sortByCount'));
			$groups[] = $similar;
		}

		$results = array(
			'groups' => $groups,
			'page' => $page,
			'totalPages' => ceil(count($this->all_terms) / $this->options['number'])
		);

		return $results;
	}

	public function sortByCount($a, $b) {
		if ($a->count > $b->count)
			return -1;
		if ($a->count < $b->count)
			return 1;
		if ($a->count == $b->count)
			return 0;
	}

}

/**
 * AJAX Functions
 */

/**
 * Respond to AJAX requests for tag consolidation suggestions
 *
 * @since 0.1
 */
function tdc_ajax_get_consolidation_suggestions() {
	//check_ajax_referer('tdc_ajax_nonce', 'security');

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
