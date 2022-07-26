<?php

if (!function_exists('rabbit_hole_meta_box_callback')) {

    function rabbit_hole_meta_box_callback($post) {
        // Add a nonce field so we can check for it later.
        wp_nonce_field('rabbit_hole_nonce', 'rabbit_hole_nonce');
        $settings = get_post_meta($post->ID, 'rabbit_hole', true);
        rabbit_hole_config($post->post_type, $settings, true);
        rabbit_hole_assets();
    }

}

if (!function_exists('rabbit_hole_save_meta_box_data')) {

    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id
     */
    function rabbit_hole_save_meta_box_data($post_id) {

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

        $rabbit_hole = $_POST['rabbit_hole'];
        $rabbit_hole = array_map('sanitize_text_field', $rabbit_hole);

        // Update the meta field in the database.
        update_post_meta($post_id, 'rabbit_hole', $rabbit_hole);
    }

}
add_action('save_post', 'rabbit_hole_save_meta_box_data');

if (!function_exists('rabbit_hole_meta_box')) {

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

}
add_action('add_meta_boxes', 'rabbit_hole_meta_box');

