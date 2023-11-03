<?php
/**
 * Rabbit Hole
 *
 * Plugin Name: Rabbit Hole
 * Plugin URI:  https://wordpress.org/plugins/rabbit-hole/
 * Description: Rabbit Hole is a module that adds the ability to control what should happen when an entity is being viewed at its own page.
 * Version:     1.1
 * Author:      frafish
 * Author URI:  https://nerds.farm
 * License:     GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: rabbit-hole
 * Domain Path: /languages
 * Requires at least: 4.9
 * Tested up to: 6.4
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 3, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
if (!defined('ABSPATH')) {
    die('Invalid request.');
}

define('RABBIT_HOLE__FILE__', __FILE__);
define('RABBIT_HOLE_PATH', plugin_dir_path(RABBIT_HOLE__FILE__));
define('RABBIT_HOLE_URL', plugins_url('/', RABBIT_HOLE__FILE__));

$fnc = RABBIT_HOLE_PATH.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR;
if (is_admin()) {
    include_once($fnc.'settings.php');
    include_once($fnc.'admin.php');
    include_once($fnc.'metabox.php');
} else {
    include_once($fnc.'frontend.php');
}

