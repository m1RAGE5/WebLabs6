<?php

namespace Unicorn\Service;


use Unicorn\Service\Minify\MinifyFactory;
use Wpcb\Service\ExternalFile;

class SCSSUpdater
{
    public function recompileCode($snippetId) {

        $code = \get_post_meta($snippetId, 'wpcb_original_code', true);
        $minify = \get_post_meta($snippetId, 'wpcb_minify', true);

        $compiler = new \Wpcb\Compiler();
        $code = $compiler->compileCode($code, 'scss');

        if(isset($minify) && $minify) {
            $minifyFactory = new MinifyFactory();
            $minifyService = $minifyFactory->createMinifyService('scss');
            $code = $minifyService->minify($code);
        }

        \update_post_meta($snippetId, 'wpcb_code', $code);

        $renderType = \get_post_meta($snippetId, 'wpcb_render_type', true);

        if($renderType === 'external') {
            $externalFileService = new ExternalFile();
            $externalFileService->writeContentToFile($snippetId . '.' . 'css', $code);
        }
    }

}