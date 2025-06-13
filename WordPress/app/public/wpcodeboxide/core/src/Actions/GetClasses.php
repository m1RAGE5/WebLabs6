<?php
namespace Unicorn\Actions;


use Unicorn\Service\ClassParser;

class GetClasses
{
    public function execute()
    {
        $classParser = new ClassParser();
        $classes = $classParser->getPhpClassesInDirectory(B_ABSPATH . '/wp-content/plugins/' . $_GET['plugin']);

        echo json_encode(['classes' => $classes]);
        die;

    }
}

