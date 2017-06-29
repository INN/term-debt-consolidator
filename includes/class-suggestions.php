<?php
/**
 * Term Debt Consolidator Suggestions.
 *
 * @since   1.0.0
 * @package Term_Debt_Consolidator
 */

/**
 * Term Debt Consolidator Suggestions.
 *
 * @since 1.0.0
 */
class TDC_Suggestions {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var   Term_Debt_Consolidator
	 */
	protected $plugin = null;

	/**
	 * Instance of TDC_Functions
	 *
	 * @since   1.0.0
	 * @var     TDC_Functions
	 */
	protected $functions;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param  Term_Debt_Consolidator $plugin Main plugin object.
	 */
	public function __construct( $plugin, $taxonomy = 'category', $options = array() ) {
		$this->plugin = $plugin;
		$this->hooks();

		$this->functions = new TDC_Functions( $this );

		$this->taxonomy = $taxonomy;
		$defaults = apply_filters( 'tdc_default_get_terms_args', array(
			'hide_empty' => true,
			'offset' => 0,
			'number' => 100,
			'autoDismiss' => true,
			/**
			 * Pass `tdc` so we can identify queries for suggestions
			 * and modify the get_terms SQL accordingly
			 *
			 * @see tdc_list_terms_exclusions
			 **/
			'tdc' => true,
			/**
			 * Avoid problems when object cache is in use
			 */
			'cache_domain' => 'tdc_terms_' . microtime()
		), $taxonomy );
		$this->options = wp_parse_args( $options, $defaults );
		$all_terms_opts = apply_filters( 'tdc_all_term_args', array( 'hide_empty' => false, 'tdc' => true ), $taxonomy );
		$this->all_terms = get_terms( array( $this->taxonomy ), $all_terms_opts );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		add_filter( 'list_terms_exclusions', array( $this, 'tdc_list_terms_exclusions' ), 99, 3 );
	}

	/**
	 * Modify the get_terms SQL query to get excluded term_ids from the tdc_dismissed_suggestions
	 * table.
	 */
	public function tdc_list_terms_exclusions( $exclusions, $args, $taxonomies ) {
		if ( isset( $args['tdc'] ) ) {
			global $wpdb;
			$ret = " AND t.term_id NOT IN (SELECT tdcds.term_id from " .
				$wpdb->prefix . "tdc_dismissed_suggestions as tdcds where tdcds.taxonomy in ('" .
				implode( "', '", array_map( 'esc_sql', $taxonomies ) ) . "'))";
			return $ret;
		} else {
			return $exclusions;
		}
	}

	public function get_suggestions( $page = 1 ) {
		$this->options['offset'] = (int) ( ( $page - 1 ) * $this->options['number'] );
		$terms = get_terms( array( $this->taxonomy ), $this->options );
		$groups = array();

		if ( empty( $terms ) ) {
			return false;
		}

		foreach ( $terms as $idx => $term ) {
			$term->url = get_term_link( $term, $this->taxonomy );
			$term->edit_url = edit_term_link( 'Edit', '', '', $term, false );
			$similar = $this->getSuggestionsForTerm( $term );

			if ( count( $similar ) <= 1 && $this->options['autoDismiss'] ) {
				$this->functions->tdc_dismiss_suggestions_for_term( $term->term_id, $this->taxonomy );
				continue;
			}

			usort( $similar, array( $this, 'sortByCount' ) );
			$groups[] = $similar;
		}

		$results = array(
			'groups' => $groups,
			'page' => $page,
			'totalPages' => ceil( count( $this->all_terms ) / $this->options['number'] )
		);

		return $results;
	}

	/**
	 * Determine how similar two terms are.
	 *
	 * @since 0.1
	 */
	public function are_terms_similar( $a, $b ) {

		// Calculate the Levenshtein Distance between the two terms
		$distance = levenshtein( $a, $b );

		// Are these words similar?
		if ( $distance >= 0 && $distance <= 2 ) {

			// Do the words also sound similar?
			if ( metaphone( $a, 2 ) === metaphone( $b, 2 ) ) {
				return true;
			}
		}
		return false;
	}

	public function getSuggestionsForTerm( $term ) {
		$similar = array( $term );

		foreach ( $this->all_terms as $subidx => $term_to_consider ) {
			if ( $term_to_consider->term_id == $term->term_id ) {
				continue;
			}

			if ( $this->are_terms_similar( $term->name, $term_to_consider->name ) ) {
				$term_to_consider->url = get_term_link( $term_to_consider, $this->taxonomy );
				$term_to_consider->edit_url = edit_term_link( 'Edit', '', '', $term_to_consider, false );
				$similar[] = $term_to_consider;
			}
		}

		return $similar;
	}

	public function sortByCount( $a, $b ) {
		if ( $a->count > $b->count ) {
			return -1;
		} elseif ( $a->count < $b->count ) {
			return 1;
		} elseif ( $a->count == $b->count ) {
			return 0;
		}
	}
}
