=== Plugin Name ===
Contributors: 
Donate link: https://joshcorne.co.uk/
Tags: media
Requires at least: 3.0.1
Tested up to: 6.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin can be used to deliver media. Add a video and images, secure with booking details and notify customer by email.

== Description ==

This plugin allows the user to create posts which contain a video and an image gallery. On saving, the user can select whether
to notify an email address that the post has been saved with a link to it. If the recipient follows the link, they are able
to login to the view the post using the set booking reference and their surname. Once they have verified themselves, the 
media can be streamed on the page and images view in a lightbox gallery. They can also download the video and a zip of all the
images to their machine. In order to save on space, a cron job can be enabled to delete media after a set number of days.

For users which generate a significant amount of media, their hosting may not support so much space so it is recommended to use
an additional plugin to store WordPress media in blob storage such as S3 or Azure Blob Storage.

1. Send emails to customers when their media has been upload
2. Requires booking reference and surname to unlock post
3. Allows download of media

https://github.com/davidstaab/wp-mac
https://wppb.me/
https://lokeshdhakar.com/projects/lightbox2/
https://davidwalsh.name/create-zip-php

== Roadmap ==

1. Add Gutenberg editor support.
2. Add Full Site Editor templates.
3. Add multi-site support
4. Add cron for autodeletion of media to avoid high storage costs
5. Add X-SendFile to mac to speed up media loading
6. Add gulp minification to build process

== Installation ==

1. Upload `media-delivery.zip` to the `/wp-content/plugins/` directory.
2. Unzip `media-delivery.zip`.
3. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= What is this for? =

The use for this plugin was for experience providers such as skydiving centers who often deliver personalised media
to customers after their experience. This allows the customer to view it on the skydive center's website.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).

== Changelog ==

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0 =
This is the first release!
