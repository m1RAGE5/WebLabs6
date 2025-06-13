<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class SaveNewFolder
{
    public function execute()
    {
        $request = json_decode(file_get_contents('php://input'), true);

        $fs = FS::getInstance();

        $response = $fs->create($request['id'], $request['name'], true);

        echo json_encode([
            'name' => $request['name'],
            'path' => $request['id'] . DIRECTORY_SEPARATOR . $request['name'],
            'type' => 'folder',
            'state' => 0,
            'dirty' => false,
            'id' => $response['id']
        ]);
        die;
    }
}
