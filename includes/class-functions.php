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
		add_action( 'edit_term', array( $this, 'check_term' ), 10, 3 );
		add_action( 'create_term', array( $this, 'check_term' ), 10, 3 );
	}

	/**
	 * Check existing terms (for use on activation)
	 *
	 * @since	1.0.0
	 *
	 * @param	boolean	$hide_empty		Setting this variable to true will ignore terms that aren't attached to any posts.
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
				'hide_empty'    => $hide_empty,
			) );

			foreach ( $all_terms_in_tax as $term ) {

				// Skip terms we've already reviewed.
				if ( $skip >= $term->term_id ) {
					continue;
				}

				// Leave "Uncategorized" category alone.
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
	 * @param	object $term				object for current term.
	 * @param	object $all_terms_in_tax	array of term object to compare to.
	 */
	public function get_similar_terms( $term, $all_terms_in_tax ) {

		$similar_terms = array();

		// Compare $term to every other term in the taxonomy.
		foreach ( $all_terms_in_tax as $term_to_compare ) {

			// Don't compare term to itself.
			if ( $term->term_id === $term_to_compare->term_id ) {
				continue;
			}

			// Add to $similar_terms array if a similarity exists.
			if ( true === $this->are_terms_similar( $term->name, $term_to_compare->name ) ) {
				$similar_terms[] = $term_to_compare;
			}
		}

		if ( 0 < count( $similar_terms ) ) {
			$this->create_recommendation( $term, $similar_terms );
		}
	}

	/**
	 * Compare two terms to determine if they are related.
	 *
	 * @since 1.0.0
	 *
	 * @param	string $term				Term string.
	 * @param	string $term_to_compare	Term string to compare to.
	 */
	public function are_terms_similar( $term, $term_to_compare ) {

		$similarity = false;

		// Calculate the Levenshtein Distance between the two terms.
		$distance = levenshtein( $term, $term_to_compare );

		// Are these words similar?
		if ( $distance >= 0 && $distance <= 2 ) {

			// Do the words also sound similar?
			if ( metaphone( $term, 2 ) === metaphone( $term_to_compare, 2 ) ) {
				$similarity = true;
			}
		}
		return apply_filters( 'tdc_are_terms_similar', $similarity, $term, $term_to_compare );

	}

	/**
	 * Create or update a recommendation for term consolidation
	 *
	 * @since 1.0.0
	 *
	 * @param	object $term			Term object.
	 * @param	array  $similar_terms	Array of term ids for similar terms.
	 */
	public function create_recommendation( $term, $similar_terms ) {

		// Build array of similar terms.
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
					'terms'             => $similar_terms_array,
				),
			),
		));

		// If we have an existing recommendation that might match this one.
		if ( ! empty( $existing_recommendations ) ) {

			$existing_recommendation_terms = wp_get_post_terms( $existing_recommendations[0]->ID, $term->taxonomy );
			$existing_recommendation_terms_array = array();

			// Format existing recommendation post terms into array (to match $similar_terms_array).
			foreach ( $existing_recommendation_terms as $existing_recommendation_term ) {
				$existing_recommendation_terms_array[] = $existing_recommendation_term->term_id;
			}

			// Compare existing recommendation with new recommendation.
			$missing_terms = array_diff( $existing_recommendation_terms_array, $similar_terms_array );
			$new_terms = array_diff( $similar_terms_array, $existing_recommendation_terms_array );

			// Recommendation should be off by 1 term, which is the new term we'll add to the recommendation.
			if ( 0 === count( $missing_terms ) && 1 === count( $new_terms ) && $new_terms[0] === $term->term_id ) {
				wp_set_object_terms( $existing_recommendations[0]->ID, $term->term_id, $term->taxonomy, true );
				return;
			}
		}

		// Create post object.
		$post_arr = array(
			'post_title'    => 'Recommendations for ' . $term->taxonomy . ' Term ' . $term->term_id,
			'post_content'  => '',
			'post_type'     => 'tdc_recommendations',
			'post_status'   => 'publish',
			'tax_input'     => array(
				$term->taxonomy => $similar_terms_array,
			),
		);

		// Insert the post into the database.
		wp_insert_post( $post_arr );
	}

	/**
	 * Check single term on insert or update.
	 *
	 * @since 1.0.0
	 *
	 * @param	integer	$term_id		Term ID.
	 * @param	integer $tt_id			Term taxonomy ID.
	 * @param	string  $taxonomy		Taxonomy slug.
	 * @param	boolean	$hide_empty		Ignore terms with no posts attached.
	 */
	public function check_term( $term_id, $tt_id, $taxonomy, $hide_empty = false ) {

		$status = get_option( 'tdc_status' );
		$term = get_term( $term_id, $taxonomy );
		$all_terms_in_tax = get_terms( array(
			'taxonomy'      => $taxonomy,
			'orderby'       => 'term_id',
			'hide_empty'    => $hide_empty,
		) );

		$this->get_similar_terms( $term, $all_terms_in_tax );

		// Update status if this is a new term ID.
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
	 * @param	integer $primary_term_id	Primary term ID.
	 * @param	array   $terms_to_merge		Array of term IDs.
	 * @param	integer	$recommendation_id	Recommendation ID.
	 */
	public function merge_terms( $primary_term_id, $terms_to_merge, $recommendation_id = '' ) {

		$primary_term = get_term( $primary_term_id );
		if ( is_wp_error( $primary_term ) ) {
			// @TODO return error (term doesn't exist)
		}

		$terms = array();

		// Validate variables passed to this function.
		if ( is_array( $terms_to_merge ) && ! empty( $terms_to_merge ) ) {
			foreach ( $terms_to_merge as $term ) {
				$term_obj = get_term( $term );
				if ( is_wp_error( $term_obj ) ) {
					// @TODO return error (term doesn't exist).
				} elseif ( $term_obj->taxonomy !== $primary_term->taxonomy ) {
					// @TODO return error (taxonomy doesn't match).
				}

				$terms[] = $term_obj;
			}
		} else {
			// @TODO return error ($terms_to_merge not an array, or is an empty array).
		}

		$args = array(
			'post_type' => 'post',
			'tax_query' => array(
				array(
					'taxonomy' => $primary_term->taxonomy,
					'field'    => 'id',
					'terms'    => $terms_to_merge,
					'operator' => 'IN',
				),
			),
		);
		$query = new WP_Query( $args );

		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				$post_terms = wp_get_post_terms( $post->ID, $primary_term->taxonomy );
				foreach ( $post_terms as $key => $term ) {
					if ( $term->term_id !== $primary_term_id && in_array( $term->term_id, $terms_to_merge, true ) ) {
						unset( $post_term[ $key ] );
					}
				}
				wp_set_post_terms( $post->ID, $post_terms, $primary_term->taxonomy );
			}
		}
		rewind_posts();

		foreach ( $terms_to_merge as $term_to_delete ) {
			wp_delete_term( $term_to_delete, $primary_term->taxonomy );
		}

		if ( intval( $recommendation_id ) ) {
			$recommendation_post = array(
				'ID'           => $recommendation_id,
				'post_status'  => 'trash',
			);

			// Update the post into the database.
			wp_update_post( $recommendation_post );
		}
	}
}
