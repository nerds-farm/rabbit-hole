<?php
if (!function_exists('rabbit_hole_register_settings')) {

    function rabbit_hole_register_settings() {
        add_option('rabbit_hole', '[]');
        register_setting('rabbit_hole_options_group', 'rabbit_hole');
        if (!empty($_GET['page']) && $_GET['page'] == 'rabbit_hole' 
                && !empty($_GET['action']) && $_GET['action'] == 'reset') {
            delete_option('rabbit_hole');
            wp_redirect(admin_url('options-general.php?page=rabbit_hole'));
        }
    }

}
add_action('admin_init', 'rabbit_hole_register_settings');

if (!function_exists('rabbit_hole_register_options_page')) {

    function rabbit_hole_register_options_page() {
        add_options_page('Rabbit Hole', 'Rabbit Hole', 'manage_options', 'rabbit_hole', 'rabbit_hole_options_page');
    }

}
add_action('admin_menu', 'rabbit_hole_register_options_page');

if (!function_exists('rabbit_hole_options_page')) {

    function rabbit_hole_print_settings($ptkey, $label, $settings, $type = 'post', $i = 0) {
        $allow_override = !empty($settings['allow_override']) ? $settings['allow_override'] : '';
        $disable_bypassing = !empty($settings['disable_bypassing']) ? $settings['disable_bypassing'] : '';
        $display_message = !empty($settings['display_message']) ? $settings['display_message'] : '';
        
        $akey = '';
        if ($type != '' && $type != 'post') {
            $akey = '[' . $type . ']' . $akey;
        }
        ?>
        <div class="rh-settings postbox" id="<?php echo esc_attr($ptkey); ?>" <?php echo ($i) ? ' style="display:none;"' : ''; ?>>
            <div class="postbox-header"><h3 class="hndle ui-sortable-handle"><?php echo $label ?></h3></div>
            <div class="inner">
                <label class="bulk-select-button" for="rabbit_hole__<?php echo esc_attr($ptkey); ?>__allow_override">
                    <input type="checkbox" id="rabbit_hole__<?php echo esc_attr($ptkey); ?>__allow_override" name="rabbit_hole<?php echo $akey; ?>[<?php esc_attr_e($ptkey); ?>][allow_override]"<?php echo $allow_override ? ' checked' : ''; ?>>
                    <?php _e('Allow these settings to be overridden for individual entities', 'rabbit-hole'); ?>
                    <br><small><?php _e('If checked, users with the Administer Rabbit Hole settings for Content permission will be able to override these settings for individual entities.', 'rabbit-hole'); ?> </small>
                </label>

                <label class="bulk-select-button" for="rabbit_hole__<?php echo esc_attr($ptkey); ?>__disable_bypassing">
                    <input type="checkbox" class="rh-disable-bypassing" id="rabbit_hole__<?php echo esc_attr($ptkey); ?>__disable_bypassing" name="rabbit_hole<?php echo $akey; ?>[<?php esc_attr_e($ptkey); ?>][disable_bypassing]"<?php echo $disable_bypassing ? ' checked' : ''; ?>>
                    <?php _e('Enable permissions-based bypassing', 'rabbit-hole'); ?>
                    <br><small><?php _e('If checked, users will be able to bypass configured Rabbit Hole behavior. It will be applied to Administrators and other users with bypass permissions.', 'rabbit-hole'); ?> </small>
                </label>
                <label class="accordion-section-content accordion-section-content--roles" id="rabbit_hole__<?php echo esc_attr($ptkey); ?>__disable_bypassing_roles_select" for="rabbit_hole__<?php echo esc_attr($ptkey); ?>__disable_bypassing_roles"<?php if (!$disable_bypassing) { ?> style="display: none;"<?php } ?>>
                    <b><?php _e('Roles with bypass permissions', 'rabbit-hole'); ?></b><br>
                    <select multiple id="rabbit_hole__<?php echo esc_attr($ptkey); ?>__disable_bypassing_roles" name="rabbit_hole<?php echo $akey; ?>[<?php esc_attr_e($ptkey); ?>][disable_bypassing_roles]" class="rh-disable-bypassing-roles" style="width: 100%;">
                        <?php
                        $bypass_roles = empty($settings['disable_bypassing_roles']) ? [] : $settings['disable_bypassing_roles'];
                        if (is_string($bypass_roles)) {
                            $bypass_roles = [$bypass_roles];
                        }
                        $roles = wp_roles();
                        foreach ($roles->roles as $rkey => $role) {
                            $selected = ((empty($bypass_roles) && $rkey == 'administrator') || (!empty($bypass_roles) && in_array($rkey, $bypass_roles))) ? ' selected' : '';
                            ?>
                            <option<?php echo $selected; ?> value="<?php esc_attr_e($rkey); ?>"><?php esc_html_e($role['name']); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </label>  

                <label class="bulk-select-button" for="rabbit_hole__<?php echo esc_attr($ptkey); ?>__display_message">
                    <input type="checkbox" class="rh-disable-message" id="rabbit_hole__<?php echo esc_attr($ptkey); ?>__display_message" name="rabbit_hole<?php echo $akey; ?>[<?php esc_attr_e($ptkey); ?>][display_message]"<?php echo $display_message ? ' checked' : ''; ?>>
                    <?php _e('Display a message when viewing the page', 'rabbit-hole'); ?>
                    <br><small><?php _e('If checked, users who NOT bypassed the Rabbit Hole action, will see a warning message when viewing the page. ', 'rabbit-hole'); ?> </small>
                </label>
                <label class="accordion-section-content accordion-section-content--message" id="rabbit_hole__<?php echo esc_attr($ptkey); ?>__display_message_content_txt" for="rabbit_hole__<?php echo esc_attr($ptkey); ?>__display_message_content"<?php if (!$disable_bypassing) { ?> style="display: none;"<?php } ?>>
                    <b><?php _e('Display Content', 'rabbit-hole'); ?></b><br>
                    <textarea placeholder="<?php _e('You are not allowed to access this page.', 'rabbit-hole'); ?>" rows="4" id="rabbit_hole__<?php echo esc_attr($ptkey); ?>__display_message_content" name="rabbit_hole<?php echo $akey; ?>[<?php esc_attr_e($ptkey); ?>][display_message_content]" class="rh-display-message-content" style="width: 100%;"><?php
                echo empty($settings['display_message_content']) ? '' : $settings['display_message_content'];
                ?></textarea>
                </label> 

                <br>
                <?php rabbit_hole_config($ptkey, $settings, false, $type); ?>
            </div>
        </div>
        <?php
    }

    function rabbit_hole_options_page() {

        $icon = '<span class="dashicons dashicons-carrot"></span> ';
        $roles = wp_roles();

        $args = [
            //'_builtin' => true,
            'public' => true,
            'publicly_queryable' => true,
        ];
        $post_types = get_post_types($args, 'objects');
        $pages = get_post_type_object('page');
        $post_types = ['page' => $pages] + $post_types;
        $rabbit_hole = get_option('rabbit_hole');
        //echo '<pre>';var_dump($rabbit_hole);echo '</pre>';
        //content on page goes here
        ?>
        <div id="rabbit_hole">
            <a class="float-end rh-version" href="https://wordpress.org/plugins/rabbit-hole/" target="_blank">v1.1 <span class="dashicons dashicons-info-outline"></span></a>
            <h1><img class="rh-logo" src="<?php echo plugin_dir_url(__FILE__); ?>../assets/img/icon.svg"width="60" height="60"> <?php esc_html_e('Rabbit Hole', 'rabbit-hole'); ?> </h1>
            <hr class="wp-header-end">
            <form method="POST" action="options.php">
                <?php
                settings_fields('rabbit_hole_options_group');
                do_settings_sections('rabbit_hole_options_group');
                ?>
                <div class="bg-white rh-wrapper">
                <h2> <?php esc_html_e('Post Types', 'rabbit-hole'); ?></h2>
                <nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
                    <?php
                    $i = 0;
                    foreach ($post_types as $ptkey => $post_type) {
                        ?>
                        <a href="#<?php echo esc_attr($ptkey); ?>" class="nav-tab<?php echo (!$i) ? ' nav-tab-active' : ''; ?>" aria-current="page">
                            <?php
                            if (!empty($rabbit_hole[$ptkey]['allow_override']) || (!empty($rabbit_hole[$ptkey]['behavior']) && $rabbit_hole[$ptkey]['behavior'] != '200')) {
                                echo $icon;
                            }
                            ?>
                            <abbr title="<?php esc_attr_e($ptkey); ?>"><?php esc_html_e($post_type->label); ?></abbr>
                        </a>
                        <?php
                        $i++;
                    }
                    ?>
                </nav>
                <?php
                $i = 0;
                foreach ($post_types as $ptkey => $post_type) {
                    $settings = !empty($rabbit_hole[$ptkey]) ? $rabbit_hole[$ptkey] : [];
                    $label = esc_html__($post_type->label);
                    rabbit_hole_print_settings($ptkey, $label, $settings, 'post', $i);
                    $i++;
                }
                ?>
                </div>

                <hr>
                <div class="bg-white rh-wrapper">
                <h2> <?php esc_html_e('Taxonomies', 'rabbit-hole'); ?></h2>
                <nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
                    <?php
                    $taxonomies = get_taxonomies();
                    foreach ($taxonomies as $ptkey => $taxonomy) {
                        $taxonomy = get_taxonomy($taxonomy);
                        //var_dump($taxonomy);
                        if ($taxonomy->publicly_queryable) {
                            ?>
                            <a href="#<?php esc_attr_e($ptkey); ?>" class="nav-tab" aria-current="page">
                                <?php
                                if (!empty($rabbit_hole['tax'][$ptkey]['allow_override']) || (!empty($rabbit_hole['tax'][$ptkey]['behavior']) && $rabbit_hole['tax'][$ptkey]['behavior'] != '200')) {
                                    echo $icon;
                                }
                                ?>
                                <abbr title="<?php esc_attr_e($ptkey); ?>"><?php esc_html_e($taxonomy->label); ?></abbr></a>
                            <?php
                        }
                    }
                    ?>
                </nav>
                <?php
                foreach ($taxonomies as $ptkey => $taxonomy) {
                    $ptkey = $ptkey;
                    $settings = !empty($rabbit_hole['tax'][$ptkey]) ? $rabbit_hole['tax'][$ptkey] : [];
                    $taxonomy = get_taxonomy($taxonomy);
                    $label = esc_html__($taxonomy->label);
                    rabbit_hole_print_settings($ptkey, $label, $settings, 'tax', $i);
                }
                ?>
                </div>
                
                <hr>
                <div class="bg-white rh-wrapper">
                <h2><?php esc_html_e('User Roles', 'rabbit-hole'); ?></h2>
                <nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
                    <?php
                    //var_dump($roles);
                    foreach ($roles->roles as $ptkey => $role) {
                        ?>
                        <a href="#<?php esc_attr_e($ptkey); ?>" class="nav-tab" aria-current="page">
                            <?php
                                if (!empty($rabbit_hole['role'][$ptkey]['allow_override']) || (!empty($rabbit_hole['role'][$ptkey]['behavior']) && $rabbit_hole['role'][$ptkey]['behavior'] != '200')) {
                                    echo $icon;
                                }
                                ?>
                            <abbr title="<?php esc_attr_e($ptkey); ?>"><?php esc_html_e($role['name']); ?></abbr></a>
                        <?php
                    }
                    ?>
                </nav>
                <?php
                foreach ($roles->roles as $ptkey => $role) {
                    $settings = !empty($rabbit_hole['role'][$ptkey]) ? $rabbit_hole['role'][$ptkey] : [];
                    $label = esc_html__($role['name']);
                    rabbit_hole_print_settings($ptkey, $label, $settings, 'role', $i);
                }
                ?>
                </div>
                <br><br>
                <?php if (!empty($settings)) { ?>
                <a href="?page=rabbit_hole&action=reset" class="button button-primary button-danger button-reset"><span class="dashicons dashicons-warning"style="vertical-align: text-top;"></span> <?php esc_html_e('Reset Settings', 'rabbit-hole'); ?></a>
                <?php
                }
                submit_button();
                ?>
            </form>
        </div>
        <br class="clear">
        <?php
        $footer_text = sprintf(
			/* translators: 1: Elementor, 2: Link to plugin review */
				__( 'Enjoyed %1$s? Please leave us a %2$s rating. We really appreciate your support!', 'rabbit-hole' ),
				'<strong>' . esc_html__( 'Rabbit Hole', 'rabbit-hole' ) . '</strong>',
				'<a href="https://wordpress.org/support/plugin/rabbit-hole/reviews/#new-post" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
        echo $footer_text;
        rabbit_hole_assets();
    }

}