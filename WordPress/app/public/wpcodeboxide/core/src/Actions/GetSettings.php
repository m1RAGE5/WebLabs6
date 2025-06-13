<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class GetSettings
{
    public function execute()
    {
        $credentials = include (__DIR__ . '/../../credentials.php');
        $url =  (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        // Remove query string and replace api.php with frontend.php
        $url = substr($url, 0, strpos($url, '?'));
        $url = str_replace('api.php', 'frontend.php', $url);

        echo json_encode([
            'user' => $credentials['user'],
            'url' => $url
        ]);
        die;
    }
}
