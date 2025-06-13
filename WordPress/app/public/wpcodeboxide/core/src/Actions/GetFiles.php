<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class GetFiles
{
    public function execute()
    {
        $fs = FS::getInstance();

        $active_plugins = [];
        try {
            $folder = '/';
            if(isset($_GET['folder'])) {
                $folder = $_GET['folder'];
            }
            $directory = $fs->lst($folder, false);
        }
        catch (\Exception $e) {
            echo json_encode([
                'error' => $e->getMessage()
            ]);
            die;
        }

        echo json_encode([
            'abspath' => B_ABSPATH,
            'items' => $directory,
            'active_plugins' => $active_plugins
        ]);
        die;

    }




}
