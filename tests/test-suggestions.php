<?php
/**
 * Term Debt Consolidator Suggestions Tests.
 *
 * @since   1.0.0
 * @package Term_Debt_Consolidator
 */
class TDC_Suggestions_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  1.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'TDC_Suggestions' ) );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  1.0.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'TDC_Suggestions', term_debt_consolidator()->suggestions );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  1.0.0
	 */
	function test_are_terms_similar() {
		$suggesions = new TDC_Suggestions( 'term-debt-consolidator' );
		$this->assertTrue( $suggesions->are_terms_similar( 'color', 'Colour' ) );
		$this->assertFalse( $suggesions->are_terms_similar( 'apple', 'Orange' ) );
	}
}
