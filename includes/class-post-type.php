<?php
/**
 * Term Debt Consolidator Post Type.
 *
 * @since   1.0.0
 * @package Term_Debt_Consolidator
 */

/**
 * Term Debt Consolidator Post Type.
 *
 * @since 1.0.0
 */
class TDC_Post_Type {
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
		add_action( 'init', array( $this, 'term_debt_consolidator_recommendations' ) );
	}

	// Register Custom Post Type
	public function term_debt_consolidator_recommendations() {

		$labels = array(
			'name'                  => _x( 'Term Debt Consolidator Recommendations', 'term_debt_consolidator' ),
		);
		$args = array(
			'label'                 => __( 'Term Debt Consolidator Recommendations', 'term_debt_consolidator' ),
			'labels'                => $labels,
			'supports'              => array( ),
			'taxonomies'            => apply_filters( 'tdc_enabled_taxonomies', array( 'category', 'post_tag' ) ),
			'hierarchical'          => false,
			'public'                => false,
			'show_in_rest'          => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => false,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( 'tdc_recommendations', $args );

	}
}
