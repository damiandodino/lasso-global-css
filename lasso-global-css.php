<?php
/**
 * Plugin Name: Lasso Global CSS
 * Description: Enables Global CSS pulling from the Green Rive Lake Commons site.
 * GitHub Plugin URI: https://github.com/damiandodino/lasso-global-css
 */

// Enqueue the global CSS stylesheet
function lasso_global_css_enqueue_styles() {
    // Retrieve the custom URL set in the settings page
    $user_css_url = get_option('lasso_global_css_url');
    if (empty($user_css_url)) {
        $user_css_url = 'https://greenriverlakecommons.kinsta.cloud/'; // Default URL
    }
    
    $css_path = 'wp-content/themes/hello-theme-child-master/style.css?ver=?ver=';
    $timestamp = time();
    
    $css_url = $user_css_url . $css_path . $timestamp;
    
    // Enqueue the stylesheet with a unique handle and set it to load in the head
    wp_enqueue_style('lasso-global-css', $css_url, array(), null, 'all');
}
add_action('wp_enqueue_scripts', 'lasso_global_css_enqueue_styles', 9999);

function lasso_global_css_add_inline_script() {
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var h1Element = document.querySelector('h1');
            var bodyElement = document.querySelector('body');

            if (h1Element) {
                var className = h1Element.textContent.trim().replace(/\s+/g, '-').toLowerCase();
                bodyElement.classList.add(className);
            }
        });
    </script>
    <?php
}
add_action('wp_head', 'lasso_global_css_add_inline_script');


// Add the plugin settings page to the admin menu
function lasso_global_css_add_settings_page() {
    add_menu_page(
        'Lasso Global CSS Settings', // Page title
        'Lasso Global CSS',          // Menu title
        'manage_options',            // Capability
        'lasso-global-css',          // Menu slug
        'lasso_global_css_settings_page', // Callback function
        'dashicons-admin-generic',   // Icon URL
        null                         // Position
    );
}
add_action( 'admin_menu', 'lasso_global_css_add_settings_page' );



// Plugin settings page
function lasso_global_css_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( 'Lasso Global CSS Settings' ); ?></h1>
        <form method="post" action="options.php">
            <?php
                settings_fields( 'lasso_global_css_settings_group' );
                do_settings_sections( 'lasso-global-css' ); // Page slug
                submit_button();
            ?>
        </form>
    </div>
    <?php
}


function lasso_global_css_register_settings() {
    // Register a new setting
    register_setting( 'lasso_global_css_settings_group', 'lasso_global_css_url' );

    // Register a new section
    add_settings_section(
        'lasso_global_css_section',
        __('CSS Settings', 'lasso-global-css'),
        'lasso_global_css_section_callback',
        'lasso-global-css' // Page slug
    );

    // Register a new field
    add_settings_field(
        'lasso_global_css_url_field',
        __('Green River Lake Commons URL', 'lasso-global-css'),
        'lasso_global_css_url_field_callback',
        'lasso-global-css', // Page slug
        'lasso_global_css_section'
    );
}
add_action( 'admin_init', 'lasso_global_css_register_settings' );


function lasso_global_css_section_callback( $args ) {
    echo '<p>' . esc_html__('Enter the URL of the Green River Lake Commons Site once it is live. Include \'/\' At the end! Example:https://greenriver-mhc.com/', 'lasso-global-css') . '</p>';
}

function lasso_global_css_url_field_callback( $args ) {
    // Get the value of the setting we've registered with register_setting()
    $url = get_option( 'lasso_global_css_url' );
    // Output the field
    echo '<input type="text" name="lasso_global_css_url" value="' . esc_attr( $url ) . '" style="width: 500px;" />';
}


function lasso_global_css_settings_link( $links ) {
    $settings_link = '<a href="' . admin_url( 'admin.php?page=lasso-global-css' ) . '">' . __( 'Settings', 'lasso-global-css' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'lasso_global_css_settings_link' );


function lasso_global_css_set_default_options() {
    add_option( 'lasso_global_css_url', 'https://greenriverlakecommons.kinsta.cloud/' );
}

register_activation_hook( __FILE__, 'lasso_global_css_set_default_options' );

