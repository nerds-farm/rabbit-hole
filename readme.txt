=== Rabbit Hole ===
Contributors: nerds-farm
Tags: gutenberg, disable, disable gutenberg, editor, classic editor, block editor
Requires at least: 4.9
Tested up to: 6.1
Stable tag: 1.0.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Control what should happen when an entity is being viewed at its own page

== Description ==

Rabbit Hole is a module that adds the ability to control what should happen when an entity is being viewed at its own page.

Perhaps you have a content type that never should be displayed on its own page, like an image content type that's displayed in a carousel. Rabbit Hole can prevent this node from being accessible on its own page, through node/xxx.

*Options*

This works by providing multiple options to control what should happen when the entity is being viewed at its own page. You have the ability to

    Deliver an access denied page.
    Deliver a page not found page.
    Issue a page redirect to any path or external url.
    Or simply display the entity (regular behavior).

This is configurable per bundle and per entity. There is also a permission that lets certain roles override Rabbit Hole completely.

It's possible to evaluate PHP for the redirect path. This makes it possible to execute different redirects based on whatever logics you need. Perhaps you want a user to be able to view nodes that he has created, but no one else's. The PHP evaluation is limited to users with the appropriate permission, so there's no unnecessary security breach.

== Changelog ==

= 1.0.1 =
Initial release.

== Frequently Asked Questions ==

= Default settings =

When activated this plugin will restore the previous ("classic") WordPress editor and hide the new block editor ("Gutenberg").
These settings can be changed at the Settings => Writing screen.

= Default settings for network installation =

There are two options, global configuration or settings to be overridden for individual entities

= Cannot find the "Switch to classic editor" link =

It is in the main block editor menu, see this [screenshot](https://ps.w.org/classic-editor/assets/screenshot-7.png?rev=2023480).

== Screenshots ==
1. Admin settings on the Settings -> Writing screen.
2. User settings on the Profile screen. Visible when the users are allowed to switch editors.
3. "Action links" to choose alternative editor. Visible when the users are allowed to switch editors.
4. Link to switch to the block editor while editing a post in the classic editor. Visible when the users are allowed to switch editors.
5. Link to switch to the classic editor while editing a post in the block editor. Visible when the users are allowed to switch editors.
6. Network settings to select the default editor for the network and allow site admins to change it.
7. The "Switch to classic editor" link.
