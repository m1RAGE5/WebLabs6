<?php

namespace Unicorn\Service\Minify;


use Wpcb\Service\Minify\MinifyJs;

class MinifyFactory {


    public function createMinifyService($fileType)
    {
        if($fileType === 'css' || $fileType === 'scss' || $fileType === 'less') {
            return new MinifyCss();
        } else if ($fileType === 'js') {
            return new MinifyJs();
        } else {
            return new MinifyNull();
        }
    }

}