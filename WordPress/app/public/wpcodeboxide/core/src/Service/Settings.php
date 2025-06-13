<?php

namespace Unicorn\Service;

class Settings
{
    private $settings;

    private $loaded = false;

    public function save()
    {
        if (!file_exists(FileSystem::FILE_PATH . '.wpcbide.settings.php')) {
            touch(FileSystem::FILE_PATH . '.wpcbide.settings.php');
        }

        file_put_contents(FileSystem::FILE_PATH . '.wpcbide.settings.php', "<?php die() ; __halt_compiler(); ?>\n" . json_encode($this->settings, JSON_PRETTY_PRINT));

    }

    public function load()
    {
        return;
        if ($this->loaded) {
            return true;
        }

        if (!file_exists(FileSystem::FILE_PATH . '.wpcbide.settings.php')) {
            return [];
        }

        $settings = file_get_contents(FileSystem::FILE_PATH . '.wpcbide.settings.php');
        $settings = str_replace("<?php die() ; __halt_compiler(); ?>\n", "", $settings);

        $this->settings = json_decode($settings, true);

        $this->loaded = true;

        return true;

    }

    public function set($key, $value)
    {
        $this->load();
        $this->settings[$key] = $value;
    }

    public function get($key, $default)
    {
        $this->load();

        if (!isset($this->settings[$key])) {
            return $default;
        }

        return $this->settings[$key];
    }


}