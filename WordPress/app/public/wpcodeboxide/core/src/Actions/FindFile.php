<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class FindFile
{
    public function execute()
    {

        $request = json_decode(file_get_contents('php://input'), true);

        $id = $request['id'];
        $search = $request['search'];

        $fs = FS::getInstance();

        $response = $fs->findFileByNameRecursive($id, $search);

        echo json_encode($response);

        die;
    }

}