<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class DeleteFolder
{
    public function execute()
    {

        $request = json_decode(file_get_contents('php://input'), true);

        $path = $request['id'];


        $fs = FS::getInstance();
        $fs->remove($path);

        echo json_encode([]);
        die;
    }





}
