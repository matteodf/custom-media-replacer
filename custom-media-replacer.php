<?php
/*
Plugin Name: Custom Media URL Replacer
Description: Replace media URLs with remote media URLs.
Version: 1.0
Author: Matteo De Filippis
*/

if (strpos(home_url(), 'localhost') !== false) {

    add_filter('wp_get_attachment_url', 'replace_media_base_url');

    function replace_media_base_url($url)
    {
        $home_url = home_url();
        $remote_media_url = REMOTE_MEDIA_URL;

        // Replace the local home URL with the remote media URL
        if (strpos($url, $home_url) !== false) {
            $url = str_replace($home_url, $remote_media_url, $url);
        }

        return $url;
    }

    add_filter('wp_calculate_image_srcset', 'replace_srcset_base_url');

    function replace_srcset_base_url($sources)
    {
        $home_url = home_url();
        $remote_media_url = REMOTE_MEDIA_URL;

        foreach ($sources as &$source) {
            if (strpos($source['url'], $home_url) !== false) {
                $source['url'] = str_replace($home_url, $remote_media_url, $source['url']);
            }
        }

        return $sources;
    }

    function hide_media_menu_for_admin()
    {
        // Check if the current user is an administrator
        if (current_user_can('administrator')) {
            // Remove the Media menu item
            remove_menu_page('upload.php');
        }
    }

    // Hook the function to 'admin_menu' so it runs when the admin menu is being built
    add_action('admin_menu', 'hide_media_menu_for_admin', 999);

    function localhost_environment_notice()
    {
        echo '<div class="notice notice-error">
                 <p style="font-size:0.9rem;"><strong>Caution:</strong> You are on localhost. You should modify content on the staging server.</p>
              </div>';
    }

    // Hook the function to 'admin_notices' so it runs in the admin area
    add_action('admin_notices', 'localhost_environment_notice');
}
