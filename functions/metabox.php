<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/* POST ********* */

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

        $rabbit_hole = wp_unslash($_POST['rabbit_hole']);
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
                    esc_html__('Rabbit Hole', 'rabbit-hole'),
                    'rabbit_hole_meta_box_callback',
                    $screen
            );
        }
    }

}
add_action('add_meta_boxes', 'rabbit_hole_meta_box');

/* USER ********* */

if (!function_exists('rabbit_hole_save_user_meta_box_data')) {

    /**
     * Save additional profile fields.
     *
     * @param  int $user_id Current user ID.
     */
    function rabbit_hole_save_user_meta_box_data($user_id) {
        
        // Check if our nonce is set.
        if (!isset($_POST['rabbit_hole_nonce'])) {
            return;
        }
        
        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST['rabbit_hole_nonce'], 'rabbit_hole_nonce')) {
            return;
        }

        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        // Make sure that it is set.
        if (isset($_POST['rabbit_hole'])) {

            $rabbit_hole = wp_unslash($_POST['rabbit_hole']);
            $rabbit_hole = array_map('sanitize_text_field', $rabbit_hole);

            // Update the meta field in the database.
            update_user_meta($user_id, 'rabbit_hole', $rabbit_hole);
        }
    }

}
add_action('personal_options_update', 'rabbit_hole_save_user_meta_box_data');
add_action('edit_user_profile_update', 'rabbit_hole_save_user_meta_box_data');

if (!function_exists('rabbit_hole_user_meta_box')) {

    /**
     * Add new fields above 'Update' button.
     *
     * @param WP_User $user User object.
     */
    function rabbit_hole_user_meta_box($user) {
        // Add a nonce field so we can check for it later.
        wp_nonce_field('rabbit_hole_nonce', 'rabbit_hole_nonce');
        $settings = get_user_meta($user->ID, 'rabbit_hole', true);
        foreach ($user->roles as $role) {
            rabbit_hole_config($role, $settings, true);
        }
        rabbit_hole_assets();
    }

}
add_action('show_user_profile', 'rabbit_hole_user_meta_box');
add_action('edit_user_profile', 'rabbit_hole_user_meta_box');

/* TERM ********* */

function rabbit_hole_term_type_update($term_id, $tt_id, $taxonomy) {

    // Check if our nonce is set.
    if (!isset($_POST['rabbit_hole_nonce'])) {
        return;
    }
    
    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['rabbit_hole_nonce'], 'rabbit_hole_nonce')) {
        return;
    }

    if (!current_user_can('edit_posts', $term_id)) {
        return false;
    }

    if (isset($_POST['rabbit_hole'])) {
        $rabbit_hole = wp_unslash($_POST['rabbit_hole']);
        $rabbit_hole = array_map('sanitize_text_field', $rabbit_hole);
        update_term_meta($term_id, 'rabbit_hole', $rabbit_hole);
    }
}

add_action('created_term', 'rabbit_hole_term_type_update', 10, 3);
add_action('edit_term', 'rabbit_hole_term_type_update', 10, 3);

if (!function_exists('rabbit_hole_term_meta_box')) {

    function rabbit_hole_term_meta_box($term = null) {
        // Add a nonce field so we can check for it later.
        wp_nonce_field('rabbit_hole_nonce', 'rabbit_hole_nonce');
        $settings = [];
        if (isset($_GET['taxonomy'])) {
            $taxonomy = sanitize_key($_GET['taxonomy']);
        }
        if (is_string($term)) {
            $taxonomy = $term;
        }
        if (is_object($term) && get_class($term) == 'WP_Term') {
            $taxonomy = $term->taxonomy;
            $settings = get_term_meta($term->term_id, 'rabbit_hole', true);
        }
        rabbit_hole_config($taxonomy, $settings, true);
        rabbit_hole_assets();
    }

}

$rabbit_hole_settings = get_option('rabbit_hole');
if (!empty($rabbit_hole_settings['tax']) && is_array($rabbit_hole_settings['tax'])) {
    foreach ($rabbit_hole_settings['tax'] as $tax => $rabbit_hole_setting) {
        if (!empty($rabbit_hole_setting['allow_override'])) {
            add_action($tax . '_edit_form', 'rabbit_hole_term_meta_box');
            add_action($tax . '_add_form_fields', 'rabbit_hole_term_meta_box');
        }
    }
}
//$taxonomies = get_taxonomies();
/*foreach ($taxonomies as $tax => $taxonomy) {
    //add_action($tax.'_edit_form_fields', 'rabbit_hole_term_meta_box'); 
    add_action($tax . '_edit_form', 'rabbit_hole_term_meta_box');
    add_action($tax . '_add_form_fields', 'rabbit_hole_term_meta_box');
    //add_action($tax.'_add_form', 'rabbit_hole_term_meta_box');
}*/