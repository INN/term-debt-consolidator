# Term Debt Consolidator

Term Debt Consolidator can look at your WordPress site's tags to suggest areas for consolidation and deletion and help you identify ways to improve your use of WordPress' taxonomy functionality.

Keep in mind that this process is imperfect and may make nonsensical suggestions from time to time. It's up to you to review the suggestions and approve or deny them.

**Contributors:** The INN Nerds (Ryan Nagle, Adam Schweigert)

**Tags:** taxonomy, tags

**License:** GPLv2 or later

**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

## Installation

Term Debt Consolidator can be installed like any other WordPress plugin.

1. Upload the plugin files to the `/wp-content/plugins/term-debt-consolidator` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the plugin via the Term Debt Consolidator link in the WordPress dashboard

## Enabled taxonomies

By default, Term Debt Consolidator is enabled for post tags and category taxonomies.

If you have a custom taxonomy that you'd like TDC to evaluate and make suggestions, you can add the custom taxonomy using the `tdc_enabled_taxonomies` filter.

For example, if your custom taxonomy is `custom_taxonomy`:

    function my_custom_tdc_taxonomies($taxonomies) {
        $taxonomies[] = 'custom_taxonomy';
        return $taxonomies;
    }
    add_filter( 'tdc_enabled_taxonomies', 'my_custom_tdc_taxonomies', 10, 1 );

After adding `custom_taxonomy` to the list of enabled taxonomies, you will see your taxonomy available under "Choose a taxonomy" in the Term Debt Consolidator area of the WordPress dashboard.

## Development

If you'd like to contribute to the project, please see our [contributing guidelines](contributing.md).
