<?php


namespace Unicorn\Actions;

class GetActivePlugins
{
    public function execute()
    {
        if(file_exists(B_ABSPATH . 'wp-load.php')) {
            include_once B_ABSPATH . 'wp-load.php';
        }
        else {
            echo json_encode([]);
            die;
        }

        global $wpdb;

        $active_plugins = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'active_plugins'");
        $active_plugins = unserialize($active_plugins);
        if(!is_array($active_plugins)) {
            $active_plugins = [];
        }

        $active_plugins_response = [];

        foreach ($active_plugins as $plugin) {
            $active_plugins_response[] = $plugin;
        }
        //var_dump($active_plugins_response);
        echo json_encode(array_values($active_plugins_response));
        die;

    }
}

