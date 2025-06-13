<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;
use Unicorn\Service\Settings;

class GeneratePlugin
{

    public function execute()
    {

        $fs = FS::getInstance();

        $body = file_get_contents('php://input');
        $body = json_decode($body, true);

        $pluginName = $body['pluginName'];
        $pluginDescription = $body['pluginDescription'];
        $pluginVersion = $body['pluginVersion'];

        $generateAutoloader = $body['generateAutoloader'];
        $namespace = $body['namespace'];

        $pluginSlug = strtolower(str_replace(' ', '-', $pluginName));

// Generate a WordPress plugin and save it to a file
        $pluginCode = <<<EOT
<?php
/**
 * Plugin Name: $pluginName
 * Description: $pluginDescription
 * Version: $pluginVersion
 */

if(!defined('ABSPATH')) {
    die;
}


EOT;

        if($generateAutoloader) {
            $pluginCode .= <<<EOT
// Let's include the autoloader
include_once(__DIR__ . '/src/Bootstrap.php');

// Register the autoloader
\$bootstrap = new $namespace\Bootstrap();
spl_autoload_register(array(\$bootstrap, 'autoload'));
EOT;

            $namespaceWithoutBackslashes = str_replace("\\", '', $namespace);
            $autoloaderCode = <<<EOD
$<?php

namespace $namespaceWithoutBackslashes;

class Bootstrap
{

    function autoload(\$className) {

        if(strpos(\$className, '\\\\') !== false){

            \$prefix = '$namespace\\\\';
            \$base_dir = str_replace('\\\\', '/', dirname(__FILE__)) . '/';

            // does the class use the namespace prefix?
            \$len = strlen(\$prefix);
            if (strncmp(\$prefix, \$className, \$len) !== 0) {
                // no, move to the next registered autoloader
                return;
            }

            // get the relative class name
            \$relative_class = substr(\$className, \$len);

            // replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            \$file = \$base_dir . str_replace('\\\\', '/', \$relative_class) . '.php';

            // if the file exists, require it
            if (file_exists(\$file)) {
                require_once \$file;
            }
        }

        return;
    }

}

EOD;


        }


        if($this->isGenerateScss($body) || $this->isGenerateCss($body)) {
            $pluginCode .= <<<EOT


// Enqueue styles
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('$pluginSlug', plugin_dir_url(__FILE__) . 'css/styles.css');
});

EOT;
        }

        if($this->isGenerateJs($body)) {
            $pluginCode .= <<<EOT

// Enqueue scripts
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('$pluginSlug', plugin_dir_url(__FILE__) . 'js/$pluginSlug.js');
});


EOT;
        }



// Save it to a file in the folder based on plugin slug
        if (!is_dir($this->getPluginsDir() . $pluginSlug)) {

            $response = $fs->create( 'wp-content' . DIRECTORY_SEPARATOR .'plugins' . DIRECTORY_SEPARATOR , $pluginSlug, true);

            $id = $response['id'];

            if($generateAutoloader) {
                $fs->create( $id, "src", true);
                file_put_contents($this->getPluginPath($pluginSlug) . "src" . DIRECTORY_SEPARATOR . "Bootstrap.php", $autoloaderCode);
            }

            if($this->isGenerateCss($body) || $this->isGenerateScss($body)) {
                $cssFolder = $fs->create( $id, "css", true);
                $cssPath = $cssFolder['id'];
                file_put_contents($this->getPluginPath($pluginSlug) . "css" . DIRECTORY_SEPARATOR . "styles.css", '');

                if($this->isGenerateScss($body)) {
                    file_put_contents($this->getPluginPath($pluginSlug). "css/styles.scss", '');
                }
            }

            if($this->isGenerateJs($body)) {
                mkdir($this->getPluginPath($pluginSlug) . "js");
                file_put_contents($this->getPluginPath($pluginSlug) . "js" . DIRECTORY_SEPARATOR . $pluginSlug . ".js", '');
            }

        }


        file_put_contents($this->getPluginPath($pluginSlug) . "$pluginSlug.php", $pluginCode);



        echo json_encode([
            'name' => $pluginSlug,
            'id' =>   $fs->id(WPCBIDE_ABSPATH . DIRECTORY_SEPARATOR . "/wp-content/plugins/$pluginSlug"),
            'type' => 'folder',
            'state' => 1,
            'dirty' => false,
            'children' => $fs->lst("/wp-content/plugins/$pluginSlug")
        ]);


        die;

    }

    private function isGenerateCss($body) {
        return isset($body['generateCss']) && $body['generateCss'];
    }

    private function isGenerateScss($body) {
        return isset($body['generateScss']) && $body['generateScss'];
    }

    private function isGenerateJs($body) {
        return isset($body['generateJs']) && $body['generateJs'];
    }

    private function getPluginPath($pluginSlug)
    {
        return $this->getPluginsDir()  . $pluginSlug . DIRECTORY_SEPARATOR;
    }

    private function getPluginsDir()
    {
        return WPCBIDE_ABSPATH . 'wp-content/plugins' . DIRECTORY_SEPARATOR;
    }
}
