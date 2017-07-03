<?php
/**
 * Term Debt Consolidator Admin.
 *
 * @since   1.0.0
 * @package Term_Debt_Consolidator
 */

/**
 * Term Debt Consolidator Admin class.
 *
 * @since 1.0.0
 */
class TDC_Admin {
	/**
	 * Parent plugin class.
	 *
	 * @var    Term_Debt_Consolidator
	 * @since  1.0.0
	 */
	protected $plugin = null;

	/**
	 * Option key, and option page slug.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $key = 'term_debt_consolidator_admin';

	/**
	 * Options page metabox ID.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $metabox_id = 'term_debt_consolidator_admin_metabox';

	/**
	 * Options Page title.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $title = '';

	/**
	 * Options Page hook.
	 *
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Instance of TDC_Functions
	 *
	 * @since   1.0.0
	 * @var     TDC_Functions
	 */
	protected $functions;

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

		$this->functions = new TDC_Functions( $this );

		// Set our title.
		$this->title = esc_attr__( 'Term Consolidation Recommendations', 'term-debt-consolidator' );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {

		// Hook in our actions to the admin.
		add_action( 'admin_post_tdc_merge', array( $this, 'merge_request' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
	}

	public function merge_request() {
		// @TODO add nonce validation

		// @TODO add sanitization here
		// @TODO are these all IDs
		$primary_term = $_POST['primary_term'];
		$merge_terms = $_POST['merge_terms'];
		$recommendation = $_POST['recommendation'];

		if ( $primary_term && $merge_terms ) {

			$merge_terms = explode( ',', $merge_terms );
			$merge_terms = array_combine( $merge_terms, $merge_terms );

			// Remove primary term ID from merge set
			unset( $merge_terms[ $primary_term ] );

			$merge = $this->functions->merge_terms( $primary_term, $merge_terms, $recommendation );
		}

		wp_safe_redirect( '/wp-admin/tools.php?page=term_debt_consolidator_admin' );

	}

	/**
	 * Add menu options page.
	 *
	 * @since  1.0.0
	 */
	public function add_options_page() {
		$this->options_page = add_submenu_page(
			'tools.php',
			$this->title,
			$this->title,
			'edit_posts',
			$this->key,
			array( $this, 'admin_page_display' )
		);
	}

	/**
	 * Admin page markup.
	 *
	 * @since  1.0.0
	 */
	public function admin_page_display() {

		$recommendations = new TDC_Plugin_List_Table( $this->plugin );
        $recommendations->prepare_items();
        ?>
            <div class="wrap">
				<h2>Term Consolidation Suggestions</h2>
                <?php $recommendations->display(); ?>
            </div>
			<?php $this->functions->review_existing_terms(); ?>
       <?php
	}
}
