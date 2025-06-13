<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class PasteFile
{
    private FS $fs;

    public function execute()
    {
        $this->fs = FS::getInstance();

        $request = json_decode(file_get_contents('php://input'), true);

        if ($request['type'] === 'file') {
            echo $this->pasteFile($request);
        } else if ($request['type'] === 'folder') {
            echo $this->pasteFolder($request);
        }

        die;
    }


    function pasteFile($request)
    {
        $name = basename($request['file']);
        $newPath = $request['id'];

        if (is_file($newPath . DIRECTORY_SEPARATOR . $name)) {
            // Add _copy before the extension

            while (is_file($newPath . DIRECTORY_SEPARATOR . $name)) {
                $name = substr($name, 0, strrpos($name, '.')) . '_copy' . substr($name, strrpos($name, '.'));
            }
        }
        $response = $this->fs->copyWithNewName($request['file'], $newPath, $name);

        if ($request['originType'] === 'cut') {
            $this->fs->remove($request['file']);
        }

        return json_encode([
            'name' => $name,
            'type' => 'file',
            'state' => 0,
            'dirty' => false,
            'id' => $response['id']
        ]);
    }

    function pasteFolder($request)
    {

        $destinationPath = $request['id'];
        $sourcePath = $request['file'];

        $folderName = basename($sourcePath);


        if (is_dir($destinationPath . DIRECTORY_SEPARATOR . $folderName)) {
            while (is_dir($destinationPath . DIRECTORY_SEPARATOR . $folderName))
                $folderName = $folderName . '_copy';
        }

        $response = $this->fs->copyWithNewName($request['file'], $request['id'], $folderName);

        if ($request['originType'] === 'cut') {
            $this->fs->remove($request['file']);
        }
        return json_encode([
            'name' => $folderName,
            'path' => $destinationPath,
            'type' => 'folder',
            'state' => 0,
            'dirty' => false,
            'id' => $response['id'],
            'children' => $this->fs->lst($request['id'] . DIRECTORY_SEPARATOR . $folderName)
        ]);
    }
}
