<div id="tdc-suggestions" class="wrap">
	<h2>Term Debt Consolidator Suggestions</h2>

	<div class="tdc-preamble">
		<p>Term Debt Consolidator can look at your site's categories and tags to suggest areas for consolidation and deletion and help you identify ways to improve your use of WordPress' taxonomy functionality.</p>
		<p>Keep in mind that this process is imperfect and may make nonsensical suggestions from time to time. It's up to you to review the suggestions and approve or deny them.</p>
	</div>

	<div id="tdc-suggestions-request">
		<p class="fetching">Fetching suggestions... <span class="spinner"></span></p>
	</div>
	<div id="tdc-suggestions-list"></div>
	<div id="tdc-pagination-container"></div>
</div>

<script type="text/template" id="tdc-suggestion-tmpl">
	<div class="tdc-suggestion">
		<form data-primary-term="<%= group[0].term_id %>">
			<p>Terms to consolidate:</p>
			<ul>
			<% _.each(group, function(term, k) { %>
				<% if ( k == 0 ) { %>
					<li>
						<span class="tdc-term tdc-primary-term"><strong><%= term.name %></strong></span>
						<input type="hidden" name="consolidate-primary" value="<%= term.term_id %>" />
						<ul class="tdc-term-actions">
							<li><span class="tdc-primary-term-indicator">Primary</span> | </li>
							<li><a target="new" href="<%= term.url %>" class="tdc-view-posts">View posts</a></li>
						</ul>
					</li>
				<% } else { %>
					<li>
							<span class="tdc-term"><%= term.name %></span>
							<ul class="tdc-term-actions">
								<li><a href="#" class="tdc-make-primary">Make primary</a> | </li>
								<li><a href="#" class="tdc-remove-term">Remove</a> | </li>
								<li><a target="new" href="<%= term.url %>" class="tdc-view-posts">View posts<span class="dashicons dashicons-external"></span></a></li>
							</ul>
					</li>
				<% } %>
			<% }) %>
			</ul>
		</form>

		<div class="tdc-suggestion-actions">
			<ul>
				<li><a class="button button-primary" href="#" class="tdc-apply-consolidation">Apply consolidation</a></li>
				<li><a href="#" class="tdc-dismiss-suggestion">Dismiss this suggestion</a></li>
			</ul>
		</div>
	</div>
</script>

<script type="text/template" id="tdc-no-suggestion-tmpl">
	<div class="tdc-suggestion tdc-no-suggestion">
		<p>No suggestions for term: <strong><%= group[0].name %></strong></p>

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
