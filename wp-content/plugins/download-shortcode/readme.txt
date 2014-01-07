=== Download Shortcode ===
Contributors: DrewAPicture
Donate link: http://www.werdswords.com
Tags: downloads, shortcode, force download
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 1.1
License: GPLv2

Allows you to wrap file links in a shortcode that will force a download when clicked.

== Description ==

Have you ever linked a PDF, an mp3, image, or other kind of file because you wanted to let people download it but instead it just loads in the browser? This plugin introduces a `[download]` shortcode which allows you to output links that will tell the browser to download those files!

= Usage =

**WITH 'pretty permalinks' enabled in Settings > Permalinks:**

The following example would display as:

* A link titled `My Link`
* With a rewritten URL of `http://yoursite.com/download/my_song.mp3`
`
[download label="My Link"]http://yoursite.com/wp-content/uploads/my_song.mp3[/download]
`


The following example would display as:

* A rewritten link titled `http://yoursite.com/download/my_song.mp3`
`
[download]http://yoursite.com/wp-content/uploads/my_song.mp3[/download]
`

**WITHOUT 'pretty permalinks' enabled in Settings > Permalinks:**

The following example would display as:

* A link titled `My Other Link`
* With an exposed URL of `http://yoursite.com/wp-content/force-download.php?file=uploads/my_other_song.mp3`
`
[download label="My Other Link"]http://yoursite.com/wp-content/uploads/my_other_song.mp3[/download]
`

The following example would display as:

* An exposed link titled `http://yoursite.com/wp-content/force-download.php?file=uploads/my_other_song.mp3`
`
[download]http://yoursite.com/wp-content/uploads/my_other_song.mp3[/download]
`

= Other Uses =
If you wanted to use this in a php template, you could call something like:
`
<?php echo do_shortcode( '[download label="My Label"]http://example.com/wp-content/uploads/my_song.mp3[/download]' ); ?>
`

= Important notes: =
1. If your server permissions allow it, the plugin will automatically attempt to copy force-download.php to your `/wp-content/` directory.
2. If your server permissions DO NOT allow it, you will need to manually upload the file located in `/download-shortcode/wp-content/force-download.php` to your `/wp-content/` directory.
3. By default, forced-download links only support files located in your uploads directory. More about this in [Other Notes](http://wordpress.org/extend/plugins/download-shortcode/other_notes).


== Installation ==

1. Upload the entire `download-shortcode` folder to the `/wp-content/plugins/` directory
2. The plugin will automatically attempt to copy force-download.php into your `/wp-content/` folder, though you may need to upload it manually if your server lacks the necessary permissions.
3. Activate the plugin through the 'Plugins' menu in the Dashboard.

== Frequently Asked Questions ==

= Which file formats does this work with? =

Currently, this plugin can force downloads for pdf, mp4, mp3, gif, png, jpg and jpeg files

= When I click links from the front-end, all I get is a 404 Page Not Found error or a white screen. What gives? =

There are two things you should check:
* There may be a problem with the force-download.php script located in your `/wp-content/` directory. Try copying the force-download.php file from download-shortcode/inc/force-download.php to your wp-content directory.
* It is also possible you just need to flush your rewrite rules by visiting the Settings > Permalinks screen in your Dashboard.

= How can I modify this plugin's default behavior? =

Check out the [Other Notes](http://wordpress.org/extend/plugins/download-shortcode/other_notes) section for example functions and filters.

= I've uninstalled Download Shortcode but now I have a bunch of broken shortcodes in my posts and pages. How can I hide them? =

You can add the following function to your theme's `functions.php` file to hide all instances of the `[download]` shortcode in your content.
`
function hide_download_shortcodes( $attr, $content ) {
	return;
}
add_shortcode( 'download', 'hide_download_shortcodes' );
`

== Other Notes ==

By default, download shortcodes work in a few specific ways:

* If you have 'pretty permalinks' enabled via Settings > Permalinks, your links will automatically be rewritten to http://yoursite.com/download/path/__yourfile
* If you don't include a label with your shortcode, the link URL will be displayed instead

If you wish to modify how some aspects of how download shortcodes are displayed or rewritten, there are several filters you can use to accomplish this. These should be added to your theme's `functions.php` file.

= Filters =

**Use a specific CSS class to style your links**

This example filter adds a class of 'downloads' to your links:
`
function add_download_links_class() {
	return 'downloads';
}
add_filter( 'fds_download_link_class', 'add_download_links_class' );
`

**Disable URL rewriting even if 'pretty permalinks' are enabled**
`
add_filter( 'fds_rewrite_urls', '__return_false' );
`

**Change the supported directory from `/uploads/` to something else**

This example filter changes the supported directory to `/themes/`.
`
function change_download_files_directory() {
	return 'themes';
}
add_filter( 'fds_download_files_directory', 'change_download_files_directory' );
`
Please note, the directory **must** be located in your `/wp-content/` directory.


**Change the rewrite path from `/downloads/` to something else**

This example filter changes the rewrite path to http://yoursite.com/members/yourfile.jpg
`
function change_download_rewrite_path() {
	return 'members';
}
add_filter( 'fds_download_rewrite_path', 'change_download_rewrite_path' );
`

== Changelog ==

= 1.1 =

* Add support for WordPress in a subdirectory
* Regenerate pot file to include translatable strings
* TODO: Add Multisite upload directory support

= 1.0 =

* Complete plugin rewrite
* Introduce URL rewriting if 'pretty permalinks' are enabled
* Add logic to make overwriting/updating/removing force-download.php more automated
* Add `fds_rewrite_urls` filter to allow disabling URL rewriting
* Add `fds_download_rewrite_path` filter to allow changing the rewrite endpoint
* Add `fds_upload_rewrite_path` filter to allow changing the supported directory
* Make the plugin translatable

= 0.2.3 =

* Update readme.txt with note about replacing force-download.php in wp-content with the new one

= 0.2.2 =

* Remove faulty strlen check on filenames in force-download.php
* Fixes for WSOD issues.

= 0.2 =

* Fix security vulnerability which exposed php core files to direct download
* Adds phpDoc blocks
* Add `fds_download_link_class` filter to change download link class
* Other minor tweaks

= 0.1 =

* Initial release


== Upgrade Notice ==

= 1.1 =

* Add support for WordPress in a subdirectory
* Regenerate pot file to include translatable strings

= 1.0 =

* Adds URL rewriting
* Adds more robust security
* Add additional filters for advanced customizations

= 0.2.3 =

* Make sure you replace force-download.php in your wp-content folder with the new version!

= 0.2 =

* Security Fixes

= 0.1 =

* Initial submission


== Screenshots ==

1. No screenshots.