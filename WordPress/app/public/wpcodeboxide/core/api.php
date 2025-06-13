<?php

include_once(__DIR__ . '/src/Bootstrap.php');

define('B_ABSPATH', getbasepath() . DIRECTORY_SEPARATOR);
define('WP_CONTENT_DIR', B_ABSPATH . 'wp-content/');
define('WPCBIDE_ABSPATH', B_ABSPATH);
define('SHORTINIT', true);

$bootstrap = new \Unicorn\Bootstrap();
spl_autoload_register(array($bootstrap, 'autoload'));


$loginService = new \Unicorn\Service\Login();


// A shortened version of wp_hash from pluggable.php
if ($_GET['wpcodeboxide_route'] !== '/login' && !$loginService->checkToken()) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
    die;
}

$router = new \Unicorn\Http\Router();

$router->map('GET', '/get_files', [new \Unicorn\Actions\GetFiles(), 'execute']);
$router->map('GET', '/get_active_plugins', [new \Unicorn\Actions\GetActivePlugins(), 'execute']);
$router->map('GET', '/get_file', [new \Unicorn\Actions\GetFile(), 'execute']);
$router->map('POST', '/generate_plugin', [new \Unicorn\Actions\GeneratePlugin(), 'execute']);
$router->map('POST', '/rename_file', [new \Unicorn\Actions\RenameFile(), 'execute']);
$router->map('POST', '/rename_folder', [new \Unicorn\Actions\RenameFolder(), 'execute']);
$router->map('POST', '/move_file', [new \Unicorn\Actions\MoveFile(), 'execute']);
$router->map('POST', '/save_new_file', [new \Unicorn\Actions\SaveNewFile(), 'execute']);
$router->map('POST', '/save_new_folder', [new \Unicorn\Actions\SaveNewFolder(), 'execute']);
$router->map('POST', '/delete_file', [new \Unicorn\Actions\DeleteFile(), 'execute']);
$router->map('POST', '/delete_folder', [new \Unicorn\Actions\DeleteFolder(), 'execute']);
$router->map('POST', '/save_file', [new \Unicorn\Actions\SaveFile(), 'execute']);
$router->map('POST', '/upload_file', [new \Unicorn\Actions\UploadFile(), 'execute']);
$router->map('POST', '/download_file', [new \Unicorn\Actions\DownloadFile(), 'execute']);
$router->map('POST', '/download_folder', [new \Unicorn\Actions\DownloadFolder(), 'execute']);
$router->map('POST', '/paste_file', [new \Unicorn\Actions\PasteFile(), 'execute']);
$router->map('POST', '/save_scss_settings', [new \Unicorn\Actions\SaveScssSettings(), 'execute']);
$router->map('POST', '/get_scss_settings', [new \Unicorn\Actions\GetScssSettings(), 'execute']);
$router->map('POST', '/find_in_folder', [new \Unicorn\Actions\FindInFolder(), 'execute']);
$router->map('POST', '/find_file', [new \Unicorn\Actions\FindFile(), 'execute']);
$router->map('GET', '/get_plugin_namespace', [new \Unicorn\Actions\GetNamespace(), 'execute']);
$router->map('POST', '/create_class', [new \Unicorn\Actions\CreateClass(), 'execute']);
$router->map('POST', '/activate_plugin', [new \Unicorn\Actions\ActivatePlugin(), 'execute']);
$router->map('POST', '/deactivate_plugin', [new \Unicorn\Actions\DeactivatePlugin(), 'execute']);
$router->map('POST', '/login', [new \Unicorn\Actions\Login(), 'execute']);
$router->map('POST', '/extract_file', [new \Unicorn\Actions\ExtractFile(), 'execute']);
$router->map('POST', '/zip_folder', [new \Unicorn\Actions\ZipFolder(), 'execute']);
$router->map('GET', '/get_settings', [new \Unicorn\Actions\GetSettings(), 'execute']);
$router->map('POST', '/save_settings', [new \Unicorn\Actions\SaveSettings(), 'execute']);

// match current request url
$match = $router->match($_GET['wpcodeboxide_route'], $_SERVER['REQUEST_METHOD']);

// call closure or throw 404 status
if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
// no route was matched
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

function getbasepath()
{
    $path = dirname(__DIR__);
    $path = explode(DIRECTORY_SEPARATOR . 'wp-content', $path);
    $path = $path[0] . '/../';
    return $path;
}