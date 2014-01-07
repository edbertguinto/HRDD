=== WP jQuery Text and Image Slider ===
Contributors: Hit Reach
Donate link: 
Tags: Image, Gallery, slider, allow, php, javascript, pure, html, Hit Reach, jquery, Hit, Reach, text, WP, WordPress
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 1.2

An extensible jQuery text and image slider for WordPress posts, pages and widgets

== Description ==

WP jQuery Text and Image slider is an extensible sliding gallery for WordPress, It can use slides composed of images, or html to create a gallery on your site.

Galleries can be added using shortcodes within posts, pages and widgets (where available).

The plugin can have custom animations and slide designs added via function calls from other plugins, so you are not stuck with the same designs, you can easily add your own to your site

From time to time we may have our own animations listed on the [plugin site](http://hitr.ch/wptis "WP jQuery Text and Image Slider") for download, be sure to check back often.

== Usage ==

The primary functions of the plugin are handled in the admin dashboard, inside the dashboard there are 3 sub menus which are used to access all the features of this plugin.

= Adding a Gallery =

Adding a new gallery is easy, Go to the gallery overview page and select "Add New Gallery".

Add the details of your gallery, and select the animation type, then click submit. Simple!

= Adding a New Slide =

It is simple to add a new slide, Go to the slide addition page and fill in the form making sure to select the Gallery and Slide Type then click submit

= Gallery options explained =

The following options are available when adding a new gallery:

* **Gallery Name**: This is used for reference, However animation types may incorporate it into their design
* **Gallery Description** (optional): A text area to allow you to keep track of what is inside the gallery. animation types may also incorporate this into their design
* **Animation Type**: The type of animation you wish to use for this gallery, initially there are 3 on the system: fade, fade with bullets and a custom fade, but you can add your own later if required
* **Gallery Width**:the width of the slides in the gallery in pixels, some animation types use it to keep all slides uniform in width
* **Slide Height**:the height of the slides in the gallery in pixels, some animation types use it to keep all slides uniform in height

= Slide options explained =

The following options are available when adding a new slide:

* **Slide Name**: This is used for reference, however with the WPTIS image type, it also becomes the alt attribute
* **Gallery**: This is the gallery to add the slide to, you need to have added a gallery to add a slide
* **Slide Type**: This is the type of slide, it is used to decode the content intered into a usable slide format, there are 2 default ones, HTML and Image, but you can add your own
* **Slide Link** (optional): This is the link to apply to the slide if you want it to go somewhere!
* **Slide Show Time**: Some animations allow you to set the length of time (in milliseconds) that the slide is shown on screen before transition begins
* **Slide Content**: This is the most inportant field, it is used to add your content to the side, the HTML slide type accepts raw HTML where as the Image slide type only accepts the url to the image.

= Add Your Own Animation To The Plugin =

You can register your own animation type to the plugin by calling the WPTIS::public_register_animation function in the plugin with the following 4 parameters:

* **NAME**: the name of the animation, its used in the dropdown and also to unregister the animation if required, try and keep it unique
* **PATH TO PHP FILE**: the path to the PHP file that will output the gallery onto the page, it needs to be on the same site as the blog installation.
* **PATH TO CSS FILE** (optional): the path to the CSS file that has the slide designs, it needs to be on the same site as the blog installation.
* **PATH TO JS FILE** (optional): the path to the Javascript file that has the slide animation scripts, it needs to be on the same site as the blog installation.
The CSS and JS files are optional in case the CSS and JS are included in the PHP file

In the PHP for your animation, the animation must be inside a function called "output" for it to work.  Output also accepts 3 arguments, $ID is the gallery id, $SLIDES is the array of slides, $gallery is the information about the gallery.

To remove your animation from the system, call the function WPTIS::public_unregister_animation with the name of the animation as a parameter. This can be used to remove any animation you no longer need on the system. The core animation types are automatically re-added on plugin activation

= Add Your Own Slide Type To The Plugin =

You can register your own slide types to the system as well, so where we have already added Image and HTML, you may wish add others, or your own formatting of the image/html types. To add your own type, simply call WPTIS::public_register_slide_type with the following parameters:

* **NAME**: the name of the slide type, its used in the dropdown and also to unregister the slide type if required, try and keep it unique
* **PROCESSING FILE**: This is the url to the file that processes the slide content into the final slide, the processing function must be called the same as the NAME but with any spaces changed to _'s

The function inside the processing file must be able to accept 2 arguments, $slide is an array of information about the slide and $gallery is an array of information about the gallery.

To remove your slide type from the system, call the function WPTIS::public_unregister_slide_type with the name of the slide type as a parameter. This can be used to remove any slide types you no longer need on the system. The core slide type types are automatically re-added on plugin activation

= Adding your gallery to a post or page =

To add your gallery to a post or page, simply use the shortcode: [WPTIS_gallery id=XX] where XX is the id of your gallery obtained from the plugin gallery view page.

== Installation ==

1. Extract the zip file and drop the contents in the wp-content/plugins/ directory of your WordPress installation
1. Activate the Plugin from Plugins page

== Changelog ==
 = 1.0 =
 * Initial Release
 = 1.1 = 
 * Fixed major bug with processors
 * Upgraded Gallery CSS
 = 1.2 =
 * Fix for headers already sent message on the alter.php
 * Update to the HTML slide processor
 
== Upgrade Notice==
 = 1.1 =
 * Major Bug Fix

== Frequently Asked Questions ==

= My Question Is Not Answered Here! =
If your question is not listed here please look on the [plugin site](http://hitr.ch/wptis "WP jQuery Text and Image Slider"), if your question is not listed there, leave a comment!

== Screenshots ==
1. Screen for adding a new gallery to the system
2. Example output from the plugin at the front end