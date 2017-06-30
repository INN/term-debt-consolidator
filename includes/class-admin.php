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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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

		$default = 'post_tag';
		$enabled_taxonomies = $this->functions->tdc_enabled_taxonomies();

		$existing = array();
		$taxonomies = array();

		foreach ( $enabled_taxonomies as $tax ) {
			$dissmissed_for_tax = $this->functions->tdc_get_dismissed_suggestions( $tax );
			$existing[ $tax ] = ! empty( $dissmissed_for_tax );
			$taxonomies[ $tax ] = get_taxonomy( $tax );
		}
		?>
		<div id="tdc-suggestions" class="wrap options-page <?php echo esc_attr( $this->key ); ?>">
			<h2>Term Debt Consolidator Suggestions</h2>
			<?php $this->functions->review_existing_terms(); ?>
<?php /*
			<div class="tdc-preamble">
				<p>Term Debt Consolidator looks at your site's tags to suggest areas for consolidation and deletion and help you identify ways to improve your use of WordPress' taxonomy functionality.</p>
				<p>Keep in mind that this process is imperfect and may make nonsensical suggestions from time to time. It's up to you to review the suggestions and approve or deny them.</p>
			</div>

			<div id="tdc-tax-selector" class="tdc-taxonomy-selector">
				<p>Choose a taxonomy:</p>
				<ul>
					<?php foreach ( $taxonomies as $tax_name => $tax ) { ?>
						<li><label for="<?php echo $tax_name ; ?>"><input <?php checked( 'post_tag', $tax_name); ?> type="radio" name="taxonomy" id="<?php echo $tax_name; ?>" value="<?php echo $tax_name; ?>" /> <?php echo ( ! empty( $tax->label) ) ? $tax->label : $tax_name; ?></label></li>
					<?php } ?>
				</ul>
			</div>

			<div id="tdc-suggestions-request">
				<a class="tdc-generate-suggestions button button-primary" href="#"><?php if ( $existing[ $default ] ) { ?>Regenerate<?php } else { ?>Generate<?php } ?> suggestions</a>
				<span class="spinner"></span>
				<div class="tdc-generate-suggestions-progress"></div>
			</div>

			<div id="tdc-fetching-suggestions">
				<p class="fetching">Fetching suggestions...</p>
			</div>

			<div id="tdc-suggestions-list"></div>
			<div id="tdc-pagination-container"></div>
		</div>

		<script type="text/javascript">
			var TDC = <?php echo json_encode( $this->functions->tdc_json_obj( array( 'taxonomy' => 'post_tag' ) ) ); ?>;
		</script>

		<script type="text/template" id="tdc-suggestion-tmpl">
			<form>
				<p>Terms to consolidate:</p>
				<ul><%= terms %></ul>
			</form>

			<div class="tdc-suggestion-actions">
				<ul>
					<li><a class="button button-primary tdc-apply-consolidation" href="#">Apply consolidation</a></li>
					<li><a href="#" class="tdc-dismiss-suggestion">Dismiss this suggestion</a></li>
					<li><span class="spinner"></span></li>
				</ul>
			</div>
		</script>

		<script type="text/template" id="tdc-primary-term-tmpl">
			<li data-term-id="<%= term.term_id %>">
				<span class="tdc-term tdc-primary-term"><strong><%= term.name %></strong> <span class="tdc-post-count">(Post count: <%= term.count %>)</span></span>
				<input type="hidden" name="primary_term_id" value="<%= term.term_id %>" />
				<ul class="tdc-term-actions">
					<li><span class="tdc-primary-term-indicator">Primary</span> | </li>
					<li><%= term.edit_url %> | </li>
					<li><a target="new" href="<%= term.url %>" class="tdc-view-posts">View posts<span class="dashicons dashicons-external"></span></a></li>
				</ul>
			</li>
		</script>

		<script type="text/template" id="tdc-secondary-term-tmpl">
			<li>
				<span class="tdc-term"><%= term.name %> <span class="tdc-post-count">(Post count: <%= term.count %>)</span></span>
				<input type="hidden" name="term_ids[]" value="<%= term.term_id %>" />
				<ul class="tdc-term-actions">
					<li><a href="#" class="tdc-make-primary" data-term-id="<%= term.term_id %>">Make primary</a> | </li>
					<li><a href="#" class="tdc-remove-term">Remove</a> | </li>
					<li><%= term.edit_url %> | </li>
					<li><a target="new" href="<%= term.url %>" class="tdc-view-posts">View posts<span class="dashicons dashicons-external"></span></a></li>
				</ul>
			</li>
		</script>

		<script type="text/template" id="tdc-no-suggestion-tmpl">
			<div class="tdc-no-suggestion">
				<p>No suggestions for term: <strong><%= term.name %></strong></p>
				<form>
					<input type="hidden" name="primary_term_id" value="<%= term.term_id %>" />
				</form>
				<div class="tdc-suggestion-actions">
					<ul>
						<li><a href="#" class="tdc-dismiss-suggestion">Dismiss</a></li>
					</ul>
				</div>
			</div>
		</script>

		<script type="text/template" id="tdc-pagination-tmpl">
			<div id="tdc-search-results-pagination">
				<a href="#" class="disabled prev button button-primary">Previous</a>
				<a href="#" class="disabled next button button-primary">Next</a>
				<span class="spinner"></span>
				<p class="tdc-page-count">Page <span class="tdc-page"></span> of <span class="tdc-total-pages"></span></p>
			</div>
		</script>
		<?php
*/
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
