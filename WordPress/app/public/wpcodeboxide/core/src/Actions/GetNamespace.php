<?php
namespace Unicorn\Actions;

use Unicorn\Service\FS;

class GetNamespace
{
    public function execute()
    {
        $plugin = $_GET['plugin'];
        $pluginNamespace = '';

        $fs = FS::getInstance();

        $pluginPath = $fs->path('/wp-content/plugins/' . $plugin . '/src/Bootstrap.php');
        if (file_exists($pluginPath)) {
            $pluginBootstrap = file_get_contents($pluginPath);
            preg_match('/namespace\s+(.*?);/', $pluginBootstrap, $matches);
            $pluginNamespace = $matches[1];
        }

        echo json_encode(['namespace' => $pluginNamespace]);
        die;

    }
}

