<?php
namespace Unicorn\Actions;

use Unicorn\Service\FS;

class SaveSettings
{
    public function execute()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $credentials = include (__DIR__ . '/../../credentials.php');

        if(isset($request['user'])) {
            $credentials['user'] = $request['user'];
        }

        if(isset($request['password']) && !empty($request['password'])) {
            $credentials['password'] = password_hash($request['password'], PASSWORD_DEFAULT);
        }

        file_put_contents(__DIR__ . '/../../credentials.php', '<?php return ' . var_export($credentials, true) . ';');
        die;
    }
}
