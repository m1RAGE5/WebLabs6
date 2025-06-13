<?php

namespace Unicorn\Actions;

use Unicorn\Service\Settings;

class SaveScssSettings
{
    public function execute()
    {
        $request = json_decode(file_get_contents('php://input'), true);

        $settings = new Settings();

        $file = $request['file'];
        $compile = $request['compile'];
        $compilePath = $request['compilePath'];

        $cssMap = $settings->get('cssMap', []);

        if($compile) {
            $cssMap[$file] = $compilePath;
        } else {
            if(isset($cssMap[$file])) {
                unset($cssMap[$file]);
            }
        }

        $settings->set('cssMap', $cssMap);
        $settings->save();

        echo json_encode([]);
        die;

    }
}
