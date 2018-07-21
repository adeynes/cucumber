<?php
declare(strict_types=1);

namespace cucumber;

use cucumber\log\LogManager;
use cucumber\mod\PunishmentManager;
use cucumber\task\PunishmentSaveTask;
use cucumber\utils\Queries;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

final class Cucumber extends PluginBase
{

    /** @var Cucumber */
    private static $instance;

    /** @var Config */
    private $messages;

    /** @var DataConnector */
    private $connector;

    /** @var LogManager */
    private $log_manager;

    /** @var PunishmentManager */
    private $punishment_manager;

    public static function getInstance(): self
    {
        return self::$instance;
    }

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        $this->initConfigs();
        $this->initDatabase();
        $this->initLog();
        $this->initMod();
        $this->initEvents();
        $this->registerCommands();

        $this->getServer()->getPluginManager()->registerEvents(new CucumberListener($this), $this);
    }

    public function onDisable(): void
    {
        $this->getPunishmentManager()->save();

        $this->getConnector()->close(); // last
    }

    private function initConfigs(): void
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->messages = new Config($this->getDataFolder() . 'messages.yml');
    }

    private function initDatabase(): void
    {
        $this->connector = libasynql::create($this, $this->getConfig()->get('database'),
            ['mysql' => 'mysql.sql']);
        $connector = $this->getConnector();
        $queries = [Queries::CUCUMBER_INIT_PLAYERS, Queries::CUCUMBER_INIT_PUNISHMENTS_BANS,
            Queries::CUCUMBER_INIT_PUNISHMENTS_IP_BANS, Queries::CUCUMBER_GET_PUNISHMENTS_MUTES];

        foreach ($queries as $query)
            $connector->executeGeneric($query);

        // stop execution until init is completed
        $connector->waitAll();
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
        // Save punishments every hour
        $this->getScheduler()->scheduleRepeatingTask(new PunishmentSaveTask($this), 3600 * 20);
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
                ["\\cucumber\\event\\$class[1]", 'init'],
                $class[0],
                $this->getMessage("log.templates.$type")
            );
    }

    private function registerCommands(): void
    {
        $map = $this->getServer()->getCommandMap();

        $commands = [
            'rawtell' => 'RawtellCommand',
            'log' => 'LogCommand',
            'alert' => 'AlertCommand',
            'ban' => 'BanCommand',
            'banlist' => 'BanlistCommand',
            'pardon' => 'PardonCommand',
            'ipban' => 'IpbanCommand',
            'ipbanlist' => 'IpbanlistCommand',
            'ippardon' => 'IppardonCommand',
            'uban' => 'UbanCommand',
            'ubanlist' => 'UbanlistCommand',
            'mute' => 'MuteCommand',
            'mutelist' => 'MutelistCommand',
            'unmute' => 'UnmuteCommand'
        ];

        foreach ($commands as $command => $class){
            // Unregisters the old command if it is a duplicate name
            if ($old = $map->getCommand($command)) {
                $old->setLabel($command . '_disabled');
                $old->unregister($map);
            }
            $class = "\\cucumber\\command\\$class";
            $map->register('cucumber', new $class($this));
        }
    }

    public function getMessageConfig(): Config
    {
        return $this->messages;
    }

    public function getMessage(string $path): ?string
    {
        return $this->getMessageConfig()->getNested($path);
    }

    public function getConnector(): DataConnector
    {
        return $this->connector;
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