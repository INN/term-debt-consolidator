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
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param  Term_Debt_Consolidator $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		// Set our title.
		$this->title = esc_attr__( 'Term Debt Consolidator', 'term-debt-consolidator' );
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

	}

	/**
	 * Register our setting to WP.
	 *
	 * @since  1.0.0
	 */
	public function admin_init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page.
	 *
	 * @since  1.0.0
	 */
	public function add_options_page() {
		$this->options_page = add_menu_page(
			$this->title,
			$this->title,
			'edit_posts',
			$this->key,
			array( $this, 'admin_page_display' ),
			'dashicons-networking'
		);
	}

	/**
	 * Admin page markup.
	 *
	 * @since  1.0.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap options-page <?php echo esc_attr( $this->key ); ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
		</div>
		<?php
	}
}
