<?php

namespace Unicorn\Service;

class FS
{
    private $base;

    private static $fs;

    public static function getInstance() {
        if (!self::$fs) {
            self::$fs = new FS();
            self::$fs->init(WPCBIDE_ABSPATH);
        }

        return self::$fs;
    }

    public function init($base)
    {
        $this->base = $this->real($base);
        if (!$this->base) {
            throw new \Exception('Base directory does not exist');
        }
    }

    public function real($path)
    {
        $temp = realpath($path);

        if (!$temp) {
            throw new \Exception('Path does not exist: ' . $path);
        }
        if ($this->base && strlen($this->base)) {
            if (strpos($temp, $this->base) !== 0) {
                throw new \Exception('Path is not allowed');
            }
        }
        return $temp;
    }

    public function path($id)
    {
        $id = str_replace('/', DIRECTORY_SEPARATOR, $id);
        $id = trim($id, DIRECTORY_SEPARATOR);
        $id = $this->real($this->base . DIRECTORY_SEPARATOR . $id);
        return $id;
    }

    public function id($path)
    {
        $path = $this->real($path);
        $path = substr($path, strlen($this->base));
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        $path = trim($path, '/');
        return strlen($path) ? $path : '/';
    }

    function isPlugin($id)
    {
        $arr = explode("/", $id);
        if (isset($arr[count($arr) - 1]) && $arr[count($arr) - 1] == 'plugins') {
            return true;
        }

        return false;
    }

    function isTheme($id)
    {
        $arr = explode("/", $id);
        if (isset($arr[count($arr) - 1]) && $arr[count($arr) - 1] == 'themes') {
            return true;
        }

        return false;
    }

    public function lst($id, $with_root = false)
    {

        $dir = $this->path($id);
        $lst = @scandir($dir);
        if (!$lst) {
            throw new \Exception('Could not list path: ' . $dir);
        }
        $res = array();
        foreach ($lst as $item) {
            if ($item == '.' || $item == '..' || $item === null) {
                continue;
            }

            if ($item == 'wpcodeboxide') {
                continue;
            }
            $tmp = preg_match('([^ a-zа-я-_0-9.]+)ui', $item);
            if ($tmp === false || $tmp === 1) {
                continue;
            }

            if($item === '.DS_Store' || $item === '.idea' || $item === '.git' || $item === 'node_modules' || $item === 'vendor' || $item === 'wpcodeboxide') {
                continue;
            }

            if(is_link($dir . DIRECTORY_SEPARATOR . $item)) {
                continue;
            }

            if (is_dir($dir . DIRECTORY_SEPARATOR . $item)) {

                $data = array(
                    'type'=>'folder',
                    'name' => $item,
                    'children' => $this->lst($this->id($dir . DIRECTORY_SEPARATOR . $item)),
                    'id' => $this->id($dir . DIRECTORY_SEPARATOR . $item), 'icon' => 'folder');



                $res[] = $data;
            } else {
                $data = array('name' => $item, 'children' => false, 'id' => $this->id($dir . DIRECTORY_SEPARATOR . $item), 'type' => 'file', 'icon' => 'file file-' . substr($item, strrpos($item, '.') + 1));
                if ($this->isPlugin($id)) {
                    $data['icon'] = 'plugin';
                }

                if(strpos($item, '.scss') !== false) {
                    $settings = new Settings();
                    $settings->load();

                    $cssMap = $settings->get('cssMap', []);

                    if(isset($cssMap[$this->id($dir . DIRECTORY_SEPARATOR . $item)])) {
                        $data['compile'] = true;
                    } else {
                        $data['compile'] = false;
                    }
                }

                $res[] = $data;
            }
        }
        if ($with_root && $this->id($dir) === '/') {
            $res = [
                ['name' => 'WPCodeBox IDE',
                    'children' => $res,
                    'id' => '/',
                    'icon' => 'wordpress',
                    'state' => ['opened' => true /*,'disabled' => true*/]]];

        }
        return $res;
    }

    /**
     * @param $id
     * @return string[]
     * @throws \Exception
     */
    public function data($id)
    {
        if (strpos($id, ":")) {
            $id = array_map(array($this, 'id'), explode(':', $id));
            return array('type' => 'multiple', 'content' => 'Multiple selected: ' . implode(' ', $id));
        }
        $dir = $this->path($id);
        if (is_dir($dir)) {
            return array('type' => 'folder', 'content' => $id);
        }
        if (is_file($dir)) {
            $ext = strpos($dir, '.') !== FALSE ? substr($dir, strrpos($dir, '.') + 1) : '';
            $dat = array('type' => $ext, 'content' => '');
            switch ($ext) {
                case 'txt':
                case 'text':
                case 'md':
                case 'js':
                case 'json':
                case 'css':
                case 'html':
                case 'htm':
                case 'xml':
                case 'c':
                case 'cpp':
                case 'h':
                case 'sql':
                case 'log':
                case 'py':
                case 'rb':
                case 'htaccess':
                case 'php':
                    $dat['content'] = file_get_contents($dir);
                    break;
                case 'jpg':
                case 'jpeg':
                case 'gif':
                case 'png':
                case 'bmp':
                    $dat['content'] = 'data:' . finfo_file(finfo_open(FILEINFO_MIME_TYPE), $dir) . ';base64,' . base64_encode(file_get_contents($dir));
                    break;
                default:
                    $dat['content'] = file_get_contents($dir);
                    break;
            }
            return $dat;
        } else {
            throw new \Exception('Not a valid selection: ' . $dir);
        }
    }

    public function create($id, $name, $mkdir = false)
    {
        $dir = $this->path($id);
        if (preg_match('([^ a-zа-я-_0-9.]+)ui', $name) || !strlen($name)) {
            throw new \Exception('Invalid name: ' . $name);
        }
        if ($mkdir) {
            mkdir($dir . DIRECTORY_SEPARATOR . $name);
        } else {
            file_put_contents($dir . DIRECTORY_SEPARATOR . $name, '');
        }
        return array('id' => $this->id($dir . DIRECTORY_SEPARATOR . $name));
    }

    public function rename($id, $name)
    {
        $dir = $this->path($id);
        if ($dir === $this->base) {
            throw new \Exception('Cannot rename root');
        }
        if (preg_match('([^ a-zа-я-_0-9.]+)ui', $name) || !strlen($name)) {
            throw new \Exception('Invalid name: ' . $name);
        }
        $new = explode(DIRECTORY_SEPARATOR, $dir);
        array_pop($new);
        array_push($new, $name);
        $new = implode(DIRECTORY_SEPARATOR, $new);
        if ($dir !== $new) {
            if (is_file($new) || is_dir($new)) {
                throw new \Exception('Path already exists: ' . $new);
            }
            rename($dir, $new);
        }
        return array('id' => $this->id($new));
    }

    public function remove($id)
    {
        $dir = $this->path($id);
        if ($dir === $this->base) {
            throw new \Exception('Cannot remove root');
        }
        if (is_dir($dir)) {
            foreach (array_diff(scandir($dir), array(".", "..")) as $f) {
                $this->remove($this->id($dir . DIRECTORY_SEPARATOR . $f));
            }
            rmdir($dir);
        }
        if (is_file($dir)) {
            unlink($dir);
        }
        return array('status' => 'OK');
    }

    public function move($id, $par)
    {

        $dir = $this->path($id);
        $par = $this->path($par);

        $new = explode(DIRECTORY_SEPARATOR, $dir);
        $new = array_pop($new);
        $new = $par . DIRECTORY_SEPARATOR . $new;
        rename($dir, $new);
        return array('id' => $this->id($new));
    }

    public function copy($id, $par)
    {
        $dir = $this->path($id);
        $par = $this->path($par);
        $new = explode(DIRECTORY_SEPARATOR, $dir);
        $new = array_pop($new);
        $new = $par . DIRECTORY_SEPARATOR . $new;
        if (is_file($new) || is_dir($new)) {
            throw new Exception('Path already exists: ' . $new);
        }

        if (is_dir($dir)) {
            mkdir($new);
            foreach (array_diff(scandir($dir), array(".", "..")) as $f) {
                $this->copy($this->id($dir . DIRECTORY_SEPARATOR . $f), $this->id($new));
            }
        }
        if (is_file($dir)) {
            copy($dir, $new);
        }
        return array('id' => $this->id($new));
    }

    public function copyWithNewName($id, $par, $name)
    {
        $dir = $this->path($id);
        $par = $this->path($par);

        $new = $par . DIRECTORY_SEPARATOR . $name;
        if (is_file($new) || is_dir($new)) {
            throw new \Exception('Path already exists: ' . $new);
        }

        if (is_dir($dir)) {
            mkdir($new);
            foreach (array_diff(scandir($dir), array(".", "..")) as $f) {
                $this->copy($this->id($dir . DIRECTORY_SEPARATOR . $f), $this->id($new));
            }
        }
        if (is_file($dir)) {
            copy($dir, $new);
        }

        return array('id' => $this->id($new));
    }

    public function setContent($id, $content)
    {
        $path = $this->base . DIRECTORY_SEPARATOR . $id;
        file_put_contents($path, $content);

        return array();
    }

    public function upload($folder)
    {
        $path = $this->path($folder);
        $targetFile = $path . DIRECTORY_SEPARATOR . basename($_FILES["file"]["name"]);
        $rslt = move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile);
    }



    public function findInFile($id, $search, $caseSensitive = false) {

        $res = [];
        $currentLine = 0;

        $file = $this->path($id);

        if(!is_file($file)) {
            return $res;
        }

        // Only include php, js, txt, HTML files
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if($ext !== 'php' && $ext !== 'js' && $ext !== 'txt' && $ext !== 'html') {
            return $res;
        }

        $f = fopen($file, "r");
        while ($line = fgets($f)) {
            $currentLine ++;
            if($caseSensitive) {
                if(strpos($line, $search) !== false) {
                    $res[] = [
                        'line' => $currentLine,
                        'content' => $line
                    ];
                }
            } else {
                if(stripos($line, $search)) {
                    $res[] = [
                        'line' => $currentLine,
                        'content' => $line
                    ];
                }
            }
        }

        return $res;
    }

    public function findFileByNameRecursive($id, $search, $caseSensitive = false) {
        $dir = $this->path($id);
        $res = [];
        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($objects as $name => $object) {
            if ($object->isFile()) {
                if($caseSensitive) {
                    if(strpos($name, $search) !== false) {
                        $res[] = $this->id($name);
                    }
                } else {
                    if(stripos($name, $search)) {
                        $res[] = $this->id($name);
                    }
                }
            }
        }

        return $res;
    }


    public function findInFolderRecursive($id, $search, $caseSensitive = false)
    {
        $dir = $this->path($id);
        $res = [];
        $totalMatches = 0;
        $totalFiles = 0;

        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($objects as $name => $object) {
            if ($object->isFile()) {
                $matches = $this->findInFile($this->id($name), $search, $caseSensitive);
                if (count($matches) > 0) {
                    $totalMatches += count($matches);
                    $totalFiles++;
                    $res[] = [
                        'file' => $this->id($name),
                        'name' => basename($name),
                        'matches' => $matches
                    ];
                }
            }



        }

        return [
            'totalMatches' => $totalMatches,
            'totalFiles' => $totalFiles,
            'results' => $res
        ];

    }
}