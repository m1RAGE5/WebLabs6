<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class FindInFolder
{
    public function execute()
    {

        $request = json_decode(file_get_contents('php://input'), true);

        $id = $request['id'];
        $search = $request['search'];
        $caseSensitive = isset($request['cs']) ? $request['cs'] : false;
        $caseSensitive = !!$caseSensitive;

        $fs = FS::getInstance();

        $response = $fs->findInFolderRecursive($id, $search, $caseSensitive);

        echo json_encode($response);

        die;
    }

}