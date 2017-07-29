<?php
/**
 * Term Debt Consolidator Functions Tests.
 *
 * @since   1.0.0
 * @package Term_Debt_Consolidator
 */
class TDC_Functions_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  1.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'TDC_Functions' ) );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  1.0.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'TDC_Functions', term_debt_consolidator()->functions );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  1.0.0
	 */
	function test_review_existing_terms() {
		$this->assertTrue( true );
		// @TODO test does it create the right # of recommendations
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  1.0.0
	 */
	function test_get_similar_terms() {
		$functions = new TDC_Functions( $this );

		$term_a = wp_create_term( 'hot dog', 'post_tag' );
		$term_a_obj = get_term( $term_a['term_id'], 'post_tag' );

		$term_b = wp_create_term( 'hotdog', 'post_tag' );
		$term_b_obj = get_term( $term_b['term_id'], 'post_tag' );

		$term_c = wp_create_term( 'hotdogs', 'post_tag' );
		$term_c_obj = get_term( $term_c['term_id'], 'post_tag' );

		$all_terms_in_tax = get_terms( array(
			'taxonomy'      => 'post_tag',
			'orderby'       => 'term_id',
			'hide_empty'    => false,
		) );

		// Get similar terms for $term_a.
		$similar_terms = $functions->get_similar_terms( get_term( $term_a['term_id'], 'post_tag' ), $all_terms_in_tax );

		// We should have 2 similar terms ( $term_b & $term_c ).
		$this->assertCount( 2, $similar_terms );

		// Loop through $similar_terms and remove $term_b & $term_c.
		foreach ( $similar_terms as $key => $similar_term ) {
			if ( in_array( $similar_term->name, array( 'hotdog', 'hotdogs', true) ) ) {
				unset( $similar_terms[ $key ] );
			}
		}

		// Now there should be nothing left in $similar_terms.
		$this->assertCount( 0, $similar_terms );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  1.0.0
	 */
	function test_are_terms_similar() {
		$functions = new TDC_Functions( $this );

		// Compound words.
		$this->assertTrue( $functions->are_terms_similar( 'hot dog', 'hotdog' ) );

		// Trailing or preceding numbers or dates.
		$this->assertFalse( $functions->are_terms_similar( 'election', 'election 2016' ) );
		$this->assertFalse( $functions->are_terms_similar( 'election', '2016 election' ) );
		$this->assertFalse( $functions->are_terms_similar( 'election', '2016election' ) );
		$this->assertTrue( $functions->are_terms_similar( '2012 election', '2016 election' ) );

		// Similar sounding words.
		$this->assertTrue( $functions->are_terms_similar( 'Assistance', 'Assistants' ) );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  1.0.0
	 */
	function test_create_recommendation() {
		$this->assertTrue( true );
		// @TODO test creating a recommendation
		// @TODO test creating a similar recommendation (do they merge?)
		// @TODO test that the right number of recommendations are added
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  1.0.0
	 */
	function test_check_term() {
		$this->assertTrue( true );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  1.0.0
	 */
	function test_merge_terms() {
		$this->assertTrue( true );
	}

}
