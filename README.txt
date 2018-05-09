=== Easy Table of Contents ===
Contributors: shazahm1@hotmail.com
Donate link: http://connections-pro.com/
Tags: table of contents, toc
Requires at least: 4.4
Tested up to: 4.9
Requires PHP: 5.3
Stable tag: 1.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a user friendly and fully automatic way to create and display a table of contents generated from the page content.

== Description ==

A user friendly, featured focused plugin which allows you to insert a table of contents into your posts, pages and custom post types.

= Features =
* Automatically generate a table of contents for your posts, pages and custom post types by parsing its contents for headers.
* Optionally enable for pages and/or posts. Custom post types are supported, as long as their content is output with the `the_content()` template tag.
* Optionally auto insert the table of contents into the page, selectable by enabled post type.
* Provides many easy to understand options to configure when and where to insert the table of contents.
* Many options are available to configure how the inserted table of contents appears which include several builtin themes. If the supplied themes do no meet you needs, you can create your own by choosing you own colors for the border, background and link color.
* Multiple counter bullet formats to choose from; none, decimal, numeric and roman.
* Choose to display the table of contents hierarchical or not. This means headings of lower priority will be nested under headings of higher priority.
* User can optionally hide the table of contents. You full control of this feature. It can be disabled and you can choose to have it hidden by default.
* Supports smooth scrolling.
* Selectively enable or disabled the table of contents on a post by post basis.
* Choose which headings are used to generate the table of contents. This too can be set on a post by post basis.
* Easily exclude headers globally and on a post by post basis.
* If you rather not insert the table of contents in the post content, you can use the supplied widget and place the table of contents in your theme's sidebar.
* The widgets supports being affixed or stuck on the page so it is always visible as you scroll down the page. NOTE: this is an advanced option since every theme is different, you might need support from your theme developer to learn what the correct item selector to use in the settings to enable this feature.
* The widget auto highlights the sections currently visible on the page. The highlight color is configurable.
* Developer friendly with many action hooks and filters available. More can be added by request on [Github](https://github.com/shazahm1/Easy-Table-of-Contents). Pull requests are welcomed.

= Live Examples =

Here are links to documentation pages for several of the premium templates for the [Connections Business Directory plugin](https://wordpress.org/plugins/connections/) which utilize the widget included with this plugin:

* [cMap Template Docs](http://connections-pro.com/documentation/cmap/)
* [Circled Template Docs](http://connections-pro.com/documentation/circled/)
* [Gridder Template Docs](http://connections-pro.com/documentation/gridder/)

= Roadmap =
* Fragment caching for improved performance.
* Support for `<!--nextpage-->`.
* Customizer support.

= Requirements =

* **WordPress version:** >= 3.2
* **PHP version:** >= 5.2.4

= Credit =

Easy Table Contents is a fork of the excellent [Table of Contents Plus](https://wordpress.org/plugins/table-of-contents-plus/) plugin by [Michael Tran](http://dublue.com/plugins/toc/).

== Screenshots ==

1. The General section of the settings.
2. The Appearance section of the settings.
3. The Advanced section of the settings.

== Installation ==

= Using the WordPress Plugin Search =

1. Navigate to the `Add New` sub-page under the Plugins admin page.
2. Search for `easy table of contents`.
3. The plugin should be listed first in the search results.
4. Click the `Install Now` link.
5. Lastly click the `Activate Plugin` link to activate the plugin.

= Uploading in WordPress Admin =

1. [Download the plugin zip file](http://wordpress.org/plugins/easy-table-of-contents/) and save it to your computer.
2. Navigate to the `Add New` sub-page under the Plugins admin page.
3. Click the `Upload` link.
4. Select Easy Table of Contents zip file from where you saved the zip file on your computer.
5. Click the `Install Now` button.
6. Lastly click the `Activate Plugin` link to activate the plugin.

= Using FTP =

1. [Download the plugin zip file](http://wordpress.org/plugins/easy-table-of-contents/) and save it to your computer.
2. Extract the Easy Table of Contents zip file.
3. Create a new directory named `easy-table-of-contents` directory in the `../wp-content/plugins/` directory.
4. Upload the files from the folder extracted in Step 2.
4. Activate the plugin on the Plugins admin page.

== Changelog ==

= 1.7 05/09/2018 =
* NEW: Introduce the `ez_toc_shortcode` filter.
* TWEAK: Fix notices due to late eligibility check. props unixtam
* TWEAK: Tweak eligibility check to support the TOC widget.
* TWEAK: Prefix a few CSS classes in order to prevent collisions with theme's and other plugins.
* TWEAK: Avoid potential PHP notice in admin when saving the post by checking for nonce before validating it.
* TWEAK: Using the shortcode now overrides global options.
* TWEAK: `the_content()` now caches result of `is_eligible()`.
* TWEAK: Refactor to pass the WP_Post object internally vs. accessing it via the `$wp_query->post` which may not in all cases exist.
* TWEAK: Use `pre_replace()` to replace one or more spaces with an underscore.
* TWEAK: Return original title in the `ez_toc_url_anchor_target` filter.
* TWEAK: Strip `&nbsp;`, replacing it with a space character.
* TWEAK: Minor tweaks to the in page URL creating.
* TWEAK: Wrap TOC list in a nav element.
* TWEAK: Init plugin on the `plugins_loaded` hook.
* TWEAK: Tweak the minimum number of headers to 1.
* BUG: The header options from the post meta should be used when building the TOC hierarchy, not the header options from the global settings.
* BUG: Do not double escape field values.
* BUG: Ensure Apostrophe / Single quote use in Exclude Headings work.
* OTHER: Update CSS to include the newly prefixed classes.
* DEV: Remove some commented out unused code.

= 1.6.1 03/16/2018 =
* TWEAK: Revert change made to allow HTML added via the `ez_toc_title` filter as it caused undesirable side effects.
* BUG: Ensure Smooth Scroll Offset is parsed as an integer.

= 1.6 03/15/2018 =
* NEW: Add `px` option for font size unit.
* NEW: Add title font size and weight settings options.
* NEW: Add the Mobile Smooth Scroll Offset option.
* TWEAK: Change default for font size unit from `px` to `%` to match the default options values.
* TWEAK: Correct CSS selector so margin is properly applied between the title and TOC items.
* TWEAK: Honor HTML added via `ez_toc_title` filter.
* TWEAK: Ensure the ezTOC content filter is not applied when running `the_content` filter.
* TWEAK: Only enqueue the javascript if the page is eligible for a TOC.
* TWEAK: Update icomoon CSS to remove unecessary CSS selectors to prevent possible conflicts.
* TWEAK: The smooth scroll offset needs to be taken into account when defining the offset_top property when affixing the widget.
* OTHER: Update frontend minified CSS file.
* OTHER: Update the frontend minified javascript file.
* DEV: phpDoc corrections.

= 1.5 02/20/2018 =
* BUG: Correct CSS selector to properly target the link color.
* OTHER: Update the WayPoints library.
* DEV: Add a couple @todo's.

= 1.4 01/29/2018 =
* TWEAK: Change text domain from ez_toc to easy-table-of-contents.
* TWEAK: Rename translation files with correct text domain.
* BUG: Ensure page headers are processed to add the in page header link when using the shortcodes.
* BUG: Add forward slash to domain path in the plugin header.
* I18N: Update POT file.
* I18N: Update Dutch (nl_NL) translation.

= 1.3 12/18/2017 =
* FEATURE: Add support for the `[ez-toc]` shortcode.
* NEW: For backwards compatibility with "Table of Content Plus", register the `[toc]` shortcode.
* NEW: Introduce the `ez_toc_extract_headings_content` filter.
* TWEAK: Update the tested to and required readme header text.
* TWEAK: Do not show the widget on the 404, archive, search and posts pages.
* I18N: Add the nl_NL translation.

= 1.2 04/29/2016 =
* TWEAK: Remove the font family from styling the TOC title header.
* TWEAK: Pass the raw title to the `ez_toc_title` filter.
* BUG: A jQuery 1.12 fix for WordPress 4.5.

= 1.1 02/24/2016 =
* FEATURE: Add option to replace header wither alternate header text in the table of content.
* NEW: Introduce the ez_toc_filter.
* NEW: Introduce ezTOC_Option::textarea() to render textareas.
* NEW: Introduce array_search_deep() to recursively search an array for a value.
* TWEAK: Run table of contents headers thru wp_kses_post().
* TWEAK: Escape URL.
* TWEAK: Count excluded headings only once instead of multiple times.
* TWEAK: Escape translated string before rendering.
* TWEAK: Use wp_unslash() instead of stripslashes().
* TWEAK: Escape translated string.
* BUG: Fix restrict path logic.
* OTHER: Readme tweaks.
* I18N: Add POT file.
* I18N: Add Dutch translation.
* DEV: Update .gitignore to allow PO files.
* DEV: phpDoc fix.

= 1.0 09/08/2015 =
* Initial release.
  - Complete refactor and restructure of the original code for better design and separation of function to make code base much easier to maintain and extend.
  - Update all third party libraries.
  - Make much better use of the WordPress Settings API.
  - Minified CSS and JS files are used by default. Using SCRIPT_DEBUG will use the un-minified versions.
  - Add substantial amounts of phpDoc for developers.
  - Add many hooks to permit third party integrations.
  - Widget can be affixed/stuck to the page so it is always visible.
  - Widget will highlight the table of content sections that are currently visible in the browser viewport.
  - Widget will now generate table of contents using output from third party shortcodes.
  - Use wpColorPicker instead of farbtastic.
  - Remove all shortcodes.
  - Per post options are saved in post meta instead of set by shortcode.

== Frequently Asked Questions ==

= Ok, I've installed this... what do I do next? =

You first stop should be the Table of Contents settings admin page. You can find this under the Settings menu item.

You first and only required decision is you need to decide which post types you want to enable Table of Contents support for. By default it is the Pages post type. If on Pages is the only place you plan on using Table of Contents, you have nothing to do on the Settings page. To keep things simple, I recommend not changing any of the other settings at this point. Many of the other settings control when and where the table of contents is inserted and changing these settings could cause it not to display making getting started a bit more difficult. After you get comfortable with how this works... then tweak away :)

With that out of the way make sure to read the **How are the tables of contents created?** FAQ so you know how the Table of Contents is automatically generated. After you have the page headers setup, or before, either way... Scroll down on the page you'll see a metabox named "*Table of Contents*", enable the *Insert table of contents.* option and Update and/or Publish you page. The table of contents should automatically be shown at the top of the page.

= How are the tables of contents created? =

The table of contents is generated by the headers found on a page. Headers are the [`<h1>,<h2>,<h3>,<h4>,<h5>,<h6>` HTML tags](http://www.w3schools.com/tags/tag_hn.asp). If you are using the WordPres Visual Post Editor, these header tags are used and inserted into the post when you select one of the [*Heading n* options from the formatting drop down](http://torquemag.io/wordpress-heading-tags/). Each header that is found on the page will create a table of content item. Here's an example which will create a table of contents containing the six items.

`<h1>Item 1</h1>
<h1>Item 2</h1>
<h1>Item 3</h1>
<h1>Item 4</h1>
<h1>Item 5</h1>
<h1>Item 6</h1>`

You can also create "nested" table of contents. This is difficult to explain so I'll illustrate building on the previous example. In this example a table of contents will be created with the same six items but now the first three will each an child item nested underneath it. The indentation is not necessary, it was only added for illustration purposes.

`<h1>Item 1</h1>
    <h2>Item 1.1 -- Level 2</h2>
<h1>Item 2</h1>
    <h2>Item 2.1 -- Level 2</h2>
<h1>Item 3</h1>
    <h2>Item 3.1 -- Level 2</h2>
<h1>Item 4</h1>
<h1>Item 5</h1>
<h1>Item 6</h1>`

You are not limited to a single a single nested item either. You can add as many as you need. You can even create multiple nested levels...

`<h1>Item 1</h1>
    <h2>Item 1.1 -- Level 2</h2>
        <h3>Item 1.1.1 -- Level 3</h3>
        <h3>Item 1.1.2 -- Level 3</h3>
        <h3>Item 1.1.3 -- Level 3</h3>
    <h2>Item 1.2 -- Level 2</h2>
      <h3>Item 1.2.1 -- Level 3</h3>
      <h3>Item 1.2.2 -- Level 3</h3>
      <h3>Item 1.2.3 -- Level 3</h3>
    <h2>Item 1.3 -- Level 2</h2>
<h1>Item 2</h1>
    <h2>Item 2.1 -- Level 2</h2>
    <h2>Item 2.2 -- Level 2</h2>
<h1>Item 3</h1>
    <h2>Item 3.1 -- Level 2</h2>
    <h2>Item 3.2 -- Level 2</h2>
<h1>Item 4</h1>
<h1>Item 5</h1>
<h1>Item 6</h1>`

You can nest up 6 levels deep if needed. I hope this helps you understand how to create and build your own auto generated table of contents on your sites!

== Upgrade Notice ==

= 1.0 =
Initial release.

= 1.3 =
Requires WordPress >= 4.4 and PHP >= 5.3. PHP version >= 7.1 recommended.

= 1.4 =
Requires WordPress >= 4.4 and PHP >= 5.3. PHP version >= 7.1 recommended.

= 1.5 =
Requires WordPress >= 4.4 and PHP >= 5.3. PHP version >= 7.1 recommended.

= 1.6 =
Requires WordPress >= 4.4 and PHP >= 5.3. PHP version >= 7.1 recommended.

= 1.6.1 =
Requires WordPress >= 4.4 and PHP >= 5.3. PHP version >= 7.1 recommended.

= 1.7 =
Requires WordPress >= 4.4 and PHP >= 5.3. PHP version >= 7.1 recommended.
