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
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Process actions
	 *
	 * @since  1.0.0
	 */
	public function admin_init() {
		// Process actions here
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

	public function enqueue_scripts() {
		wp_register_style(
			'tdc-common',
			plugins_url( 'styles.css', dirname( __FILE__ ) )
		);

		wp_register_script(
			'tdc-suggestions',
			plugins_url( '/assets/js/suggestions.js', dirname( __FILE__ ) ),
			array( 'underscore', 'backbone', 'jquery-ui-progressbar' ),
			'1.0',
			true
		);

		if ( isset( $_GET['page'] ) && 'term_debt_consolidator_admin' === $_GET['page'] ) {
			wp_enqueue_style( 'tdc-common' );
			wp_enqueue_script( 'backbone' );
			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'jquery-ui-progressbar' );
			wp_enqueue_script( 'tdc-suggestions' );
		}
	}
}
