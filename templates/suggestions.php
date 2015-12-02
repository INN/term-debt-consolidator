<div id="tdc-suggestions" class="wrap">
	<h2>Term Debt Consolidator Suggestions</h2>

	<div class="tdc-preamble">
		<p>Term Debt Consolidator can look at your site's tags to suggest areas for consolidation and deletion and help you identify ways to improve your use of WordPress' taxonomy functionality.</p>
		<p>Keep in mind that this process is imperfect and may make nonsensical suggestions from time to time. It's up to you to review the suggestions and approve or deny them.</p>
	</div>

	<div id="tdc-suggestions-request">
		<a class="tdc-generate-suggestions button button-primary" href="#"><?php if ( $existing ) { ?>Regenerate<?php } else { ?>Generate<?php } ?> suggestions</a>
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
	var TDC = <?php echo json_encode(tdc_json_obj(array('taxonomy' => 'post_tag'))); ?>;
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
