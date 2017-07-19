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
		$this->assertTrue( true );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  1.0.0
	 */
	function test_are_terms_similar() {
		$this->assertTrue( true );
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
