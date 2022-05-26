=== BuddyForms Hook Fields ===
Contributors: svenl77, konradS, themekraft, buddyforms, gfirem, marin250189
Tags: buddypress, user, members, profiles, custom post types, taxonomy, frontend posting, frontend editing,hook fields, custom fields, post meta
Requires at least: 3.9
Tested up to: 6.0
Stable tag: 1.3.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Have full control of your fields output. Hook your BuddyForms Form Fields via options and display them everywhere on your site.

== Description ==

This is the BuddyForms Hook Fields Extension. You need the BuddyForms plugin installed for the plugin to work. <a href="http://buddyforms.com" target="_blank">Get BuddyForms now!</a>

With this plugin you will get new options added to your Form Builder "Fields" to select where you want to display the field. This makes it very easy to manage the output and can save you a lot of time modifying your templates, by just adding a hook.

<h4>Display different Field Types</h4>
The plugin will keep care about the different Field Types like ( Links, Categories, text fields etc. ) and display them exactly as they should. You can decide if you only want the value get displayed or with the field name.

<h4>Create a list - Reorder Items via Drag and Drop</h4>
If you want to display multiple fields as list in one place, You can reorder the list easily via drag and drop in the FormBuilder. Just move the field to the correct position in the form builder and they will get displayed exact in this order.

<h4>For the single view you have 4 default options.</h4>

1. before the title
2. after the title
3. before the content
4. after the content.

<h4>Side wide, you can hook everywhere</h4>

Just enter the Hook name in the text fields.

== Documentation & Support ==

<h4>Extensive Documentation and Support</h4>

All code is neat, clean and well documented (inline as well as in the documentation).

The BuddyForms Documentation with many how-to’s is following now!

If you still get stuck somewhere, our support gets you back on the right track.
You can find all help buttons in your BuddyForms Settings Panel in your WP Dashboard!

== Installation ==

You can download and install BuddyForms Hook Fields using the built in WordPress plugin installer. If you download BuddyForms manually,
make sure it is uploaded to "/wp-content/plugins/".

Activate BuddyForms Hook Fields in the "Plugins" admin panel using the "Activate" link. If you're using WordPress Multisite, you can optionally activate BuddyForms Hook Fields Network Wide.

== Frequently Asked Questions ==

You need the BuddyForms plugin installed for the plugin to work.
<a href="http://buddyforms.com" target="_blank">Get BuddyForms now!</a>

== Screenshots ==

1. **BuddyForms Hook Fields - FormBuilder "Field Options"** -  Hook your BuddyForms Form Fields via options.


== Changelog ==
= 1.3.9 - 26 May 2022 =
* Fixed vulnerability issue.
* Tested up to WordPress 6.0

= 1.3.8 - 17 May 2022 =
* Updated readme.txt

= 1.3.7 - 5 May 2022 =
* Fixed conflict between fields of different forms when displaying its value through shortcode.
* Added gutenberg block support.

= 1.3.6 - 23 Mar 2022 =
* Added new option to show Edit link in frontend.
* Added new shortcode to display single field in frontend.
* Tested up to WordPress 5.9

= 1.3.5 - 31 May 2021 =
* Hotfix: Fixed CSS issues for CPT other than posts. Eg: Product (WooCommerce).
* Hotfix: Fixed issue related with duplicate hooked media files on the Post Single View.

= 1.3.4 - 22 May 2021 =
* Fixed to do not show default thumbnail if no media was uploaded. 
* Improved thumbnail/preview support for PDF, Video, Audio (MP3) and Compressed files on the Upload and File fields.
* Tested up to WordPress 5.7

= 1.3.3 - 8 Mar 2021 =
* Fixed issue related with hooked fields on the single view list.
* Added improvements on the integration of Upload and File fields.
* Tested up 5.6.2

= 1.3.2 - 2 Nov 2020 =
* Fixed: Show upload field value.

= 1.3.1 - 23 April 2020 =
* Removed the limitation of the hook tab to appear in all form elements.
* Added a custom post type to handle the templates.
* Added a security to make all template private.
* Getting the form element output form buddyforms to show the table values.

= 1.3.0 - 27 March 2020 =
* Added option to use a page as template to customize the single post view with form shortcodes.

= 1.2.6 30 Sept 2019 =
* Fixed the output for the elements Category, Upload and File.

= 1.2.5 30 Sept 2019 =
* Fixed the option to output the content after/before the Title.

= 1.2.4 19 Sept 2019 =
* Added support for the File element.

= 1.2.3 -  Mar. 02 2019 =
* Freemius SDK Update

= 1.2.2 =
* Remove create function to use closures.

= 1.2.1 =
* Added a new option to display all form elements as table on the single under the post content

= 1.2 =
* Added Freemius Integration
* Fixed and issue with the dependencies check. The function tgmpa does not accepted an empty array.
* Added user_website as supported field

= 1.1.9.2 =
* Fixed and issue with the dependencies check. The function tgmpa does not accepted an empty array.

= 1.1.9.1 =
* Fixed an issue with the dependencies management. If pro was activated it still ask for the free version. Fixed now with a new default BUDDYFORMS_PRO_VERSION in the core to check if the pro is active.

= 1.1.9 =
* Add dependencies management with tgm

= 1.1.8 =
* fixed some smaller issues
* fixed some notice of undefined index

= 1.1.7 =
* Make it work with the latest version of buddyforms. the buddyforms array has changed so I adjust the code too the new structure
* Limit the hook options to supported form elements

= 1.1.6 =
* Make it work with the latest version of BuddyForms. the BuddyForms array has changed so I adjust the code too the new structure
* limit the hook options to supported form elements

= 1.1.5 =
* change the url to BuddyForms.com
* move the options to the new section addons

= 1.1.4 =
* Fixed a issue with the checkbox form element. Props to Thomas for the detailed issue Report.

= 1.1.3 =
* Reduce the query for a better performance
* Clean up the code

= 1.1.2 =
* Fixed some bugs reported by users.

= 1.1 =
* Spelling Corrections

= 1.0 =
* final 1.0 version
