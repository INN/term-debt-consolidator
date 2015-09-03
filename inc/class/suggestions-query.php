<?php

class SuggestionsQuery {

	public $all_terms;

	public function __construct($taxonomy='category', $options=array()) {
		$this->taxonomy = $taxonomy;
		$defaults = array(
			'hide_empty' => true,
			'offset' => 0,
			'number' => 100,
		);
		$this->options = wp_parse_args($options, $defaults);

		$all_terms_opts = array('hide_empty' => false);
		$dismissed_suggestions = tdc_get_dismissed_suggestions($this->taxonomy);

		if (!empty($dismissed_suggestions)) {
			$this->options['exclude'] = $dismissed_suggestions;
			$all_terms_opts['exclude'] = $dismissed_suggestions;
		}

		$this->all_terms = get_terms(array($this->taxonomy), $all_terms_opts);
	}

	public function getTerms($page=1) {
		$this->options['offset'] = (int) (($page - 1) * $this->options['number']);
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
				$term_to_consider->edit_url = edit_term_link('Edit', '', '', $term_to_consider, false);
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
			$term->edit_url = edit_term_link('Edit', '', '', $term, false);
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
