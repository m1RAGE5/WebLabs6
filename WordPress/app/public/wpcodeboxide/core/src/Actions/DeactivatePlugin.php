<?php
namespace Unicorn\Actions;

use Unicorn\Service\Plugin as PluginService;

class DeactivatePlugin
{

    private $pluginService;

    public function __construct()
    {
        $this->pluginService = new PluginService();
    }
    public function execute()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        // Plugin path relative to the wp-content/plugins directory
        // $plugin_path = 'plugin-folder/plugin-file.php';

        $plugin_path = $data['id'];

        $plugin_path = $this->pluginService->get_main_plugin_file($plugin_path);

        $this->pluginService->deactivate_plugin_via_wpdb($plugin_path);

        echo json_encode([
            'state' => 0,
            'main_file' => $plugin_path,
            'id' => $data['id']
        ]);
    }
}
