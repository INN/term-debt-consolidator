<?php
/**
 * Term Debt Consolidator Cli.
 *
 * @since   1.0.0
 * @package Term_Debt_Consolidator
 */

/**
 * Term Debt Consolidator Cli.
 *
 * @since 1.0.0
 */
class TDC_Cli {
	/**
	 * Parent plugin class
	 *
	 * @var   Term_Debt_Consolidator
	 *
	 * @since 1.0.0
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

		// If we have WP CLI, add our commands.
		if ( $this->verify_wp_cli() ) {
			$this->add_commands();
		}
	}

	/**
	 * Check for WP CLI running.
	 *
	 * @since  1.0.0
	 *
	 * @return boolean True if WP CLI currently running.
	 */
	public function verify_wp_cli() {
		return ( defined( 'WP_CLI' ) && WP_CLI );
	}

	/**
	 * Add our commands.
	 *
	 * @since  1.0.0
	 */
	public function add_commands() {
		WP_CLI::add_command( 'tdc_status', array( $this, 'status' ) );
		WP_CLI::add_command( 'tdc_reprocess', array( $this, 'reprocess_recommendations' ) );
	}

	/**
	 * Check the status of the plugin
	 *
	 * @since 1.0.0
	 */
	public function status() {
		WP_CLI::line( WP_CLI::colorize( "%pTerm Debt Consolidator%n" ) );
		WP_CLI::line( WP_CLI::colorize( "%yEnabled taxonomies: %n" ) . implode( apply_filters( 'tdc_enabled_taxonomies', array( 'category', 'post_tag' ) ), ', ' ) );
		WP_CLI::line( 'Here we go!' );

		/**
		 * IDEAS:
		 * @TODO Processed 25 of 50 categories (50%)
		 * @TODO 34 Recommendations
		 * @TODO 12 Dismissed Recommendations
		 */
	}

	/**
	 * Reprocess recommendations
	 *
	 * @since 1.0.0
	 */
	public function reprocess_recommendations() {
	}
}
