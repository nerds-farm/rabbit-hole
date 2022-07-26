<?php

if (!function_exists('rabbit_hole_frontend')) {

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
    }

}
add_action('template_redirect', 'rabbit_hole_frontend');
