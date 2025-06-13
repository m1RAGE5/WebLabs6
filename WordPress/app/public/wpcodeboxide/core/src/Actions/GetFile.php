<?php

namespace Unicorn\Actions;


use Unicorn\Service\FS;

class GetFile
{
    public function execute()
    {
        if(isset($_GET['file'])) {

            $fs = FS::getInstance();
            $path = $fs->path($_GET['file']);

            $fileData = [
                'file' => $_GET['file'],
                'content' => file_get_contents( $path)
            ];

            if(strpos($_GET['file'], 'plugins') !== false) {

                // Add the plugin name to the response (the value of the folder after plugins/)
                $pathParts = explode('/', $_GET['file']);
                $pluginName = $pathParts[array_search('plugins', $pathParts) + 1];
                $fileData['plugin'] = $pluginName;
            }



            echo json_encode($fileData);
            die;
        }

    }
}
