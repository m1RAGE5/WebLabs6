<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class RenameFile
{
    public function execute()
    {
        $request = json_decode(file_get_contents('php://input'), true);

        $path = $request['id'];
        $newName = $request['newName'];

        $fs = FS::getInstance();
        $fs->rename($path, $newName);

        // Replace last occurance of $name in path with $newName
        $newPath = substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR) + 1) . $newName;


        echo json_encode([
            'new_id' => $fs->id(WPCBIDE_ABSPATH . DIRECTORY_SEPARATOR . $newPath)
        ]);
        die;
    }

}