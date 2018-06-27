<?php

namespace cucumber;

use cucumber\log\LogManager;
use cucumber\utils\MessageFactory;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

final class Cucumber extends PluginBase
{

    /**
     * @var Cucumber
     */
    private static $instance;
    /**
     * @var Config
     */
    public $messages;
    /**
     * @var LogManager
     */
    private $log_manager;
    /**
     * @var MessageFactory
     */
    private $message_factory;
    private $log_dir;

    public static function getInstance(): self
    {
        return self::$instance;
    }

    public function onLoad()
    {
        self::$instance = $this;
    }

    public function onEnable()
    {
        $this->initConfigs();
        $this->initLog();
        $this->registerCommands();

        $this->getServer()->getPluginManager()->registerEvents(new CListener($this), $this);
    }

    private function initConfigs()
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->messages = new Config($this->getDataFolder() . 'messages.yml');
    }

    private function initLog()
    {
        $this->log_manager = new LogManager($this);
        foreach ($this->getConfig()->getNested('log.loggers') as $logger => $args)
        {
            $args = [$this] + $args;
            $this->getLogManager()->addLogger(new $logger(...$args));
        }
        $this->message_factory = new MessageFactory($this);
    }

    private function registerCommands(){
        $map = $this->getServer()->getCommandMap();

        $commands = [
            'rawtell' => 'RawtellCommand',
            'log' => 'LogCommand',
            'alert' => 'AlertCommand',
            'kick' => 'KickCommand',
            'mute' => 'MuteCommand',
            'ban' => 'BanCommand',
            'uban' => 'UbanCommand'
        ];

        foreach ($commands as $command => $class){
            if (!is_null($old = $map->getCommand($command))) {
                $old->setLabel($command . '_disabled');
                $old->unregister($map);
            }
            $class = '\\cucumber\\command\\' . $class;
            $map->register('cucumber', new $class($this));
        }
    }

    public function getLogManager(): LogManager
    {
        return $this->log_manager;
    }

    public function getMessageFactory(): MessageFactory
    {
        return $this->message_factory;
    }

    public function fail(string $message, string $level = 'critical'): bool
    {
        $this->log($message, $level);
        return false;
    }

    public function log(string $message, string $level = 'info'): bool
    {
        $this->getServer()->getLogger()->{$level}($message);
        return true;
    }

}