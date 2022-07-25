=== Rabbit Hole ===
Contributors: nerds-farm
Tags: redirect, 404, seo, cpt, post type, deny, allow
Requires at least: 4.9
Tested up to: 6.1
Stable tag: 1.0.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Control what should happen when an entity is being viewed at its own page

== Description ==

Rabbit Hole is a plugin that adds the ability to control what should happen when an entity is being viewed at its own page.

Perhaps you have a content type that never should be displayed on its own page, like an image content type that's displayed in a carousel. 
Rabbit Hole can prevent this object from being accessible on its own page, through p=xxx (or permalink).

**Options**

This works by providing multiple options to control what should happen when the entity is being viewed at its own page. 
You have the ability to

- Deliver an access denied page.
- Deliver a page not found page.
- Issue a page redirect to any path or external url.
- Or simply display the entity (regular behavior).

This is configurable per post type and per single post type. 

It's possible to generate dynamic values with Twig or Shortcode for the redirect path. 
This makes it possible to execute different redirects based on whatever logics you need. 
Perhaps you want a user to be able to view nodes that he has created, but no one else's. 

**Coming soon** 
- *Conditions*
There will be the possibility to configure a User Role that override Rabbit Hole completely, useful for Memberships.
- *Archives*
Support for Terms and Authors archive pages

**Drupal porting**
This is porting from a beloved [Drupal module](https://www.drupal.org/project/rabbit_hole) which I used in all my projects.
Thanks to the authors for the idea.

== Frequently Asked Questions ==

= Default settings =

You can find global configuration in Settings => Rabbit Hole.
By default all CPT are predefined as Display page, following the classic WP behavior.

= How can I change behavior for a single post =

Once you enable the Allow override for single post type you will find the dedicated configuration metabox in post Edit page.

== Screenshots ==
1. Admin Global settings on the Settings -> Rabbit Hole.
2. Settings on Single Post Edit metabox for override global configuration.

== Changelog ==

= 1.0.1 - 2022-07-25 =
* Initial release.