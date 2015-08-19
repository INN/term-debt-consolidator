<?php

/**
 * Render a template by specifying a filename and context.
 *
 * @param (string) $template -- the filename of the template to render.
 * @param (array) $context -- associative array of values used within the template.
 *
 * @since 0.1
 */
function tdc_render_template($template, $context=false) {
	if (!empty($context))
		extract($context);

	include TDC_TEMPLATE_DIR . '/' . $template;
}
