=== WP Snapshot ===
Contributors: dbmartin
Tags: meta description, summary, preview, read more, excerpt
Requires at least: 2.5.1
Tested up to: 3.8
Stable tag: 1.0
License: GPLv2

Easily display a summary of post text or custom text. 

== Description ==

Generates a template tag that allows you to easily display a snapshot of text.  
Useful for meta descriptions, post snippets, or your own custom block of text.  
Displays a snippet of text without having to use the &lt;--!more--&gt; quicktag.

== Installation ==

1. Upload the `mish_wp_snapshot` folder to the your plugins directory, typically `/wp-content/plugins/`.
1. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= How do I use it? =
It accepts three user-entered parameters or uses its own defaults.

* **Content**: Enter your own custom text or leave blank.
* **Word Limit**: Enter the number of words you want to display.
* **Trailing Text**: Enter the trailing text to indicate the content continues.

Please see the plugin homepage for examples of usage.

== Changelog ==
= 1.0 =
* added `strip_shortcode()` to remove shortcode content from the excerpt created
* added `wp_kses()` to strip all html