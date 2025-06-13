<?php
namespace Unicorn\Service;

class ClassParser
{
    public function getPhpClassesInDirectory($directory)
    {
        $classes = [];
        $allFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        $phpFiles = new \RegexIterator($allFiles, '/\.php$/');

        foreach ($phpFiles as $phpFile) {

            if(strpos($phpFile->getPathname(), 'plugins/wpcbide_backend') !== false) {
                continue;
            }
            $content = file_get_contents($phpFile->getRealPath());
            $tokens = token_get_all($content);
            $namespace = '';

            for ($index = 0; $index < count($tokens); $index++) {
                if ($tokens[$index][0] === T_NAMESPACE) {
                    $index += 2; // Skip namespace keyword and whitespace
                    while (isset($tokens[$index]) && is_array($tokens[$index])) {
                        $namespace .= $tokens[$index++][1];
                    }
                }

                if ($tokens[$index][0] === T_CLASS) {
                    for ($j = $index + 1; $j < count($tokens); $j++) {
                        if ($tokens[$j] === '{') {
                            $className = $tokens[$index + 2][1];
                            $classes[] = $namespace ? $namespace . '\\' . $className : $className;
                            break;
                        }
                    }
                }
            }
        }

        // Remove empty classes
        $classes = array_filter($classes, function($class) {
            return !empty(trim($class));
        });

        return $classes;
    }


}