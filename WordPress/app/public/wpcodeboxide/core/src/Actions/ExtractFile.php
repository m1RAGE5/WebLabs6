<?php
namespace Unicorn\Actions;

use Unicorn\Service\FS;
use Unicorn\Service\ZipUtil;

class ExtractFile
{
    public function execute()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $path = $request['id'];
        $fs = FS::getInstance();
        $path = $fs->path($path);

        ZipUtil::unzipFolder($path);
        die;
    }

}
