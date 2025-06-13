<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class DeleteFile
{
    public function execute()
    {
        $request = json_decode(file_get_contents('php://input'), true);

        $fs = FS::getInstance();
        $fs->init(WPCBIDE_ABSPATH);
        $fs->remove($request['id']);

        echo json_encode([]);
        die;
    }




}
