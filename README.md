# Dynamic API Integration

**Contributors:**      karishmashukla  
**Tags:**              block, API, Gutenberg, REST API  
**Tested up to:**      6.7  
**Stable tag:**        1.0.0  
**License:**           GPL-2.0-or-later  
**License URI:**       https://www.gnu.org/licenses/gpl-2.0.html  

---

## Plugin Information

**Plugin Name:** Dynamic API Integration  
**Description:** A plugin for dynamic API integration in the Gutenberg block editor.  
**Version:** 1.0.0  
**Author:** Karishma Shukla  
**License:** GPL-2.0-or-later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

---

## Features

- Dynamically fetch data from external APIs.
- Display API data within a custom Gutenberg block.
- Configure API URL, request type, and parameters from the WordPress admin settings.
- Supports both GET and POST request methods.
- REST API endpoint for secure communication.

---

## Installation

1. Download the plugin ZIP file.
2. Go to your WordPress admin dashboard.
3. Navigate to **Plugins > Add New**.
4. Click **Upload Plugin** and select the ZIP file.
5. Click **Install Now** and then **Activate**.

---

## Usage

### 1. Configure API Settings

1. Navigate to **Settings > API Integration** in the WordPress admin panel.
2. Configure the following options:
   - **API URL:** Enter the external API endpoint.
   - **Request Type:** Select `GET` or `POST`.
   - **Parameters:** Provide JSON-formatted parameters (e.g., `{ "key": "value" }`).
3. Save the settings.

### 2. Add the Gutenberg Block

1. Open the WordPress block editor (Gutenberg).
2. Search for the **Dynamic API Integration** block.
3. Add the block to your post or page.
4. Preview or publish the post to view the fetched API data.

---

## REST API Endpoint

This plugin exposes a custom REST API endpoint:

- **Endpoint:** `/wp-json/dapii/v1/fetch-data`
- **Request Type:** Configurable via the admin settings.
- **Parameters:** Configurable via the admin settings.

---

## Development

### Key Files

- `dynamic-api-integration.php`: Main plugin file.
- `build/index.js`: JavaScript for the Gutenberg block.
- `build/style-index.css`: Frontend styles.

### Build Process

The `build` folder contains assets generated using a JavaScript bundler (e.g., Webpack). To modify and rebuild assets, developers should:

1. Ensure Node.js and npm are installed.
2. Run `npm install` to install dependencies.
3. Run `npm run build` to generate updated files in the `build` folder.

### Custom Functions

#### `dapii_fetch_api_data_rest(WP_REST_Request $request)`

Handles requests to fetch data from the external API. Utilizes WordPress functions for secure and efficient API communication.

#### `dapii_register_settings()`

Registers plugin settings in the WordPress admin panel.

#### `dapii_register_block()`

Registers the custom Gutenberg block with the WordPress editor.

---

## Troubleshooting

- **"API URL is not configured" Error:** Ensure the API URL is entered in the settings.
- **"Failed to fetch API data" Error:** Verify the external API endpoint and parameters.
- **Block Not Displaying Data:** Check the REST API response and console logs for errors.

---

## Changelog

### 1.0.0

- Introduced settings page for API configuration.
- Added REST API endpoint for external API integration.
- Created a custom Gutenberg block for displaying fetched data.
- Implemented support for GET and POST API request methods.

---

## License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

## Support

For support, please contact the plugin developer or raise an issue in the plugin repository.

