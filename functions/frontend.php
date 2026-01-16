<?php

if (!function_exists('rabbit_hole_frontend')) {

    function rabbit_hole_add_content_filter() {
        if (is_author() || is_tax() || is_category() || is_tag()) {
            global $wp_query;
            $wp_query->post_count = 0;
            add_action('loop_no_results', 'rabbit_hole_show_content');
        } else {
            add_filter('the_content', 'rabbit_hole_apply_content', 999);
        }
    }

    function rabbit_hole_show_content() {
        $content = rabbit_hole_get_message_content();
        echo $content;
    }

    function rabbit_hole_apply_content($content) {
        //var_dump($content); die();
        if ($content) {
            rabbit_hole_remove_content_filter();
        }
        $content = rabbit_hole_get_message_content();
        return $content;
    }

    function rabbit_hole_get_message_content() {
        $settings = rabbit_hole_get_settings();
        $content = $settings['display_message_content'];
        $content = do_shortcode($content);
        $content = wpautop($content);
        return $content;
    }

    function rabbit_hole_remove_content_filter() {
        remove_filter('the_content', 'rabbit_hole_apply_content', 999);
    }

    if (!function_exists('rabbit_hole_get_settings')) {

        function rabbit_hole_get_settings($type = '', $obj_id = 0) {
            $settings = [];
            if (!$obj_id || !$type) {
                $obj_id = get_queried_object_id();
                $obj = get_queried_object();
                $obj_class = get_class($obj);
                $tmp = explode('_', $obj_class, 2);
                $tmp = end($tmp);
                $type = strtolower($tmp);
            }
            switch ($type) {
                case 'post':
                    $obj_type = get_post_type($obj_id);
                    break;
                case 'term':
                    $obj_type = $obj->taxonomy;
                    break;
                case 'user':
                    $obj_type = $obj->roles;
                    break;
            }
            //var_dump($obj_type);
            $rabbit_hole = get_option('rabbit_hole');
            //var_dump($rabbit_hole); die();
            switch ($type) {
                case 'post':
                    if (!empty($rabbit_hole[$obj_type])) {
                        $settings = $rabbit_hole[$obj_type];
                    }
                    break;
                case 'term':
                    if (!empty($rabbit_hole['tax'][$obj_type])) {
                        $settings = $rabbit_hole['tax'][$obj_type];
                    }
                    break;
                case 'user':
                    // TODO: manage multiple roles
                    if (is_array($obj_type)) {
                        foreach ($obj_type as $role) {
                            if (!empty($rabbit_hole['role'][$role])) {
                                $settings = $rabbit_hole['role'][$role];
                            }
                        }
                    }
                    break;
            }

            if (!empty($settings['allow_override'])) {
                $rabbit_hole_obj = get_metadata($type, $obj_id, 'rabbit_hole', true);
                if (!empty($rabbit_hole_obj)) {
                    if (!empty($rabbit_hole_obj['behavior']) && $rabbit_hole_obj['behavior'] != 'global') {
                        $rabbit_hole_obj['display_message'] = !empty($settings['display_message']) ? $settings['display_message'] : false;
                        $rabbit_hole_obj['display_message_content'] = !empty($settings['display_message_content']) ? $settings['display_message_content'] : '';
                        $rabbit_hole_obj['disable_bypassing'] = !empty($settings['disable_bypassing']) ? $settings['disable_bypassing'] : false;
                        $rabbit_hole_obj['disable_bypassing_roles'] = !empty($settings['disable_bypassing_roles']) ? $settings['disable_bypassing_roles'] : [];
                        $settings = $rabbit_hole_obj;
                    }
                }
            }

            if (!empty($settings['disable_bypassing_roles']) && is_string($settings['disable_bypassing_roles'])) {
                $settings['disable_bypassing_roles'] = [$settings['disable_bypassing_roles']];
            }
            return $settings;
        }

    }

    function rabbit_hole_frontend() {
        //var_dump(is_singular());
        // exit function if not on front-end
        if (is_admin()) {
            return;
        }

        if (is_singular() || is_attachment() || is_single() || is_page() || is_author() || is_tax() || is_category() || is_tag()) {

            $settings = rabbit_hole_get_settings();
            //echo '<pre>';var_dump($settings);echo '</pre>';

            if (!empty($settings['disable_bypassing']) && !empty($settings['disable_bypassing_roles'])) {
                $user = wp_get_current_user();
                $current_roles = ($user) ? (array) $user->roles : [];
                //var_dump($current_roles);
                $roles = array_intersect($current_roles, $settings['disable_bypassing_roles']);
                if (!empty($roles)) {
                    return;
                }
            }
            if (!empty($settings['behavior'])) {
                switch ($settings['behavior']) {

                    case '401':
                        header('HTTP/1.0 401 Unauthorized');
                        if (!empty($settings['display_message'])) {
                            rabbit_hole_add_content_filter();
                        } else {
                            die('You are not allowed to access this page.');
                        }
                        break;

                    case '403':
                        header('HTTP/1.0 403 Forbidden');
                        if (!empty($settings['display_message'])) {
                            rabbit_hole_add_content_filter();
                        } else {
                            die('You are not allowed to access this page.');
                        }
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
                        if ($qoq) {
                            require $qoq;
                            // 5. Stop execution
                            exit;
                        }

                        // Elementor Pro will show 404 template

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

    add_action('template_redirect', 'rabbit_hole_frontend');
}