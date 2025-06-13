<?php

namespace Unicorn\Actions;

use Unicorn\Service\FS;

class CreateClass
{
    public function execute()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $className = $data['className'];
        $plugin = $data['plugin'];
        $namespace = $data['namespace'];
        $folder = $data['folder'];

        $fs = FS::getInstance();
        $classPath = $folder . '/'. $className . '.php';

        $namespaceWithoutBackslashes = str_replace("\\", '', $namespace);
        $classCode = <<<EOT
$<?php

namespace $namespaceWithoutBackslashes;

class $className
{
    public function __construct()
    {
        // Constructor
    }
}
EOT;

        $fs->setContent($classPath, $classCode);

        echo json_encode([
            'name' => $className.'.php',
            'id' => $fs->id(WPCBIDE_ABSPATH . DIRECTORY_SEPARATOR . $classPath),
            'path' => $classPath,
            'namespace' => $namespace,
            'className' => $className,
            'type' => 'file',
        ]);
        die;

    }
}
