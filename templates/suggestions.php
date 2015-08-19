<div id="tdc-suggestions" class="wrap">
	<h2>Term Debt Consolidator Suggestions</h2>

	<div class="tdc-preamble">
		<p>Term Debt Consolidator can look at your site's categories and tags to suggest areas for consolidation and deletion and help you identify ways to improve your use of WordPress' taxonomy functionality.</p>
		<p>Keep in mind that this process is imperfect and may make nonsensical suggestions from time to time. It's up to you to review the suggestions and approve or deny them.</p>
	</div>

	<form id="tdc-suggestions-request">
		<p class="submit">
			<input type="submit" id="submit" class="button button-primary button-large" value="Request suggestions"></input>
			<span class="spinner"></span>
		</p>
	</form>

	<div id="tdc-suggestions-list"></div>
</div>

<script type="text/template" id="tdc-suggestion-tmpl">
	<div class="tdc-suggestion">
		<% _.each(group, function(term, k) {
			if (k == 0) { %>
				<h4>Main term: <%= term.name %></h4>
				<p>Terms to consolidate:</p>
				<ul>
			<% } else { %>
				<li><%= term.name %></li>
			<% } %>
			</ul>
		<% }) %>
	</div>
</script>
