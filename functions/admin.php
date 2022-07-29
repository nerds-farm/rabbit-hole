<?php
if (!function_exists('rabbit_hole_register_settings')) {

    function rabbit_hole_register_settings() {
        add_option('rabbit_hole', '[]');
        register_setting('rabbit_hole_options_group', 'rabbit_hole');
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
                        <a href="#<?php echo esc_attr($ptkey); ?>" class="nav-tab<?php echo (!$i) ? ' nav-tab-active' : ''; ?>" aria-current="page"><?php esc_html_e($post_type->label); ?></a>
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
                    <div class="rh-settings postbox" id="<?php echo esc_attr($ptkey); ?>" <?php echo ($i) ? ' style="display:none;"' : ''; ?>>
                        <div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php esc_html_e($post_type->label); ?></h2></div>
                        <div class="inner">
                            <label class="bulk-select-button" for="rabbit_hole__<?php echo esc_attr($ptkey); ?>__allow_override">
                                <input type="checkbox" id="rabbit_hole__<?php echo esc_attr($ptkey); ?>__allow_override" name="rabbit_hole[<?php echo esc_attr($ptkey); ?>][allow_override]"<?php echo $allow_override ? ' checked' : ''; ?>>
                                <?php _e('Allow these settings to be overridden for individual entities', 'rabbit-hole'); ?>
                            </label>
                            <br>
                            <?php rabbit_hole_config($ptkey, $settings); ?>
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
        rabbit_hole_assets();
    }

}