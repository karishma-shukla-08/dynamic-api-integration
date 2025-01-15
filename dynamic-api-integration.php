<?php

/**
 * Plugin Name: Dynamic API Integration
 * Description: A plugin for dynamic API integration in the Gutenberg block editor.
 * Version: 1.0.0
 * Author: Karishma Shukla
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Enqueue block editor assets
function dapii_enqueue_scripts() {
    wp_register_script(
        'dapii-block-editor',
        plugins_url( 'build/index.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-editor' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'build/index.js' )
    );

    wp_localize_script( 'dapii-block-editor', 'dapiiApi', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'dapii_api_nonce' ),
    ) );

    wp_enqueue_script( 'dapii-block-editor' );

    wp_enqueue_style(
        'dapii-block-editor',
        plugins_url( 'build/editor.css', __FILE__ ),
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'build/editor.css' )
    );
}
add_action( 'enqueue_block_editor_assets', 'dapii_enqueue_scripts' );

// Add menu item for settings
function dapii_add_settings_page() {
    add_options_page(
        __( 'Dynamic API Integration Settings', 'dynamic-api-integration' ),
        __( 'API Integration', 'dynamic-api-integration' ),
        'manage_options',
        'dapii-settings',
        'dapii_render_settings_page'
    );
}
add_action( 'admin_menu', 'dapii_add_settings_page' );

// Render settings page
function dapii_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Dynamic API Integration Settings', 'dynamic-api-integration' ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'dapii_settings_group' );
            do_settings_sections( 'dapii-settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings
function dapii_register_settings() {

    register_setting( 'dapii_settings_group', 'dapii_api_url', array(
        'sanitize_callback' => 'esc_url_raw',
    ) );

    register_setting( 'dapii_settings_group', 'dapii_request_type', array(
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    register_setting( 'dapii_settings_group', 'dapii_api_params', array(
        'sanitize_callback' => 'dapii_sanitize_json',
    ) );

    add_settings_section(
        'dapii_settings_section',
        __( 'API Configuration', 'dynamic-api-integration' ),
        null,
        'dapii-settings'
    );

    add_settings_field(
        'dapii_api_url',
        __( 'API URL', 'dynamic-api-integration' ),
        'dapii_api_url_callback',
        'dapii-settings',
        'dapii_settings_section'
    );

    add_settings_field(
        'dapii_request_type',
        __( 'Request Type', 'dynamic-api-integration' ),
        'dapii_request_type_callback',
        'dapii-settings',
        'dapii_settings_section'
    );

    add_settings_field(
        'dapii_api_params',
        __( 'API Parameters', 'dynamic-api-integration' ),
        'dapii_api_params_callback',
        'dapii-settings',
        'dapii_settings_section'
    );
}
add_action( 'admin_init', 'dapii_register_settings' );

function dapii_sanitize_json( $input ) {
    $decoded = json_decode( $input, true );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return ''; // Invalid JSON, return empty string
    }
    return wp_json_encode( $decoded ); // Return re-encoded JSON
}

// Callbacks for settings fields
function dapii_api_url_callback() {
    $value = get_option( 'dapii_api_url', '' );
    echo '<input type="url" name="dapii_api_url" value="' . esc_attr( $value ) . '" class="regular-text">';
}

function dapii_request_type_callback() {
    $value = get_option( 'dapii_request_type', 'GET' );
    ?>
    <select name="dapii_request_type">
        <option value="GET" <?php selected( $value, 'GET' ); ?>>GET</option>
        <option value="POST" <?php selected( $value, 'POST' ); ?>>POST</option>
    </select>
    <?php
}

function dapii_api_params_callback() {
    $value = get_option( 'dapii_api_params', '' );
    echo '<textarea name="dapii_api_params" rows="5" class="large-text">' . esc_textarea( $value ) . '</textarea>';
}

// Register block
function dapii_register_block() {
    wp_register_script(
        'dynamic-api-integration-block',
        plugins_url( 'build/index.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-editor' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'build/index.js' )
    );

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
add_action( 'init', 'dapii_register_block' );

// Register the API endpoint
add_action( 'rest_api_init', function () {
    register_rest_route( 'dapii/v1', '/fetch-data', array(
        'methods'  => 'POST',
        'callback' => 'dapii_fetch_api_data_rest',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        },
    ) );
});

// Handle API calls
function dapii_fetch_api_data_rest( WP_REST_Request $request ) {
    $api_url = get_option( 'dapii_api_url' );
    $request_type = get_option( 'dapii_request_type', 'GET' );
    $params = get_option( 'dapii_api_params', '' );

    if ( ! $api_url ) {
        return new WP_Error( 'no_url', __( 'API URL is not configured.', 'dynamic-api-integration' ), array( 'status' => 400 ) );
    }

    $query_args = ! empty( $params ) ? json_decode( $params, true ) : [];
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return new WP_Error( 'invalid_params', __( 'Invalid JSON format for API parameters.', 'dynamic-api-integration' ), array( 'status' => 400 ) );
    }

    $response = ( $request_type === 'POST' ) 
        ? wp_remote_post( $api_url, array( 'body' => $query_args ) )
        : wp_remote_get( add_query_arg( $query_args, $api_url ) );

    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'api_error', $response->get_error_message(), array( 'status' => 500 ) );
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return new WP_Error( 'invalid_response', __( 'Invalid JSON response from the API.', 'dynamic-api-integration' ), array( 'status' => 500 ) );
    }

    return rest_ensure_response( $data );
}
