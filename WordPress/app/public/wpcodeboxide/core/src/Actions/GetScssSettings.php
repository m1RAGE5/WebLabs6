<?php

namespace Unicorn\Actions;

use Unicorn\Service\Settings;

class GetScssSettings
{
    public function execute()
    {
        $request = json_decode(file_get_contents('php://input'), true);

        $settings = new Settings();

        $file = $request['file'];
        $cssMap = $settings->get('cssMap', []);

        $response = [];


        if(isset($cssMap[$file])) {
            $response = [
                'compile' => true,
                'compilePath' => $cssMap[$file]
            ];
        } else {
            $response = [
                'compile' => false,
                'compilePath' => str_replace('.scss', '.css', $file)
            ];
        }


        echo json_encode($response);
        die;

    }
}
