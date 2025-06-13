<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class DownloadFile
{
    public function execute()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $path = $request['id'];
        $fs = FS::getInstance();
        $path = $fs->path($path);
        echo file_get_contents($path);

        die;
    }




}
