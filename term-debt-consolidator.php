<?php
/**
 * Plugin Name: Term Debt Consolidator
 * Plugin URI:  https://labs.inn.org
 * Description: TDC evaluates your tags and categories, suggests consolidations and helps identify ways to improve your use of WordPress' built-in taxonomies.
 * Version:     1.0.0
 * Author:      innlabs
 * Author URI:  https://labs.inn.org
 * Donate link: https://labs.inn.org
 * License:     GPLv2
 * Text Domain: term-debt-consolidator
 * Domain Path: /languages
 *
 * @link    https://labs.inn.org
 *
 * @package Term_Debt_Consolidator
 * @version 1.0.0
 *
 * Built using generator-plugin-wp (https://github.com/WebDevStudios/generator-plugin-wp)
 */

/**
 * Copyright (c) 2017 innlabs (email : labs@inn.org)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


// Include additional php files here.
require 'includes/class-post-type.php';
require 'includes/class-functions.php';
require 'includes/class-admin.php';
require 'includes/class-cli.php';

if ( ! function_exists( 'get_column_headers' ) ) {
	require dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-admin/includes/screen.php';
}
if ( ! class_exists( 'WP_List_Table' ) ) {
	require 'includes/class-wp-list-table.php';
}
require 'includes/class-plugin-list-table.php';

/**
 * Main initiation class.
 *
 * @since  1.0.0
 */
final class Term_Debt_Consolidator {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	const VERSION = '1.0.0';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages.
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    Term_Debt_Consolidator
	 * @since  1.0.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of TDC_Admin
	 *
	 * @since1.0.0
	 * @var TDC_Admin
	 */
	protected $admin;

	/**
	 * Instance of TDC_Functions
	 *
	 * @since1.0.0
	 * @var TDC_Functions
	 */
	protected $functions;

	/**
	 * Instance of TDC_Post_Type
	 *
	 * @since1.0.0
	 * @var TDC_Post_Type
	 */
	protected $post_type;

	/**
	 * Instance of TDC_Cli
	 *
	 * @sinceundefined
	 * @var TDC_Cli
	 */
	protected $cli;

	/**
	 * Instance of TDC_Plugin_List_Table
	 *
	 * @since1.0.0
	 * @var TDC_Plugin_List_Table
	 */
	protected $plugin_list_table;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   1.0.0
	 * @return  Term_Debt_Consolidator A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  1.0.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  1.0.0
	 */
	public function plugin_classes() {

		$this->admin = new TDC_Admin( $this );
		$this->functions = new TDC_Functions( $this );
		$this->post_type = new TDC_Post_Type( $this );
		$this->cli = new TDC_Cli( $this );
		$this->plugin_list_table = new TDC_Plugin_List_Table( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters.
	 * Priority needs to be
	 * < 10 for CPT_Core,
	 * < 5 for Taxonomy_Core,
	 * and 0 for Widgets because widgets_init runs at init priority 1.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Activate the plugin.
	 *
	 * @since  1.0.0
	 */
	public function _activate() {
		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		$this->functions = new TDC_Functions( $this );
		$this->functions->review_existing_terms();

		// @TODO remove
		/*
		$dismissed_suggestions_table = $wpdb->prefix . "tdc_dismissed_suggestions";

		global $wpdb;
		$result = $wpdb->query("
			CREATE TABLE IF NOT EXISTS `" . $dismissed_suggestions_table . "` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`term_id` bigint(20) unsigned NOT NULL,
				`taxonomy` varchar(450) NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `term_id` (`term_id`)
			) ENGINE=InnoDB;");
		*/

		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin.
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since  1.0.0
	 */
	public function _deactivate() {
		// Add deactivation cleanup functionality here.
		global $wpdb;
		$wpdb->delete( 'wp_posts', array( 'post_type' => 'tdc_recommendations' ) );
		delete_option( 'tdc_status' );
	}

	/**
	 * Init hooks
	 *
	 * @since  1.0.0
	 */
	public function init() {

		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Load translated strings for plugin.
		load_plugin_textdomain( 'term-debt-consolidator', false, dirname( $this->basename ) . '/languages/' );

		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  1.0.0
	 *
	 * @return boolean True if requirements met, false if not.
	 */
	public function check_requirements() {

		// Bail early if plugin meets requirements.
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

		// Deactivate our plugin.
		add_action( 'admin_init', array( $this, 'deactivate_me' ) );

		// Didn't meet the requirements.
		return false;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  1.0.0
	 */
	public function deactivate_me() {

		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Check that all plugin requirements are met.
	 *
	 * @since  1.0.0
	 *
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {

		// Do checks for required classes / functions or similar.
		// Add detailed messages to $this->activation_errors array.
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since  1.0.0
	 */
	public function requirements_not_met_notice() {

		// Compile default message.
		$default_message = sprintf( __( 'Term Debt Consolidator is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'term-debt-consolidator' ), admin_url( 'plugins.php' ) );

		// Default details to null.
		$details = null;

		// Add details if any exist.
		if ( $this->activation_errors && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// Output errors.
		?>
		<div id="message" class="error">
			<p><?php echo wp_kses_post( $default_message ); ?></p>
			<?php echo wp_kses_post( $details ); ?>
		</div>
		<?php
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $field Field to get.
	 * @throws Exception     Throws an exception if the field is invalid.
	 * @return mixed         Value of the field.
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'admin':
			case 'functions':
			case 'post_type':
			case 'cli':
			case 'plugin_list_table':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}
}

/**
 * Grab the Term_Debt_Consolidator object and return it.
 * Wrapper for Term_Debt_Consolidator::get_instance().
 *
 * @since  1.0.0
 * @return Term_Debt_Consolidator  Singleton instance of plugin class.
 */
function term_debt_consolidator() {
	return Term_Debt_Consolidator::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( term_debt_consolidator(), 'hooks' ) );

// Activation and deactivation.
register_activation_hook( __FILE__, array( term_debt_consolidator(), '_activate' ) );
register_deactivation_hook( __FILE__, array( term_debt_consolidator(), '_deactivate' ) );
