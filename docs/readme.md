# WordPress Term Debt Consolidator

As posts are created and assigned catgories and tags over time, the number of terms can grow quite large. Without careful curation, a site can wind up with multiple terms that mean the same thing. Instead of organizing site content and making posts in every topic easily findable, multiple terms for the same basic subject can fragment the site and reduce usability. 

Term Debt Consolidator can analyze your WordPress site's Categories and Tags (and optionally custom taxonomies) to identify terms that might be redundant. It will then suggest terms for consolidation and deletion to improve the effectiveness of WordPress's taxonomy functionality.

Keep in mind that this process is imperfect and may make some nonsensical suggestions and this plugin uses a fairly simple algorithm to identify similar terms. It's up to you to review the suggestions and approve or dismiss them. If your site has hundreds of terms, it might take some time to go through the report provided by the Term Debt Consolidator. But you'll end up with an efficient set of terms that truly helps people find your content.

## Installation

Term Debt Consolidator can be installed like any other WordPress plugin.

1. In the WordPress Dashboard go to **Plugins**, then click the **Add Plugins** button and search the WordPress Plugins Directory for Term Debt Consolidator. Alternatively, you can download the zip file from this Github repo and upload it manually to your WordPress site.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the plugin via the **Term Debt Consolidator** link in the WordPress dashboard

![Term Debt Consolidator in the WordPress Dashboard](./img/term-debt-consolidator-dashboard.png)

## Using the Term Debt Consolidator

Choose which taxonomy you wish to have the plugin review for consolidation. By default you can choose between Tags and Categories. _If you have any custom taxonomies you'd like to consolidate, please see the instructions for [adding taxonomies to the plugin](taxonomies.md)_.

Hit the **Generate Suggestions** button and after processing the taxonomy, you'll get a list of suggested terms to consolidate:

![Term Debt Consolidator suggested terms to consolidate](./img/term-debt-consolidator-suggestions.png)

Note what you can do with the list of suggestions:

- Edit any of the terms listed
- View posts for the term on the term archive page
- Make primary one of the terms being compared, so as to consolidate the terms
- Remove one of the terms being compared

If the Term Debt Consolidator suggests consolidating terms you wish to keep separate, you can **Dismiss this suggestion**:

![Dismiss link in the Term Debt Consolidator](./img/term-debt-consolidator-dismiss.png)

After dismissing the suggestion, Term Debt Consolidator won't show it again.

## Choosing Which Term to Make Primary

Of course for any given pair of terms being suggested for consolidation, it's your choice which term to make primary or remove. Just note that for each pair the Term Debt Consolidator displays two rows for each pair:

![Term Debt Consolidator suggested terms to consolidate](./img/term-debt-consolidator-suggestion-rows.png)

In this case we'd probably want to make "Politics" the primary term, and remove "Politic" which was probably a typo in the first place. 

So we'd want to:

- First click "Make primary" for "Politics"
- Then click "Remove" for "Politic"
- Finally, click the **Apply consolidation** button

This will assign the term you made primary to all the posts that had the term being removed. It will also delete the removed term in **Posts > Categories**, or **Posts > Tags**, or a custom taxonomy if you're using one with the plugin.

## Regenerating Term Debt Consolidator Suggestions

Over time as more tags are added to posts, you may want to return to the Term Debt Consolidator for additional cleanup. Just remember that if you dismiss a suggestion the plugin will remember the dismissal, and it won't suggest consolidating those terms again.
