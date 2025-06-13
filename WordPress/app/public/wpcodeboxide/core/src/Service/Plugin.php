<?php

namespace Unicorn\Service;


class Plugin
{
    public function activate_plugin_via_wpdb($plugin_path)
    {
        if(file_exists(B_ABSPATH . 'wp-load.php')) {
            include_once B_ABSPATH . 'wp-load.php';
        } else {
            return;
        }

        global $wpdb;

        // Fetch the active plugins list
        $active_plugins = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'active_plugins'");
        $active_plugins = unserialize($active_plugins);
        if(!is_array($active_plugins)) {
            $active_plugins = [];
        }

        // Check if the plugin is active
        if (!in_array($plugin_path, $active_plugins)) {
            // Add the plugin to the active plugins array
            $active_plugins[] = $plugin_path;

            // Update the active plugins option
            $active_plugins = serialize($active_plugins);
            $wpdb->query("UPDATE $wpdb->options SET option_value = '$active_plugins' WHERE option_name = 'active_plugins'");
        }
    }

    public function deactivate_plugin_via_wpdb($plugin_path) {

        if(file_exists(B_ABSPATH . 'wp-load.php')) {
            include_once B_ABSPATH . 'wp-load.php';
        } else {
            return;
        }

        global $wpdb;

        $active_plugins = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'active_plugins'");
        $active_plugins = unserialize($active_plugins);

        if(!is_array($active_plugins)) {
            $active_plugins = [];
        }

        if (in_array($plugin_path, $active_plugins)) {
            $active_plugins = array_diff($active_plugins, array($plugin_path));
            $active_plugins = serialize($active_plugins);
            $wpdb->query("UPDATE $wpdb->options SET option_value = '$active_plugins' WHERE option_name = 'active_plugins'");
        }
    }

    // Function to get the main plugin file from a directory by checking for the plugin header comment
    function get_main_plugin_file($plugin_directory)
    {

        $plugin_path = B_ABSPATH . $plugin_directory;

        // Check if the plugin directory exists
        if (!is_dir($plugin_path)) {
            return false;
        }

        // Scan the directory for PHP files
        $files = scandir($plugin_path);

        foreach ($files as $file) {
            $file_path = $plugin_path . '/' . $file;

            // Skip directories and non-PHP files
            if (is_dir($file_path) || pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            // Open the file and read its content
            $file_content = file_get_contents($file_path);

            // Look for the plugin header comment
            if (preg_match('/^[ \t*\/]*Plugin\s+Name\s*:\s*(.+)$/mi', $file_content)) {
                $plugin_directory = str_replace('wp-content/plugins/', '', $plugin_directory);
                return $plugin_directory . '/' . $file; // Return the plugin file relative to the plugin directory
            }
        }

        return false; // Return false if no valid plugin file is found
    }


}
