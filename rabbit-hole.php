<?php
/**
 * Rabbit Hole
 *
 * Plugin Name: Rabbit Hole
 * Plugin URI:  https://wordpress.org/plugins/rabbit-hole/
 * Description: Rabbit Hole is a module that adds the ability to control what should happen when an entity is being viewed at its own page.
 * Version:     1.0.1
 * Author:      Nerds Farm
 * Author URI:  https://github.com/WordPress/classic-editor/
 * License:     GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: rabbit-hole
 * Domain Path: /languages
 * Requires at least: 4.9
 * Tested up to: 6.1
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

function get_rabbit_hole_options() {
    return $rabbit_hole_options = [
        '200' => __('Display the page', 'rabbit-hole'),
        '403' => __('Access Deny', 'rabbit-hole'),
        '404' => __('Page not Found', 'rabbit-hole'),
        '30x' => __('Redirect to page', 'rabbit-hole'),
    ];
}

function get_rabbit_hole_responses() {
    return $responses = [
        '301' => __('301 (Moved Permanently)', 'rabbit-hole'),
        '302' => __('302 (Found)', 'rabbit-hole'),
        '303' => __('303 (See other)', 'rabbit-hole'),
        '304' => __('304 (Not modified)', 'rabbit-hole'),
        '305' => __('305 (Use proxy)', 'rabbit-hole'),
        '307' => __('307 (Temporary redirect)', 'rabbit-hole'),
    ];
}

function rabbit_hole_register_settings() {
    add_option('rabbit_hole', '[]');
    register_setting('rabbit_hole_options_group', 'rabbit_hole');
}

add_action('admin_init', 'rabbit_hole_register_settings');

function rabbit_hole_register_options_page() {
    add_options_page('Rabbit Hole', 'Rabbit Hole', 'manage_options', 'rabbit_hole', 'rabbit_hole_options_page');
}

add_action('admin_menu', 'rabbit_hole_register_options_page');

function _rabbit_hole_config($ptkey, $settings = [], $singular = false) {
    $default = $singular ? 'global' : '200';
    $behavior = !empty($settings['behavior']) ? $settings['behavior'] : $default;
    $url = !empty($settings['url']) ? $settings['url'] : '';
    $redirect_response = !empty($settings['redirect_response']) ? $settings['redirect_response'] : '301';
    ?>
    <h3><?php _e('Behavior', 'rabbit-hole'); ?></h3>
    <p><?php _e('What should happen when someone tries to visit an entity page for this content type?', 'rabbit-hole'); ?></p>
    <?php
    $akey = '[' . $ptkey . ']';
    $options = get_rabbit_hole_options();
    if ($singular) {
        $options['global'] = __('Global fallback', 'rabbit-hole');
        $akey = '';
    }
    foreach ($options as $opt => $option) {
        ?>
        <div>  
            <label for="rabbit_hole__<?php echo $ptkey; ?>__behavior__<?php echo $opt; ?>"><input type="radio" class="rabbit_hole_behavior" id="rabbit_hole__<?php echo $ptkey; ?>__behavior__<?php echo $opt; ?>" name="rabbit_hole<?php echo $akey; ?>[behavior]" value="<?php echo $opt; ?>"<?php echo ($behavior == $opt) ? ' checked' : ''; ?>><?php echo $option; ?></label>
        <?php if ($opt == '30x') { ?>
                <div class="accordion-section-content">
                    <h4><?php _e('Redirect settings', 'rabbit-hole'); ?></h4>
                    <h5><?php _e('Redirect path', 'rabbit-hole'); ?></h5>
                    <p><?php _e('Enter the shortcode, relative path or the full URL that the user should get redirected to. Query strings and fragments are supported.', 'rabbit-hole'); ?></p>
                    <input class="rabbit-hole-redirect-setting form-text" data-drupal-selector="edit-rh-redirect" aria-describedby="edit-rh-redirect--description" type="text" id="edit-rh-redirect" name="rabbit_hole<?php echo $akey; ?>[url]" value="<?php echo $url; ?>" aria-required="true" placeholder="https://www.example.com/?query=value#fragment" style="width: 100%;">
                    <!--<p>You may enter Twig in this field, such as {{post.field_link}} or /my/view?page={{post.ID}}.</p>-->
                    <h5><?php _e('Response code', 'rabbit-hole'); ?></h5>
                    <p><?php _e('The response code that should be sent to the users browser. Follow this link for more information on response codes.', 'rabbit-hole'); ?></p>
                    <select class="rabbit-hole-redirect-response-setting form-select" id="rh-redirect-response" name="rabbit_hole<?php echo $akey; ?>[redirect_response]">
                        <?php foreach (get_rabbit_hole_responses() as $rkey => $response) { ?>
                            <option value="<?php echo $rkey; ?>"<?php echo ($redirect_response == $rkey) ? ' selected="selected"' : ''; ?>><?php echo $response; ?></option>
            <?php } ?>
                    </select>
                </div>
        <?php } ?>
        </div>
        <?php
    }
}

function _rabbit_hole_assets() {
    wp_enqueue_script('rabbit-hole-admin', RABBIT_HOLE_URL . 'assets/js/rh-admin.js', ['jquery']);
    wp_enqueue_style('rabbit-hole-admin', RABBIT_HOLE_URL . 'assets/css/rh-admin.css');
}

function rabbit_hole_options_page() {
    $args = [
        //'_builtin' => true,
        'public' => true,
        'publicly_queryable' => true,
    ];
    $post_types = get_post_types($args, 'objects');
    $pages = get_post_type_object('page');
    $post_types['page'] = $pages;
    $rabbit_hole = get_option('rabbit_hole');
    //echo '<pre>';var_dump($rabbit_hole);echo '</pre>';
    //content on page goes here
    ?>
    <div id="rabbit_hole">
        <h1> <?php esc_html_e('Rabbit Hole', 'rabbit-hole'); ?> </h1>
        <hr class="wp-header-end">
        <form method="POST" action="options.php">
            <?php
            settings_fields('rabbit_hole_options_group');
            do_settings_sections('rabbit_hole_options_group');
            ?>
            <nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
                <?php
                $i = 0;
                foreach ($post_types as $ptkey => $post_type) {
                    ?>
                    <a href="#<?php echo $ptkey; ?>" class="nav-tab<?php echo (!$i) ? ' nav-tab-active' : ''; ?>" aria-current="page"><?php echo $post_type->label; ?></a>
                    <?php
                    $i++;
                }
                ?>
            </nav>
            <?php
            $i = 0;
            foreach ($post_types as $ptkey => $post_type) {
                $settings = !empty($rabbit_hole[$ptkey]) ? $rabbit_hole[$ptkey] : [];
                $allow_override = !empty($settings['allow_override']) ? $settings['allow_override'] : '';
                ?>
                <div class="rh-settings postbox" id="<?php echo $ptkey; ?>" <?php echo ($i) ? ' style="display:none;"' : ''; ?>>
                    <div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php echo $post_type->label; ?></h2></div>
                    <div class="inner">
                        <label class="bulk-select-button" for="rabbit_hole__<?php echo $ptkey; ?>__allow_override"><input type="checkbox" id="rabbit_hole__<?php echo $ptkey; ?>__allow_override" name="rabbit_hole[<?php echo $ptkey; ?>][allow_override]"<?php echo $allow_override ? ' checked' : ''; ?>> Allow these settings to be overridden for individual entities</label>
                        <br>
        <?php _rabbit_hole_config($ptkey, $settings); ?>
                    </div>
                </div>

                <?php
                $i++;
            }

            submit_button();
            ?>
        </form>
    </div>
    <?php
    _rabbit_hole_assets();
}

function rabbit_hole_meta_box_callback($post) {
    // Add a nonce field so we can check for it later.
    wp_nonce_field('rabbit_hole_nonce', 'rabbit_hole_nonce');
    $settings = get_post_meta($post->ID, 'rabbit_hole', true);
    _rabbit_hole_config($post->post_type, $settings, true);
    _rabbit_hole_assets();
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id
 */
function save_rabbit_hole_meta_box_data($post_id) {

    // Check if our nonce is set.
    if (!isset($_POST['rabbit_hole_nonce'])) {
        return;
    }

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['rabbit_hole_nonce'], 'rabbit_hole_nonce')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Make sure that it is set.
    if (!isset($_POST['rabbit_hole'])) {
        return;
    }

    // Update the meta field in the database.
    update_post_meta($post_id, 'rabbit_hole', $_POST['rabbit_hole']);
}

add_action('save_post', 'save_rabbit_hole_meta_box_data');

function rabbit_hole_meta_box() {
    $screens = [];
    $settings = get_option('rabbit_hole');
    if (!empty($settings) && is_array($settings)) {
        foreach ($settings as $post_type => $setting) {
            if (!empty($setting['allow_override'])) {
                $screens[] = $post_type;
            }
        }
    }
    foreach ($screens as $screen) {
        add_meta_box(
                'rabbit_hole',
                __('Rabbit Hole', 'rabbit-hole'),
                'rabbit_hole_meta_box_callback',
                $screen
        );
    }
}

add_action('add_meta_boxes', 'rabbit_hole_meta_box');

add_action('template_redirect', 'rabbit_hole_frontend');

function rabbit_hole_frontend() {
    //var_dump(is_singular());
    // exit function if not on front-end
    if (is_admin()) {
        return;
    }
    //$post = get_queried_object();
    //var_dump($post);
    if (is_singular() || is_attachment() || is_single() || is_page()) {

        $post_id = get_queried_object_id();
        $post_type = get_post_type($post_id);

        $rabbit_hole = get_option('rabbit_hole');
        //var_dump($rabbit_hole); die();
        if (!empty($rabbit_hole[$post_type])) {
            $settings = $rabbit_hole[$post_type];
            if (!empty($settings['allow_override'])) {
                $rabbit_hole_post = get_post_meta($post_id, 'rabbit_hole', true);
                if (!empty($rabbit_hole_post)) {
                    if (!empty($rabbit_hole_post['behavior']) && $rabbit_hole_post['behavior'] != 'global') {
                        $settings = $rabbit_hole_post;
                    }
                }
            }
            if (!empty($settings['behavior'])) {
                switch ($settings['behavior']) {
                    case '403':
                        header('HTTP/1.0 403 Forbidden');
                        die('You are not allowed to access this file.');
                        break;
                    case '404':
                        //add_action('wp', function(){
                        // 1. Ensure `is_*` functions work
                        global $wp_query;
                        $wp_query->set_404();

                        // 2. Fix HTML title
                        add_action('wp_title', function () {
                            return '404: Not Found';
                        }, 9999);

                        // 3. Throw 404
                        status_header(404);
                        nocache_headers();

                        // 4. Show 404 template
                        $qoq = get_404_template();
                        if ($qoq)
                            require $qoq;

                        // 5. Stop execution
                        exit;
                        //});
                        break;
                    case '30x':
                        $status = $settings['redirect_response'] ? intval($settings['redirect_response']) : 301;
                        $location = do_shortcode($settings['url']);
                        if (!empty($location)) {
                            wp_redirect($location, $status);
                            exit;
                        }
                        break;
                    case '200':
                    default:
                    // display
                }
            }
        }
    }
}
