=== Easy Table of Contents ===
Contributors: magazine3
Donate link: https://tocwp.com/
Tags: table of contents, toc
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 5.6.20
Stable tag: 2.0.77
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a user friendly and fully automatic way to create and display a table of contents generated from the page content.

== Description ==

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

== Screenshots ==

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

== Changelog ==
= 2.0.77 15/11/2025 =
* Improvement: Added hook/filter to modify product_category HTML content before rendering #911
* Improvement: Make two/three responsive columns easy table of contents #670
* Feature: Option to show TOC only when certain word count is present #912
* Feature: Create box title or legend for toc container #913
* Compatibility: Need to make a compatible with the SeedProd builder. #922

= 2.0.76 20/08/2025 =
* Bug: Heading not highlighting when using shortcode #900
* Bug: Conflict issue with GP Premium 2.4.1 Version. #901
* Bug: Youtube video not visible on frontend #893
* Improvement: TOC sticky widget improvements #904

= 2.0.75 30/06/2025 =
* Bug: TOC is not working on ACF fields #878
* Bug: Sticky TOC incorrectly adds 'ez-toc-section' span on Pages even when disabled for Page post type #884
* Bug: Issue with Tasty recipe plugin #891
* Bug : Error in recent update #886
* Compatibility: Easy TOC not picking up heading in ACF flexible headings components #896
* Improvement: option to hide the 'TOC' feature in the Classic Editor. #888

= 2.0.74 05/05/2025 =
* Bug: Accordion TOC disappears when clicking at the + / - icon #880
* Bug: The post content is scrolled to the top while using Sticky Widget #879
* Bug: Generatepress Elements not working #867
* Compatibility: PMPRO - File deleted after upload when TOC plugin is activated #877
* Compatibility: Blocksy child theme Compatibility #875
* Improvement: Redundant Title Text in TOC Causing Accessibility Warning #868
* New: Option to show/disable TOC on mobile/desktop on specific post/page #845
* Test: Need to test with WordPress 6.8 #882

= 2.0.73 12/03/2025 =
* New: Added a Sitemap Feature #835
* Tweak: Some TOC Headings Not Working When Added with Elementor Plugin #857
* Tweak: Improvement done in the Appearance Section of the Easy TOC Plugin #858
* New: Added an Option to Add Self-Referencing Links with Domain Anchors in Easy TOC #859
* New: Delete All Data When Uninstalling the Plugin #863
* Compatibility: Added compatibility with the theme Divi Child #864
* Bug: Code improvement in widget toc shortcode #870

= 2.0.72 27/01/2025 =
* New: Expand/Collapse Option for Device Target in TOC for Improved Usability #824
* New: Easy TOC Integration with Yoast SEO Schema #821
* New: Generate TOC without Links #796
* Bug: Conflict issue with customization settings #840
* Bug: Headings issue with the Kadence Child theme in the product category. #820
* Bug: 'Header Label‘ Option Not Updating in CATEGORY, TAG individually #852
* Bug: The Read Time option's label field is not visible #853
* Fix: Customisation of widget title tag  #855
* Bug: On multisite, When plugin activated, It goes to not found page #802
* Fix: Need an option for Translating the 'Toggle Table of Content' aria-label' #860
* Compatibility: We need to make compatibility with YOOtheme. #856

= 2.0.71 09/12/2024 =
* New: Support for more attributes in shortcode #828
* New: Automatically short the title in ETOC #830
* New: Floating layout for Sticky ETOC #844
* Bug: cookie "ezTOC_hidetoc-0" does not have an appropriate "SameSite" attribute value #822
* Bug: TOC Short code causing 500 error on Uncode theme #831
* Bug: The Sticky Toc title does not shift to the right in the option Heading Text Direction selected as Right to Left is selected #833
* Bug: 'Header Label' Option Not Updating in post individually #837
* Fix: Issue with "TOC open on load" option in Sticky TOC feature. #815
* Fix: TOC conflict with Avada Builder plugin and Custom Child Theme #775
* Fix: Undefined Array Key 'navigation_scroll_bar' in class-eztoc-widgetsticky.php #842
* Fix: Error in Google search console when enabling Create by Mediavine #841
* Fix: Compatibility with Customize Post Categories for WPBakery Page Builder plugin & Publisher Theme #843
* Compatibility: Tested with Wordpress 6.7 #836

= 2.0.70 17/10/2024 =
* New: One click migration for Table of Contents plus plugin #826
* New: Add missing parameters in sticky widget shortcode #812
* Bug: Timber theme TOC links not working on all pages and When ACF compatibility is on homepage gives 500 error #814
* Bug: Incorrect Symbols Displaying When Adding %PAGE_NAME% as Header Label #816
* Fix: Preserve-Linebreaks is broken. #806

= 2.0.69.1 05/09/2024 =
* Fix: Fatal error #803

= 2.0.69 28/08/2024 =
* Fix: Php warning. #788
* Bug: Sticky ETOC CSS automatically added in homepage #777
* Fix: Latest Version 2.0.68.1 causing functionality of "Inline CSS" Option in ETOC. #787
* Fix: SiteNavigation markup issue with Easy TOC #764
* Bug: The Sticky toggle appears on the homepage If the device target is set as desktop and its apparency is disabled on the Homepage #789
* Bug: Generate related keyphrase and Keyphrase synonyms are not appearing if the plugin is active #791
* Tweak: Clean the autoloaded data "ez-toc-post-content-core-level from the database. #793
* Fix: Auto TOC not getting insert in Product Category Description #797
* Fix: Copy Shortcode buttons are not working #798

= 2.0.68.1 21/07/2024 =
* Fix: Fatal error when inline css is enabled in  version 2.0.68

= 2.0.68 20/07/2024 =
* Fix: Stored XSS Security Vulnerability disclosed by Cleantalk (Dmitrii Ignatyev)
* Compatibility: Tested with WordPress version 6.6  #780

= 2.0.67.1 19/06/2024 =
* Bug: Plugin Does Not Save Changes in recent version 2.0.67 #771

= 2.0.67 18/06/2024 =
* New: Read Time in Minutes option for individual posts and set the translation for reading time. #763
* New: Exclude heading by id option #768
* New: Exclude TOC added using shortcode on specific pages/posts [post_not_in="1,2"] #758
* Compatibility: Added compatibility with the theme StoreHub #760
* Compatibility: Conflict issue with custom theme(user specific issue) #750
* Compatibility: Conflict issue with DIVI theme #749
* Bug: Highlight Heading colors are not getting customized #755
* Bug: Sticky TOC appears on homepage without enabling option when front page is set #754
* Bug: TOC is showing on homepage's post's excerpt Avada theme conflict #757
* Fix: Passing null to parameter #1 ($string) of type string is deprecated #769
* Fix: Patched Security Vulnerability disclosed by Dmitrii Ignatyev from cleantalk.org

= 2.0.66.1 15/05/2024 =
* Bug: ETOC version 2.0.66 giving **Fatal error**: Uncaught TypeError: method_exists() #752

= 2.0.66 15/05/2024 =
* Bug: Multiple warnings showing when ACF support is enabled in TOC #738
* Bug: Conflict issue with Avada child theme. #740
* Bug: The editing/updating of the post/page is slowing down if the plugin is activated with Block based themes #741
* Bug: Positions & Alignment is not being changed globally #743
* New: Option for showing promt if no headings available #654
* New: Option or shortcode like [initial_view="Show"] for specific pages/post. #732
* Fix: Patched Stored XSS Vulnerability disclosed by Dmitrii Ignatyev from cleantalk.org


Full changelog available at [changelog.txt](https://plugins.svn.wordpress.org/easy-table-of-contents/trunk/changelog.txt)

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

= Is there any shortcode to add the table of content to anywhere I want ? =

Yes you can add the TOC with this shortcode - [ez-toc] and with the help of this you can easily add the TOC in the content or anywhere in the WordPress and if you want to add the shortcode on the theme file then you can add it with the help of this code - <?php echo do_shortcode( â€˜[ez-toc]â€™ ); ?> and with this, you can add the TOC on any file according to your need.

== Contact | Help | Technical Support ==

[Contact Us](https://tocwp.com/contact/)

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

= 2.0-rc4 =
Requires WordPress >= 5.0 and PHP version >= 5.6.20 (>= 7.1 is recommended).

= 2.0.1 =
Requires WordPress >= 5.0 and PHP version >= 5.6.20 (>= 7.1 is recommended).

= 2.0.2 =
Requires WordPress >= 5.0 and PHP version >= 5.6.20 (>= 7.1 is recommended).

= 2.0.3 =
Requires WordPress >= 5.0 and PHP version >= 5.6.20 (>= 7.1 is recommended).

= 2.0.4 =
Requires WordPress >= 5.0 and PHP version >= 5.6.20 (>= 7.1 is recommended).

= 2.0.5 =
Requires WordPress >= 5.0 and PHP version >= 5.6.20 (>= 7.1 is recommended).

= 2.0.6 =
Requires WordPress >= 5.0 and PHP version >= 5.6.20 (>= 7.1 is recommended).

= 2.0.7 =
Requires WordPress >= 5.0 and PHP version >= 5.6.20 (>= 7.1 is recommended).

= 2.0.8 =
Requires WordPress >= 5.0 and PHP version >= 5.6.20 (>= 7.1 is recommended).

= 2.0.9 =
Requires WordPress >= 5.0 and PHP version >= 5.6.20 (>= 7.1 is recommended).

= 2.0.10 =
Requires WordPress >= 5.0 and PHP version >= 5.6.20 (>= 7.1 is recommended).

= 2.0.11 =
Requires WordPress >= 5.0 and PHP version >= 5.6.20 (>= 7.1 is recommended).

= 2.0.12 =
Requires WordPress >= 5.3 and PHP version >= 5.6.20 (>= 7.4 is recommended).

= 2.0.13 =
Requires WordPress >= 5.3 and PHP version >= 5.6.20 (>= 7.4 is recommended).

= 2.0.14 =
Requires WordPress >= 5.3 and PHP version >= 5.6.20 (>= 7.4 is recommended).

= 2.0.15 =
Requires WordPress >= 5.3 and PHP version >= 5.6.20 (>= 7.4 is recommended).

= 2.0.16 =
Requires WordPress >= 5.3 and PHP version >= 5.6.20 (>= 7.4 is recommended).

= 2.0.17 =
Requires WordPress >= 5.3 and PHP version >= 5.6.20 (>= 7.4 is recommended).