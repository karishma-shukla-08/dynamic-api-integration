# Dynamic API Integration

Contributors: karishmashukla  
Tags: block, API integration, Gutenberg  
Requires at least: 5.0  
Tested up to: 6.7  
Requires PHP: 7.4  
Stable tag: 1.0.0  
License: GPL-2.0-or-later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

## Plugin Information
Plugin Name: Dynamic API Integration  
Description: A plugin for dynamic API integration in the Gutenberg block editor.  
Version: 1.0.0  
Author: Karishma Shukla  
License: GPL-2.0-or-later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

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
### Configure API Settings
1. Navigate to **Settings > API Integration** in the WordPress admin panel.
2. Configure the following options:
   - **API URL:** Enter the external API endpoint.
   - **Request Type:** Select `GET` or `POST`.
   - **Parameters:** Provide JSON-formatted parameters (e.g., `{ "key": "value" }`).
3. Save the settings.

### Add the Gutenberg Block
1. Open the WordPress block editor (Gutenberg).
2. Search for the **Dynamic API Integration** block.
3. Add the block to your post or page.
4. Preview or publish the post to view the fetched API data.

---

## REST API Endpoint
This plugin exposes a custom REST API endpoint:

- **Endpoint:** `/wp-json/dai/v1/fetch-data`
- **Request Type:** Configurable via the admin settings.
- **Parameters:** Configurable via the admin settings.

---

## Frequently Asked Questions

### Q: What is the minimum WordPress version required?  
A: This plugin requires WordPress version 5.0 or higher.

### Q: What is the minimum PHP version required?  
A: This plugin requires PHP version 7.4 or higher.

### Q: How do I use this plugin?  
A: Configure the API settings in the WordPress admin under **Settings > API Integration**, then add the **Dynamic API Integration** block to your post or page.

---


## Upgrade Notice
### 1.0.0
Initial release with support for API integration and custom Gutenberg block.

---

## Changelog
### 1.0.0
- Initial release.

---

## License
This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

## Support
For support, please contact the plugin developer or raise an issue in the plugin repository.
