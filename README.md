# Easy Table of Contents 
Contributors: magazine3  
Donate link: https://tocwp.com/  
Tags: table of contents, toc  
Requires at least: 5.0  
Tested up to: 6.8  
Requires PHP: 5.6.20  
Stable tag: 2.0.75 
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Adds a user friendly and fully automatic way to create and display a table of contents generated from the page content.

### Description

A user friendly, featured focused [Easy Table of Contents](https://tocwp.com/) plugin which allows you to insert a table of contents into your posts, pages and custom post types.

[Home](https://tocwp.com/) | [Help & Tech Support](https://tocwp.com/contact/) | [Documentation](https://tocwp.com/docs/)  | [Pro version Features](https://tocwp.com/pricing/)

### Features 
* <strong>NEW </strong>: Migration Tool to import all the settings from other plugins like Table of Content Plus & more
* Automatically generate a table of contents for your posts, pages and custom post types by parsing its contents for headers.
* Supports the `<!--nextpage-->` tag.
* Supports the Rank Math plugin.
* Works with the Classic Editor, Gutenberg, Divi, Elementor, WPBakery Page Builder and Visual Composer page editors.
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
* An option to show toc based on dynamic paragraph count.
* An option which Preserve Line Breaks in TOC.
* An option to use the TOC without anchor links in the URL.
* Support for custom taxonomy description.
* Adds SiteNavigation Schema.

= TAKE IT A STEP FURTHER WITH EASY TABLE OF CONTENTS PRO =
With [EASY TABLE OF CONTENTS PRO](https://tocwp.com/pricing/) You will get access to more professional Settings, options and positions to really push your TOC to the next level.
* <strong>New - Gutenberg Block </strong>: Easily create TOC in Gutenberg block without the need of any coding or shortcode.
* <strong>New - Elementor Widget </strong>: Easily create TOC using Elementor Widget without the need of any coding or shortcode.
* <strong>New - Fixed/Sticky TOC </strong>: Users can find the content that they need, much faster through the option of sticky TOC.
* <strong>New - Fixed/Sticky TOC Customization </strong>: Customize the appearance of Sticky TOC with Theme Design options.
* <strong>New - Full AMP Support </strong>: Generates a TOC with your existing setup and make it AMP compatible automatically.
* <strong>NEW – ACF Support </strong>: Easily create TOC with your custom ACF fields.
* <strong>NEW – View More </strong>: Show selected number of TOC heading before user clicks to show remaining headings.
* <strong>NEW – Read Time </strong>: Show time of read for your posts/pages.
* <strong>NEW – Collapsable Sub Headings </strong>: Show/Hide sub headings of the Table of contents.
* <strong>NEW – Highlight Headings </strong>: Sticky heading highlight while scrolling through the content.

### Shortcode

With Our shortcode feature you are in command of the table of contents with very little effort and even if you have little to no programming skills.

[ez-toc] Would generate the table of contents. 

Below are the attibutes we support and could be useful in configuring the table of contents:

[header_label="Title"] – title for the table of contents
[display_header_label="no"] - no title for the table of contents
[toggle_view="no"] – no toggle for the table of contents 
[initial_view="hide"] –  initially hide the table of contents 
[initial_view="show"] –  initially show the table of contents 
[display_counter="no"] – no counter for the table of contents
[post_types="post,page"] – post types seperated by ,(comma)
[post_in="1,2"] – ID's of the posts|pages seperated by ,(comma)
[post_not_in="1,2"] – ID's of the posts|pages seperated by ,(comma)
[device_target="desktop"] – mobile or desktop device support for the table of contents
[view_more="5"] – 5, is the number of headings loads on first view, before user interaction (PRO)
[class="custom_toc"] – add your own class to the TOC
[exclude="Test"] – exclude heading from TOC which contain text "Test"
[heading_levels="2,3"] - Show only heading h2 and h3 

### Support

We try our best to provide support on WordPress.org forums. However, We have a special [team support](https://magazine3.company/contact/) where you can ask us questions and get help. Delivering a good user experience means a lot to us and so we try our best to reply each and every question that gets asked.

### Bug Reports

Bug reports for Easy Table of Contents are [welcomed on GitHub](https://github.com/ahmedkaludi/Easy-Table-of-Contents). Please note GitHub is not a support forum, and issues that aren't properly qualified as bugs will be closed.

### [JOIN TELEGRAM GROUP COMMUNITY](https://t.me/+XADGN24lHNk0YjE1/)**: Purpose of this group is to get proper suggestions and feedback from plugin users and the community so that we can make the plugin even better.

### Roadmap 
* Fragment caching for improved performance.
* Improve accessibility.
* Add Bullet and Arrow options for list counter style.
* [View Full Road Map](https://github.com/ahmedkaludi/Easy-Table-of-Contents/milestones)

### Credit 

Easy Table Contents is a fork of the excellent [Table of Contents Plus](https://wordpress.org/plugins/table-of-contents-plus/) plugin by [Michael Tran](http://dublue.com/plugins/toc/).

### Screenshots 

1. The General section of the settings.
2. The Appearance section of the settings.
3. The Advanced section of the settings.

### Installation 

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


### Frequently Asked Questions

= Ok, I've installed this... what do I do next? =

You first stop should be the Table of Contents settings admin page. You can find this under the Settings menu item.

You first and only required decision is you need to decide which post types you want to enable Table of Contents support for. By default it is the Pages post type. If on Pages is the only place you plan on using Table of Contents, you have nothing to do on the Settings page. To keep things simple, I recommend not changing any of the other settings at this point. Many of the other settings control when and where the table of contents is inserted and changing these settings could cause it not to display making getting started a bit more difficult. After you get comfortable with how this works... then tweak away

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

= Is there any shortcode to add the table of content to anywhere I want ? =

Yes you can add the TOC with this shortcode - [ez-toc] and with the help of this you can easily add the TOC in the content or anywhere in the WordPress and if you want to add the shortcode on the theme file then you can add it with the help of this code - <?php echo do_shortcode( â€˜[ez-toc]â€™ ); ?> and with this, you can add the TOC on any file according to your need.

== Contact | Help | Technical Support ==

[Contact Us](https://tocwp.com/contact/)