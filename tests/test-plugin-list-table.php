<?php
/**
 * Term Debt Consolidator Plugin List Table Tests.
 *
 * @since   1.0.0
 * @package Term_Debt_Consolidator
 */
class TDC_Plugin_List_Table_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  1.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'TDC_Plugin_List_Table' ) );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  1.0.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'TDC_Plugin_List_Table', term_debt_consolidator()->plugin-list-table );
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
