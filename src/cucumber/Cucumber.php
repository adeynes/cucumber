<?php

namespace cucumber;

use cucumber\log\LogManager;
use cucumber\mod\PunishmentManager;
use cucumber\provider\Provider;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

final class Cucumber extends PluginBase
{

    /** @var Cucumber */
    private static $instance;

    /** @var Config */
    public $messages;

    /** @var Provider */
    private $provider;

    /** @var LogManager */
    private $log_manager;

    /** @var PunishmentManager */
    private $punishment_manager;

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
        $this->initProvider();
        $this->initLog();
        $this->initMod();
        $this->initEvents();
        $this->registerCommands();

        $this->getServer()->getPluginManager()->registerEvents(new CListener($this), $this);
    }

    public function onDisable()
    {
        $this->getPunishmentManager()->save();

        // Last
        $this->getProvider()->close();
    }

    private function initConfigs(): void
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->messages = new Config($this->getDataFolder() . 'messages.yml');
    }

    private function initProvider(): void
    {
        $this->provider = new Provider($this);
    }

    /**
     * Instantiate LogManager & push loggers defined
     * under log.loggers to the logger stack
     * @return void
     */
    private function initLog(): void
    {
        $this->log_manager = new LogManager($this);
        // Loggers are defined in the config as
        // fully-qualified class name => constructor args
        // Cucumber instance is always the first arg,
        // user-supplied ones are passed starting with the second arg
        foreach ($this->getConfig()->getNested('log.loggers') as $logger => $args)
        {
            $args = [$this] + $args;
            $this->getLogManager()->addLogger(new $logger(...$args));
        }
    }

    /**
     * Instantiate PunishmentManager & load punishments
     * @return void
     */
    private function initMod(): void
    {
        $this->punishment_manager = new PunishmentManager($this);
    }

    private function initEvents(): void
    {
        $events = [
            'join' => ['join', 'JoinEvent'],
            'join-attempt' => ['join attempt', 'JoinAttemptEvent'],
            'quit' => ['quit', 'QuitEvent'],
            'chat' => ['chat', 'ChatEvent'],
            'chat-attempt' => ['chat attempt', 'ChatAttemptEvent'],
            'command' => ['command', 'CommandEvent'],
            'moderation' => ['moderation', 'ModerationEvent']
        ];

        foreach ($events as $type => $class)
            call_user_func(
                ['\\cucumber\\event\\' . $class[1], 'init'],
                $class[0],
                $this->messages->getNested('log.templates.' . $type)
            );
    }

    private function registerCommands(): void
    {
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
            // Unregisters the old command if it is a duplicate name
            if (!is_null($old = $map->getCommand($command))) {
                $old->setLabel($command . '_disabled');
                $old->unregister($map);
            }
            $class = '\\cucumber\\command\\' . $class;
            $map->register('cucumber', new $class($this));
        }
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getLogManager(): LogManager
    {
        return $this->log_manager;
    }

    public function getPunishmentManager(): PunishmentManager
    {
        return $this->punishment_manager;
    }

    /**
     * Logs the supplied message at the supplied
     * severity level in the server's logger
     * @param string $message
     * @param string $level The severity of the message (defined in SPL/LogLevel), default info
     * @return void
     */
    public function log(string $message, string $level = 'info'): void
    {
        $this->getServer()->getLogger()->{$level}($message);
    }

    public function cancelTask(int $id)
    {
        $this->getScheduler()->cancelTask($id);
    }

}