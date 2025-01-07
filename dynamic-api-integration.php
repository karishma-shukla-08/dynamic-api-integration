<?php 

/**
 * Plugin Name: Dynamic API Integration
 * Description: A plugin for dynamic API integration in the Gutenberg block editor.
 * Version: 1.0.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Enqueue block editor assets
function dai_enqueue_scripts() {
    // Register the block editor script
    wp_register_script(
        'dai-block-editor', // Handle for the script
        plugins_url( 'build/index.js', __FILE__ ), // Path to the JS file
        array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-editor' ), // Dependencies
        filemtime( plugin_dir_path( __FILE__ ) . 'build/index.js' ) // Version based on file modification time
    );

    // Localize the script to pass AJAX URL and nonce
    wp_localize_script( 'dai-block-editor', 'daiApi', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'dai_api_nonce' ), // Add nonce here
    ) );

    // Enqueue the block editor script
    wp_enqueue_script( 'dai-block-editor' );

    // Enqueue the block editor styles
    wp_enqueue_style(
        'dai-block-editor',
        plugins_url( 'build/editor.css', __FILE__ ),
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'build/editor.css' )
    );
}

// Hook into the block editor assets
add_action( 'enqueue_block_editor_assets', 'dai_enqueue_scripts' );



// Add menu item for settings
function dai_add_settings_page() {
    add_options_page(
        'Dynamic API Integration Settings',
        'API Integration',
        'manage_options',
        'dai-settings',
        'dai_render_settings_page'
    );
}
add_action( 'admin_menu', 'dai_add_settings_page' );


// Render settings page
function dai_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Dynamic API Integration Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'dai_settings_group' );
            do_settings_sections( 'dai-settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings

function dai_register_settings() {

    register_setting( 'dai_settings_group', 'dai_api_url' );
    register_setting( 'dai_settings_group', 'dai_request_type' );
    register_setting( 'dai_settings_group', 'dai_api_params' );

    add_settings_section(
        'dai_settings_section',
        'API Configuration',
        null,
        'dai-settings'
    );

    add_settings_field(
        'dai_api_url',
        'API URL',
        'dai_api_url_callback',
        'dai-settings',
        'dai_settings_section'
    );

    add_settings_field(
        'dai_request_type',
        'Request Type',
        'dai_request_type_callback',
        'dai-settings',
        'dai_settings_section'
    );

    add_settings_field(
        'dai_api_params',
        'API Parameters',
        'dai_api_params_callback',
        'dai-settings',
        'dai_settings_section'
    );
}
add_action( 'admin_init', 'dai_register_settings' );

// Callbacks for settings fields
function dai_api_url_callback() {
    $value = get_option( 'dai_api_url', '' );
    echo '<input type="url" name="dai_api_url" value="' . esc_attr( $value ) . '" class="regular-text">';
}

function dai_request_type_callback() {
    $value = get_option( 'dai_request_type', 'GET' ); // Default to GET
    ?>
    <select name="dai_request_type">
        <option value="GET" <?php selected( $value, 'GET' ); ?>>GET</option>
        <option value="POST" <?php selected( $value, 'POST' ); ?>>POST</option>
    </select>
    <?php
}

function dai_api_params_callback() {
    $value = get_option( 'dai_api_params', '' );
    echo 'Add Paramter in Json format<textarea name="dai_api_params" rows="5" class="large-text">' . esc_textarea( $value ) . '</textarea>';
}

// Register Block
function dai_register_block() {
    // Register the block editor script
    wp_register_script(
        'dynamic-api-integration-block',
        plugins_url( 'build/index.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-editor' ), // Block dependencies
        filemtime( plugin_dir_path( __FILE__ ) . 'build/index.js' )
    );

    // Register block editor style
    wp_register_style(
        'dynamic-api-integration-editor',
        plugins_url( 'build/editor.css', __FILE__ ),
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'build/editor.css' )
    );

    register_block_type( 'dynamic-api-integration/block', array(
        'editor_script' => 'dynamic-api-integration-block',
        'editor_style'  => 'dynamic-api-integration-editor',
    ) );
}
add_action( 'init', 'dai_register_block' );


// register the api
add_action('rest_api_init', function () {
    register_rest_route('dai/v1', '/fetch-data', array(
        'methods'  => 'POST',
        'callback' => 'dai_fetch_api_data_rest',
        'permission_callback' => '__return_true', // Adjust this to restrict access
    ));
});

// make the dynamic api call
function dai_fetch_api_data_rest(WP_REST_Request $request) {
    
    $api_url = get_option( 'dai_api_url' );
    $request_type = get_option( 'dai_request_type', 'GET' );
    $params = get_option( 'dai_api_params', '' );

    if ( ! $api_url ) {
        return new WP_Error( 'no_url', 'API URL is not configured.', array( 'status' => 400 ) );
    }

    // Parse parameters
    $query_args = ! empty( $params ) ? json_decode( $params, true ) : array();

    
    // Make API request
    $response = ( $request_type === 'POST' ) 
        ? wp_remote_post( $api_url, array( 'body' => $query_args ) )
        : wp_remote_get( add_query_arg( $query_args, $api_url ) );

       
    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'api_error', 'Failed to fetch API data.', array( 'status' => 500 ) );
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    // Return the data and status
    return rest_ensure_response( $data);
}
