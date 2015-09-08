=== Easy Table of Contents ===
Contributors: shazahm1@hotmail.com
Donate link: http://connections-pro.com/
Tags: table of contents, toc
Requires at least: 3.2
Tested up to: 4.3
Stable tag: 1.0
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

None yet.

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

= 1.0 09/08/2015 =
* Initial release.
  - Complete refactor and restructure of the original code for better design separation of function to make code base much easier to maintain.
  - Update all third party libraries.
  - Make much better use of the WordPress Settings API.
  - Minified CSS and JS files are used by default. Using SCRIPT_DEBUG will use the un-minified versions.
  - Add substantial amounts of phpDoc for developers.
  - Add many hooks to permit third party integrations.
  - Widget can be affixed/stuck to the page so it is always visible.
  - Widget will highlight the table of content sections that are currently visible in the browser viewport.
  - Use wpColorPicker instead of farbtastic.
  - Remove all shortcodes.
  - Per post options are saved in post meta instead of set by shortcode.

== Frequently Asked Questions ==

None yet.

== Upgrade Notice ==

= 1.0 =
Initial release.
