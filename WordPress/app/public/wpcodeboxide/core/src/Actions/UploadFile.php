<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class UploadFile
{
    public function execute()
    {

        $fs = FS::getInstance();

        $folder = $_POST['folder'];
        $target_dir = $fs->path($folder);
        $target_file = $target_dir . DIRECTORY_SEPARATOR . basename($_FILES["file"]['name']);
        //TODO: Check if file exists
        //TODO: Check if file is too big
        //TODO: Check if file is allowed
        //TODO: Catch errors and report back meaningul messages

        move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);

        echo json_encode([
            'file' => $folder . DIRECTORY_SEPARATOR . basename($_FILES["file"]['name']),
            'state' => 0,
            'dirty' => false,
            'id' => $folder . DIRECTORY_SEPARATOR . basename($_FILES["file"]['name'])
        ]);
        die;
    }




}
