<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class DownloadFolder
{
    public function execute()
    {

        $request = json_decode(file_get_contents('php://input'), true);
        $path = $request['id'];

        $fs = FS::getInstance();

        $folderName = basename($path);
        $source = $fs->path($path);
        $destination = $fs->path($path) . '/' .$folderName .'.zip';


        $this->zipData($source, $destination);
        echo file_get_contents($destination);
        unlink($destination);

        die;
    }


    function zipData( $source, $destination )
    {
        $zip = new ZipArchive();
        if($zip->open($destination, ZIPARCHIVE::CREATE) === true) {
            $source = realpath($source);
            if(is_dir($source)) {
                $iterator = new RecursiveDirectoryIterator($source);
                $iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
                foreach($files as $file) {
                    $file = realpath($file);
                    if(is_dir($file)) {
                        $zip->addEmptyDir(str_replace($source . DIRECTORY_SEPARATOR, '', $file . DIRECTORY_SEPARATOR));
                    }elseif(is_file($file)) {
                        $zip->addFile($file,str_replace($source . DIRECTORY_SEPARATOR, '', $file));
                    }
                }
            }elseif(is_file($source)) {
                $zip->addFile($source,basename($source));
            }
        }
        return $zip->close();
    }



}
