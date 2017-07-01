<?php
/**
 * Term Debt Consolidator Functions.
 *
 * @since   1.0.0
 * @package Term_Debt_Consolidator
 */

/**
 * Term Debt Consolidator Functions.
 *
 * @since 1.0.0
 */
class TDC_Functions {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var   Term_Debt_Consolidator
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param  Term_Debt_Consolidator $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		add_action( 'edit_term', array( $this, 'function' ), 10, 3 );
		add_action( 'create_term', array( $this, 'function' ), 10, 3 );
	}

	/**
	 * Check existing terms (for use on activation)
	 *
	 * @since	1.0.0
	 *
	 * @param	$hide_empty		boo		Setting this variable to true will ignore terms that aren't attached to any posts
	 */
	public function review_existing_terms( $hide_empty = false ) {
		$status = get_option( 'tdc_status' );

		$taxonomies = apply_filters( 'tdc_enabled_taxonomies', array( 'category', 'post_tag' ) );
		foreach ( $taxonomies as $taxonomy ) {

			/*
			 * $status[ $taxonomy ] contains the largest term ID we've reviewed in this taxonomy.
			 * Skipping all IDs < or = this number prevents re-processing
			 */
			$skip = isset( $status[ $taxonomy ] ) ? $status[ $taxonomy ] : '0';

			$all_terms_in_tax = get_terms( array(
				'taxonomy'      => $taxonomy,
				'orderby'       => 'term_id',
				'hide_empty'    => $hide_empty
			) );

			foreach ( $all_terms_in_tax as $term ) {

				// Skip terms we've already reviewed
				if ( $skip >= $term->term_id ) {
					continue;
				}

				// Leave "Uncategorized" category alone
				if ( 'category' === $taxonomy && 'Uncategorized' === $term->name ) {
					continue;
				}

				$this->get_similar_terms( $term, $all_terms_in_tax );

				$status[ $taxonomy ] = $term->term_id;
			}

		}

		update_option( 'tdc_status', $status );
	}

	/**
	 * Review all other terms in taxonomy for similar terms
	 *
	 * @since 1.0.0
	 *
	 * @param	$term				obj		object for current term
	 * @param	$all_terms_in_tax	obj		array of term object to compare to
	 */
	public function get_similar_terms( $term, $all_terms_in_tax ) {

		$similar_terms = [];

		// Compare $term to every other term in the taxonomy
		foreach( $all_terms_in_tax as $term_to_compare ) {

			// Don't compare term to itself
			if ( $term->term_id === $term_to_compare->term_id ) {
				continue;
			}

			// Add to $similar_terms array if a similarity exists
			if ( true === $this->are_terms_similar( $term->name, $term_to_compare->name ) ) {
				$similar_terms[] = $term_to_compare;
			}
		}

		$this->create_recommendation( $term, $similar_terms );
	}

	/**
	 * Compare two terms to determine if they are related
	 *
	 * @since 1.0.0
	 *
	 * @param	$term				str		Term string
	 * @param	$term_to_compare	str		Term string to compare to
	 */
	public function are_terms_similar( $term, $term_to_compare ) {
		// Calculate the Levenshtein Distance between the two terms
		$distance = levenshtein( $term, $term_to_compare );

		// Are these words similar?
		if ( $distance >= 0 && $distance <= 2 ) {

			// Do the words also sound similar?
			if ( metaphone( $term, 2 ) === metaphone( $term_to_compare, 2 ) ) {
				return true;
			}
		}
		return false;

	}

	/**
	 * Create or update a recommendation for term consolidation
	 *
	 * @since 1.0.0
	 *
	 * @param	$term			obj		Term object
	 * @param	$similar_terms	arr		Array of term ids for similar terms
	 */
	public function create_recommendation( $term, $similar_terms ) {

		// Build array of similar terms
		$similar_terms_array[] = $term->term_id;
		foreach ( $similar_terms as $similar_term ) {
			$similar_terms_array[] = $similar_term->term_id;
		}

		$existing_recommendations = get_posts( array(
			'post_type'         => 'tdc_recommendations',
			'posts_per_page'    => 1,
			'offset'            => 0,
			'tax_query'         => array(
				array(
					'taxonomy'          => $term->taxonomy,
					'field'             => 'id',
					'terms'             => $similar_terms_array
				)
			),
		));

		// If we have an existing recommendation that might match this one
		if ( ! empty( $existing_recommendations ) ) {

			$existing_recommendation_terms = wp_get_post_terms( $existing_recommendations[0]->ID, $term->taxonomy );
			$existing_recommendation_terms_array = [];

			// Format existing recommendation post terms into array (to match $similar_terms_array)
			foreach ( $existing_recommendation_terms as $existing_recommendation_term ) {
				$existing_recommendation_terms_array[] = $existing_recommendation_term->term_id;
			}


			// Compare existing recommendation with new recommendation
			$missing_terms = array_diff( $existing_recommendation_terms_array, $similar_terms_array );
			$new_terms = array_diff( $similar_terms_array, $existing_recommendation_terms_array );

			// Recommendation should be off by 1 term, which is the new term we'll add to the recommendation
			if ( 0 === count( $missing_terms ) && 1 === count( $new_terms ) && $new_terms[0] === $term->term_id ) {
				wp_set_object_terms( $existing_recommendations[0]->ID, $term->term_id, $term->taxonomy, true );
				return;
			}

		}

		// Create post object
		$post_arr = array(
		  'post_title'    => 'Recommendations for ' . $term->taxonomy . ' Term ' . $term->term_id,
		  'post_type'     => 'tdc_recommendations',
		  'post_status'   => 'publish',
		  'tax_input'     => array(
			  $term->taxonomy => $similar_terms_array
		  ),
		);

		// Insert the post into the database
		wp_insert_post( $post_arr );

	}

	/**
	 * Check single term on insert or update.
	 *
	 * @since 1.0.0
	 *
	 * @param	$term_id	int		Term ID.
	 * @param	$tt_id		int		Term taxonomy ID.
	 * @param	$taxonomy	str		Taxonomy slug.
	 */
	public function check_term( $term_id, $tt_id, $taxonomy ) {

		$status = get_option( 'tdc_status' );
		$term = get_term( $term_id, $taxonomy );
		$all_terms_in_tax = get_terms( array(
			'taxonomy'      => $taxonomy,
			'orderby'       => 'term_id',
			'hide_empty'    => $hide_empty
		) );

		$this->get_similar_terms( $term, $all_terms_in_tax );

		// Update status if this is a new term ID
		if ( $term_id < $status[ $taxonomy ] ) {
			$status[ $taxonomy ] = $term->term_id;
			update_option( 'tdc_status', $status );
		}
	}

	/**
	 * Merge 2 or more terms.
	 *
	 * @since 1.0.0
	 *
	 * @param	$primary_term_id	int		Primary term ID.
	 * @param	$terms_to_merge		arr		Array of term IDs.
	 */
	public function merge_terms( $primary_term_id, $terms_to_merge ) {

	}

	/**
	 * @TODO migrate functions for combining terms -> add functionality to consolidate to designated term
	 */










	/**
	 * Returns a JSON object with some of the essential bits used in the front-end javascript
	 *
	 * @since 0.1
	 */
	public function tdc_json_obj( $more = array() ) {
		$enabled_taxonomies = $this->tdc_enabled_taxonomies();

		$existing = array();
		foreach ( $enabled_taxonomies as $tax ) {
			$dissmissed_for_tax = $this->tdc_get_dismissed_suggestions( $tax );
			$existing[ $tax ] = ! empty( $dissmissed_for_tax );
		}

		return array_merge(
			array(
				'ajax_nonce' => wp_create_nonce('tdc_ajax_nonce'),
				'existing' => $existing,
			),
			$more
		);
	}

	/**
	 * Add a term to the list of dismissed suggestions
	 *
	 * @since 0.1
	 */
	public function tdc_dismiss_suggestions_for_term( $term_id, $taxonomy ) {
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
	public function tdc_get_dismissed_suggestions( $taxonomy ) {
		global $wpdb;

		$result = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT term_id from {$wpdb->prefix}tdc_dismissed_suggestions where taxonomy = %s", $taxonomy
			), ARRAY_N
		);

		$ret = array_map( function( $term_id ) { return $term_id[0]; }, $result );

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
	public function tdc_clear_dismissed_suggestions( $taxonomy ) {
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
	public function tdc_enabled_taxonomies() {

		$taxonomies = apply_filters( 'tdc_enabled_taxonomies', array( 'post_tag', 'category' ) );

		// Check if taxonomies exist, remove any that don't
		foreach ( $taxonomies as $key => $tax ) {
			if ( ! taxonomy_exists( $tax ) ) {
				unset( $taxonomies[$key] );
			}
		}

		return $taxonomies;
	}
}
