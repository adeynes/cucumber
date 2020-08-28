<?php
declare(strict_types=1);

namespace adeynes\cucumber;

use pocketmine\utils\Config;

class ConfigMigrationManager
{

    public static function checkVersion(string $actual, string $minimum): bool
    {
        $actual = explode('.', $actual);
        $minimum = explode('.', $minimum);
        return $actual[0] === $minimum[0] && $actual[1] >= $minimum[1];
    }

    public static function configArrayFlatten(array $array, string $base): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $new_base = $base === '' ? $key : "$base.$key";
            $recurse = false;
            if (is_array($value) && count($value) > 0 && is_string($key)) {
                $recurse = true;
                foreach ($value as $sub_key => $sub_value) {
                    if (!is_string($sub_key)) {
                        $recurse = false;
                    }
                }
            }
            if ($recurse) {
                $result = array_merge($result, self::configArrayFlatten($value, $new_base));
            } else {
                $result[$new_base] = $value;
            }
        }
        return $result;
    }

    /** @var Cucumber */
    private $plugin;

    /** @var string */
    private $file;

    public function __construct(Cucumber $plugin, string $file)
    {
        if (!file_exists($plugin->getDataFolder() . $file)) {
            throw new \InvalidArgumentException('File $file doesn\'t exist!');
        }
        $this->plugin = $plugin;
        $this->file = $file;
    }

    public function tryMigration(string $minimum_version, ?string $archive_file): void
    {
        $full_file = $this->plugin->getDataFolder() . $this->file;
        $old_config = new Config($full_file);
        /** @var string $version */
        $version = $old_config->get('version', null);
        if (self::checkVersion($version, $minimum_version)) return;

        if ($archive_file !== null) {
            rename($full_file, $this->plugin->getDataFolder() . $archive_file);
        }

        $this->plugin->saveResource($this->file);
        $new_config = new Config($full_file);

        foreach (self::configArrayFlatten($new_config->getAll(), '') as $key => $value) {
            if ($key === 'version') {
                $new_config->set('version', $minimum_version);
                continue;
            }
            $old_value = $old_config->getNested($key);
            if ($old_value !== null) {
                $new_config->setNested($key, $old_value);
            }
        }

        $new_config->save();
    }

}