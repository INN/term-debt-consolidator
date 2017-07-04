<?php
/**
 * Term Debt Consolidator Plugin List Table.
 *
 * @since   1.0.0
 * @package Term_Debt_Consolidator
 */

/**
 * Term Debt Consolidator Plugin List Table.
 *
 * @since 1.0.0
 */
class TDC_Plugin_List_Table extends WP_List_Table {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var   Term_Debt_Consolidator
	 */
	protected $plugin = null;

	/**
	 * Screen variable.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $screen = '';

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param  Term_Debt_Consolidator $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->screen = get_current_screen();
	}

	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items() {
		global $wpbd;
		$per_page = 5;

		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$args = array(
			'post_type' => 'tdc_recommendations',
		);
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {

				$query->the_post();

				// Loop through taxonomies and get terms
				$enabled_taxonomies = apply_filters( 'tdc_enabled_taxonomies', array( 'category', 'post_tag' ) );
				foreach( $enabled_taxonomies as $taxonomy ) {

					$terms = wp_get_post_terms( $query->post->ID, $taxonomy );
					if ( $terms ) {
						$term_output = [];
						$term_ids = [];
						foreach ( $terms as $term ) {
							$term_ids[] = $term->term_id;
							$term_output['list'][] = '<a href="/wp-admin/term.php?taxonomy=' . $taxonomy . '&tag_ID=' . $term->term_id . '" id="term-' . $term->term_id . '">' . $term->name . '</a>';
							$term_output['action'][] = '<option value="' . $term->term_id . '">' . $term->name . '</option>';
						}
						$post_terms = array(
							'tax' => $taxonomy,
							'terms' => implode( $term_output['list'], ' <br />' ),
						);
					}
				}

				$actions = '<form action="/wp-admin/admin-post.php" method="post">
	<input type="hidden" name="action" value="tdc_merge">
	<input type="hidden" name="recommendation" value="' . $query->post->ID . '">
	<input type="hidden" name="merge_terms" value="' . implode( $term_ids, ',' ) . '">
	<select name="primary_term"><option>Select a Primary Term</option>' . implode( $term_output['action'], ' <br />' ) . '</select>
	<input type="submit" value="Merge" class="button button-large">
</form>';

				$data[] = array(
					'id' => $query->post->ID,
					'taxonomy' => $post_terms['tax'],
					'terms' => $post_terms['terms'],
					'actions' => $actions,
				);

			}
			wp_reset_postdata();

			usort( $data, array( &$this, 'sort_data' ) );
			$currentPage = $this->get_pagenum();
			$totalItems = count( $data );
			$this->set_pagination_args( array(
				'total_items' => $totalItems,
				'per_page'    => $per_page,
			) );
			$data = array_slice( $data, ( ( $currentPage - 1 ) * $per_page ), $per_page );
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->items = $data;
		}
	}
	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns() {
		$columns = array(
			'id'          => 'Recommendation ID',
			'taxonomy'    => 'Taxonomy',
			'terms'       => 'Terms',
			'actions'     => 'Actions',
		);
		return $columns;
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns() {
		return array(
			'id' => array( 'id', false ),
			'taxonomy' => array( 'taxonomy', false ),
		);
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'id':
			case 'taxonomy':
			case 'terms':
			case 'actions':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ) ;
		}
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @return Mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults
		$orderby = 'id';
		$order = 'desc';
		// If orderby is set, use this as the sort column
		if ( ! empty( $_GET['orderby'] ) ) {
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order
		if ( ! empty( $_GET['order'] ) ) {
			$order = $_GET['order'];
		}

		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		if( 'asc' === $order ) {
			return $result;
		}
		return -$result;
	}
}
