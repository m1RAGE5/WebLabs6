<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class MoveFile
{

    public function execute()
    {
        $request = json_decode(file_get_contents('php://input'), true);

        $id = $request['id'];
        $newPath = $request['newPath'];

        if($request['type'] === 'file') {
            $name = $request['name'];

            $fs = FS::getInstance();

            if(is_file($newPath . DIRECTORY_SEPARATOR . $name)) {
                while (!is_file($request['newPath'] . DIRECTORY_SEPARATOR . $name)) {
                    // Add _copy before the extension
                    $name = substr($name, 0, strrpos($name, '.')) . '_copy' . substr($name, strrpos($name, '.'));
                }
            }

            $newId = $fs->move($id, $newPath . DIRECTORY_SEPARATOR );

            echo json_encode([
                'name' => $request['name'],
                'id' => $newId['id'],
                'type' => 'file',
                'state' => 0,
                'dirty' => false,
                'children' => []
            ]);

        } else if($request['type'] === 'folder') {


            $fs = FS::getInstance();
            $name = $request['name'];

            if(is_dir($request['newPath'] . DIRECTORY_SEPARATOR . $name)) {
                while(!is_dir($request['newPath'] . DIRECTORY_SEPARATOR . $name)) {
                    $name = $name . '_copy';
                }
            }


            $newId = $fs->move($id, $request['newPath'] );

            echo json_encode([
                'name' => $name,
                'path' => $request['newPath'] . DIRECTORY_SEPARATOR . $name,
                'id' => $newId['id'],

                'type' => 'folder',
                'state' => 0,
                'dirty' => false,
                'children' => $fs->lst($request['newPath'] . DIRECTORY_SEPARATOR . $name , false)
        ]);
        }


        die;

    }


}
