# Term Debt Consolidator

Term Debt Consolidator can look at your WordPress site's tags to suggest areas for consolidation and deletion and help you identify ways to improve your use of WordPress' taxonomy functionality.

Keep in mind that this process is imperfect and may make nonsensical suggestions from time to time. It's up to you to review the suggestions and approve or deny them.

**Contributors:** The INN Nerds (Ryan Nagle, Adam Schweigert)

**Tags:** taxonomy, tags

**License:** GPLv2 or later

**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

## Installation

Term Debt Consolidator can be installed like any other WordPress plugin.

1. Download the contents of this repository.
2. Unzip the package and rename the folder to "term-debt-consolidator" (the folder will be called "term-debt-consolidator-master" but this may cause problems if you don't rename it before uploading to your site)
3. Upload the folder to your WordPress installation in the wp-content/plugins directory
4. Login to WordPress, click on Plugins in the left hand menu
5. Select the Term Debt Consolidator plugin and click "activate"
6. Use the plugin via the Term Debt Consolidator link in the WordPress dashboard

Installation directly from the WordPress.org plugin directory coming soon!

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
