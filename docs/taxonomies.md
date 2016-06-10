## Default and Custom Taxonomies

By default, Term Debt Consolidator is enabled for post tags and category taxonomies.

If you have a custom taxonomy that you'd like TDC to evaluate and make suggestions, you can add the custom taxonomy using the `tdc_enabled_taxonomies` filter.

For example, if your custom taxonomy is `custom_taxonomy` you could add the following your theme's functions.php file:

    function my_custom_tdc_taxonomies($taxonomies) {
        $taxonomies[] = 'custom_taxonomy';
        return $taxonomies;
    }
    add_filter( 'tdc_enabled_taxonomies', 'my_custom_tdc_taxonomies', 10, 1 );

After adding `custom_taxonomy` to the list of enabled taxonomies, you will see your taxonomy available under "Choose a taxonomy" in the Term Debt Consolidator area of the WordPress dashboard.
