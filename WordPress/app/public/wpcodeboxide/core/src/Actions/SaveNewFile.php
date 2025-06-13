<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class SaveNewFile
{
    public function execute()
    {

        $request = json_decode(file_get_contents('php://input'), true);

        $fs = FS::getInstance();

        $filePath = $request['id']. DIRECTORY_SEPARATOR . $request['name'];

        $name = $request['name'];

        $response = $fs->create($request['id'], $request['name'], false);

        if(strpos($filePath, '.scss') !== false && !str_starts_with($name, '_')) {
            $minifiedFile = substr($request['name'], 0, strrpos($request['name'], '.')) . '.css';
            $fs->create($request['id'], $minifiedFile, false);

        }

        if(strpos($filePath, '.php') !== false) {
            $blankCode = <<<EOT
<?php


EOT;

            $fs->setContent($filePath, $blankCode);
        }


        echo json_encode([
            'name' => $request['name'],
            'path' => $filePath,
            'type' => 'file',
            'state' => 0,
            'dirty' => false,
            'id' => $response['id'],
            'children' => []
        ]);
        die;
    }
}
