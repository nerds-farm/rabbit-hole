<?php
if (!function_exists('rabbit_hole_get_options')) {

    function rabbit_hole_get_options() {
        return $rabbit_hole_options = [
            '200' => __('Display the page', 'rabbit-hole'),
            '403' => __('Access Deny', 'rabbit-hole'),
            '404' => __('Page not Found', 'rabbit-hole'),
            '30x' => __('Redirect to page', 'rabbit-hole'),
        ];
    }

}
if (!function_exists('rabbit_hole_get_responses')) {

    function rabbit_hole_get_responses() {
        return $responses = [
            '301' => __('301 (Moved Permanently)', 'rabbit-hole'),
            '302' => __('302 (Found)', 'rabbit-hole'),
            '303' => __('303 (See other)', 'rabbit-hole'),
            '304' => __('304 (Not modified)', 'rabbit-hole'),
            '305' => __('305 (Use proxy)', 'rabbit-hole'),
            '307' => __('307 (Temporary redirect)', 'rabbit-hole'),
        ];
    }

}
if (!function_exists('rabbit_hole_config')) {

    function rabbit_hole_config($ptkey, $settings = [], $singular = false) {
        $default = $singular ? 'global' : '200';
        $behavior = !empty($settings['behavior']) ? $settings['behavior'] : $default;
        $url = !empty($settings['url']) ? $settings['url'] : '';
        $redirect_response = !empty($settings['redirect_response']) ? $settings['redirect_response'] : '301';
        ?>
        <h3><?php _e('Behavior', 'rabbit-hole'); ?></h3>
        <p><?php echo $singular ? __('What should happen when someone tries to visit this page?', 'rabbit-hole') : __('What should happen when someone tries to visit an entity page for this content type?', 'rabbit-hole'); ?></p>
        <?php
        $akey = '[' . esc_attr($ptkey) . ']';
        $options = rabbit_hole_get_options();
        if ($singular) {
            $options['global'] = __('Global fallback', 'rabbit-hole');
            $akey = '';
        }
        foreach ($options as $opt => $option) {
            ?>
            <div>  
                <label for="rabbit_hole__<?php echo esc_attr($ptkey); ?>__behavior__<?php echo esc_attr($opt); ?>"><input type="radio" class="rabbit_hole_behavior" id="rabbit_hole__<?php echo esc_attr($ptkey); ?>__behavior__<?php echo esc_attr($opt); ?>" name="rabbit_hole<?php echo esc_attr($akey); ?>[behavior]" value="<?php echo esc_attr($opt); ?>"<?php echo ($behavior == $opt) ? ' checked' : ''; ?>><?php esc_html_e($option); ?></label>
                <?php if ($opt == '30x') { ?>
                    <div class="accordion-section-content">
                        <h4><?php _e('Redirect settings', 'rabbit-hole'); ?></h4>
                        <h5><?php _e('Redirect path', 'rabbit-hole'); ?></h5>
                        <p><?php _e('Enter the shortcode, relative path or the full URL that the user should get redirected to. Query strings and fragments are supported.', 'rabbit-hole'); ?></p>
                        <input class="rabbit-hole-redirect-setting form-text" data-drupal-selector="edit-rh-redirect" aria-describedby="edit-rh-redirect--description" type="text" id="edit-rh-redirect" name="rabbit_hole<?php echo esc_attr($akey); ?>[url]" value="<?php echo esc_attr($url); ?>" aria-required="true" placeholder="https://www.example.com/?query=value#fragment" style="width: 100%;">
                        <!--<p>You may enter Twig in this field, such as {{post.field_link}} or /my/view?page={{post.ID}}.</p>-->
                        <h5><?php _e('Response code', 'rabbit-hole'); ?></h5>
                        <p><?php _e('The response code that should be sent to the users browser. Follow this link for more information on response codes.', 'rabbit-hole'); ?></p>
                        <select class="rabbit-hole-redirect-response-setting form-select" id="rh-redirect-response" name="rabbit_hole<?php echo esc_attr($akey); ?>[redirect_response]">
                    <?php foreach (rabbit_hole_get_responses() as $rkey => $response) { ?>
                                <option value="<?php echo esc_attr($rkey); ?>"<?php echo ($redirect_response == $rkey) ? ' selected="selected"' : ''; ?>><?php esc_html_e($response); ?></option>
                <?php } ?>
                        </select>
                    </div>
            <?php } ?>
            </div>
            <?php
        }
    }

}
if (!function_exists('rabbit_hole_assets')) {

    function rabbit_hole_assets() {
        wp_enqueue_script('rabbit-hole-admin', RABBIT_HOLE_URL . 'assets/js/rh-admin.js', ['jquery']);
        wp_enqueue_style('rabbit-hole-admin', RABBIT_HOLE_URL . 'assets/css/rh-admin.css');
    }

}
