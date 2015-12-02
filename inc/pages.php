<?php

/**
 * Render the main TDC suggestions page
 *
 * @since 0.1
 */
function tdc_suggestions_page() {
	// TODO: $taxonomy should be something the user chooses
	$taxonomy = 'post_tags';
	$dismissed = tdc_get_dismissed_suggestions( $taxonomy );

	tdc_render_template( 'suggestions.php', array(
		'existing' => empty( $dismissed )
	) );
}
