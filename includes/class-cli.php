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

		// Get # of recommendations
		$existing_recommendations = get_posts( array(
			'post_type'         => 'tdc_recommendations',
			'posts_per_page'    => 0,
			'offset'            => 0,
		));

		WP_CLI::line( WP_CLI::colorize( "%y" . count( $existing_recommendations ) . " Recommendations%n" ) );

		// Get the status of processed taxonomies
		$processed = get_option( 'tdc_status' );
		$items = array();
		foreach ( $processed as $taxonomy => $status ) {
			$terms = get_terms( array(
			    'taxonomy' => $taxonomy,
			    'hide_empty' => false,
				'orderby' => 'term_id',
			) );
			$total = count( $terms );

			$count = 0;
			foreach ( $terms as $term ) {
				if ( $count < $term->term_id ) {
					$count++;
				}
			}

			$items[] = array(
				'taxonomy'  => $taxonomy,
				'status'    => 'Processed ' . $count . ' of ' . $total . ' terms',
			);
		}

		WP_CLI\Utils\format_items( 'table', $items, array( 'taxonomy', 'status' ) );
	}

	/**
	 * Reprocess recommendations
	 *
	 * @since 1.0.0
	 */
	public function reprocess_recommendations() {
	}
}
