=== Email Marketing Subscribe Form for JetMails.com ===
Contributors: jetmails
Tags: jetmails, email, mailer, newsletter, marketing, email marketing, plugin, mails, list management, mailing
Author URI: http://www.jetmails.com/
Requires at least: 2.5
Tested up to: 3.0.1
Stable tag: 1.3.1

The JetMails plugin allows you to easily setup a subscribe form for your list.

== Description ==

[JetMails](http://www.jetmails.com/) is a web-based email marketing solution that enables small, medium and large organizations to easily and affordably send professional HTML and/or text email newsletters, promotions, etc.
Our head office is in Madrid (Spain), and we have clients from over 25 different countries.
To learn more, just check out our site: [www.JetMails.com](http://www.jetmails.com/) or [contact us](http://www.jetmails.com/contact/).

This plugin allows you to set up a subscription form in you blog without editing a single line of code.

The form submission is done completely in your blog, so your visitors don't have to leave the page when they subscribe. Once the submission has been processed, the subscriber will see a "Thank you" message, and JetMails will take care of the validation (double opt-in).

Once the plugin is installed and activated you can very easily configure to send your feeds and newsletters to your subscribers. 

The plugin is free to download, but in order to use it you must open an account with JetMails.com. We invite you to check our prices, you'll be surprised!

= Plugin Main features = 

* Easy to set up and use
* Sidebar widget of add a short code anywhere you want
* Error and thank you messages in your blog 
* Feeds sent once a day or manually anytime


== Installation ==

This section describes how to install the plugin and get started using it.

1. Unzip our archive and upload the entire `jetmails-subscribe-form` directory to your `/wp-content/plugins/` directory
1. Activate the plugin through the "Plugins" menu in WordPress
1. Go to Settings and look for "JetMails Form Setup" in the menu
1. Enter your JetMails Username & Password and let the plugin verify them
1. Select One of your lists to have your visitors subscribe to.
1. Finally, go to Manage->Widgets and enable the widget
1. And you are DONE!

= Advanced =

If you have a custom coded sidebar or something else special going on where you can't simply enable the widget
through the Wordpress GUI, all you need to do is:

If you are using Wordpress v2.5 or higher, you can use the short-code:

` [jetmails_subscribe_form] `

If you are adding it inside a php code block, pop this in:

` jetmails_subscribe_form_display_widget(); `

Or, if you are dropping it in between a bunch of HTML, use this:

`<?php jetmails_subscribe_form_display_widget(); ?>`

Where ever you want it to show up. 

Note: in some environments you will need to install the Exec_PHP plugin to use that method of display. It can be found here: http://wordpress.org/extend/plugins/exec-php/


== Internationalization (i18n) ==

Currently we have the plugin configured so it can be easily translated and the following languages supported:

* en_US - English in the U.S.
* es_ES - Spanish in Spain

If your language is not listed above, feel free to create a translation. Here are the basic steps:

1. Copy "jetmails_i18n-en_US.po" to "jetmails_i18n-LANG_COUNTRY.po" - fill in LANG and COUNTRY with whatever you use for WPLANG in wp-config.php
2. Grab a transalation editor. [POedit](http://www.poedit.net/) works for us
3. Translate each line - if you need some context, just open up jetmails-subscribe-form.php and search for the line number or text
4. Send it to us - info@jetmails.com - and we'll test it and include it with our next release


== Frequently Asked Questions ==

= What is JetMails? =

[JetMails](http://www.jetmails.com/) is a web-based email marketing solution that enables small, medium and large organizations to easily and affordably send professional HTML and/or text email newsletters, promotions, etc.
Our head office is in Madrid (Spain), and we have clients from over 25 different countries.
To learn more, just check out our site: [www.JetMails.com](http://www.jetmails.com/) or [contact us](http://www.jetmails.com/contact/).

= Do I need to pay to use this plugin? =

The plugin is free to download, but in order to use it you must open an account with JetMails.com. We invite you to check our prices, you'll be surprised!

= Can I translate the plugin into my own language? =

Currently we have the plugin configured so it can be easily translated and the following languages supported:

* en_US - English in the U.S.
* es_ES - Spanish in Spain

If your language is not listed above, feel free to create a translation. Here are the basic steps:

1. Copy "jetmails_i18n-en_US.po" to "jetmails_i18n-LANG_COUNTRY.po" - fill in LANG and COUNTRY with whatever you use for WPLANG in wp-config.php
2. Grab a transalation editor. [POedit](http://www.poedit.net/) works for us
3. Translate each line - if you need some context, just open up jetmails-subscribe-form.php and search for the line number or text
4. Send it to us - info@jetmails.com - and we'll test it and include it with our next release

== Support ==

Feel free to [contact us](http://www.jetmails.com/contact/).

== Screenshots ==

== Changelog == 

= 1.3.1 =

* Due to problems in 1.3.0, It was removed the subscribers count.

= 1.3.0 = 

* The widget displays the subscribers count.

= 1.1.2 =

* Fix contact information

= 1.1.1 =

* Improved error messages
* Check for domain errors or typos while subscribing
* Improved documentation

= 1.1.0 =

* Allow to include First name and Last Name in the form.
* Allow to hide labels.
* Improve handling of race conditions.
* Improve user case: Only one list in Jetmails. 

= 1.0.5 = 

* Improve documentation.

= 1.0.4 = 

* Lots of improvements and new features

 
      

       
