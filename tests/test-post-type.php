<?php
/**
 * Term Debt Consolidator Post Type Tests.
 *
 * @since   1.0.0
 * @package Term_Debt_Consolidator
 */
class TDC_Post_Type_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  1.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'TDC_Post_Type' ) );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  1.0.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'TDC_Post_Type', term_debt_consolidator()->post-type );
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
