<?php
/**
 * Term_Debt_Consolidator.
 *
 * @since   1.0.0
 * @package Term_Debt_Consolidator
 */
class Term_Debt_Consolidator_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  1.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'Term_Debt_Consolidator') );
	}

	/**
	 * Test that our main helper function is an instance of our class.
	 *
	 * @since  1.0.0
	 */
	function test_get_instance() {
		$this->assertInstanceOf(  'Term_Debt_Consolidator', term_debt_consolidator() );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  1.0.0
	 */
	function test_sample() {
		$this->assertTrue( true );
	}
}
