<?php
declare(strict_types=1);

namespace adeynes\cucumber;

use adeynes\cucumber\log\LogManager;
use adeynes\cucumber\log\LogSeverity;
use adeynes\cucumber\mod\PunishmentRegistry;
use adeynes\cucumber\task\ExpirationCheckTask;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\MessageFactory;
use adeynes\cucumber\utils\Queries;
use adeynes\parsecmd\parsecmd;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

final class Cucumber extends PluginBase
{

    private const CONFIG_VERSION = '2.2';

    private const MESSAGES_VERSION = '2.2';

    private const SUPPORTED_LANGUAGES = ['en' => 'en', 'fr' => 'fr'];

    /** @var Cucumber */
    private static $instance;

    /** @var Config */
    private $messages;

    /** @var DataConnector */
    private $connector;

    /** @var parsecmd */
    private $parsecmd;

    /** @var LogManager */
    private $log_manager;

    /** @var PunishmentRegistry */
    private $punishment_registry;

    /** @var MigrationManager */
    private $migration_manager;

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
        if ($this->connector) $this->getConnector()->close();
    }

    private function initConfigs(): void
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();

        /** @var string $config_version */
        $config_version = $this->getConfig()->get('version');
        if (!$this->checkVersion($config_version, self::CONFIG_VERSION)) {
            $this->fail('Outdated config.yml version! Try to delete cucumber/config.yml');
        }

        foreach (self::SUPPORTED_LANGUAGES as $language) {
            $this->saveResource("lang/$language.yml");
        }

        if (!isset(self::SUPPORTED_LANGUAGES[$language = $this->getConfig()->get('language')])) {
            $language_list = implode(', ', self::SUPPORTED_LANGUAGES);
            $this->fail("Unsupported language $language! Please pick one of the following: $language_list");
        }

        $this->messages = new Config("{$this->getDataFolder()}lang/$language.yml");

        $messages_version = $this->getMessage('version');
        if (!$this->checkVersion($messages_version, self::MESSAGES_VERSION)) {
            $this->fail("Outdated $language.yml version Try to delete cucumber/$language.yml");
        }
    }

    private function initDatabase(): void
    {
        $this->connector = $connector = libasynql::create(
            $this,
            $this->getConfig()->get('database'),
            ['mysql' => 'mysql.sql']
        );

        $this->migration_manager = new MigrationManager($this);
        $this->getMigrationManager()->tryMigration();

        // other tables have a foreign key constraint on players so it must be first
        $connector->executeGeneric(Queries::CUCUMBER_INIT_PLAYERS);
        $connector->waitAll();

        $connector->executeGeneric(Queries::CUCUMBER_ADD_PLAYER, ['name' => 'CONSOLE', 'ip' => '127.0.0.1']);

        $create_queries = [
            Queries::CUCUMBER_INIT_PUNISHMENTS_BANS,
            Queries::CUCUMBER_INIT_PUNISHMENTS_IP_BANS,
            Queries::CUCUMBER_INIT_PUNISHMENTS_UBANS,
            Queries::CUCUMBER_INIT_PUNISHMENTS_MUTES
        ];
        foreach ($create_queries as $query) $connector->executeGeneric($query);

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
        // Loggers are defined in the config as [severity => [fqn, [constructor args]]]
        // LogManager instance is always the first arg, user-supplied ones are passed starting with the second arg
        foreach ($this->getConfig()->getNested('log.loggers') as $severity => $loggers) {
            try {
                $severity = LogSeverity::fromString($severity);
            } catch (CucumberException $exception) {
                $this->log(
                    MessageFactory::colorize("&eUnknown logger severity &b$severity&e, defaulting to &blog")
                );
                $severity = LogSeverity::LOG();
            }

            foreach ($loggers as $logger) {
                $this->getLogManager()->pushLogger(
                    new $logger[0]($this->getLogManager(), ...($logger[1] ?? [])),
                    $severity
                );
            }
        }
    }

    /**
     * Instantiate PunishmentRegistry & load punishments
     * @return void
     */
    private function initMod(): void
    {
        $this->punishment_registry = new PunishmentRegistry($this);
        // Check for expired punishments every 5 mins
        $this->getScheduler()->scheduleRepeatingTask(new ExpirationCheckTask($this), 300 * 20);
    }

    private function initEvents(): void
    {
        $events = [
            'join' => ['join', 'JoinEvent'],
            'join-attempt' => ['join attempt', 'JoinAttemptEvent'],
            'quit' => ['quit', 'QuitEvent'],
            'chat' => ['chat', 'ChatEvent'],
            'chat-attempt' => ['chat attempt', 'ChatAttemptEvent'],
            'command' => ['command', 'CommandEvent']
        ];

        foreach ($events as $type => $class) {
            try {

                $severity = $this->getConfig()->getNested("log.severities.$type", 'log');
                $severity = LogSeverity::fromString($severity);
            } catch (CucumberException $exception) {
                /** @noinspection PhpUndefinedVariableInspection */
                $this->log(
                    MessageFactory::colorize("&eUnknown logger severity &b$severity&e for event &b$type&e, defaulting to &blog")
                );
                $severity = LogSeverity::LOG();
            }

            call_user_func(
                ["\\adeynes\\cucumber\\event\\$class[1]", 'init'],
                $class[0],
                $this->getMessage("log.templates.$type"),
                $severity
            );
        }
    }

    private function registerCommands(): void
    {
        $this->saveResource('commands.json');
        $commands = new Config($this->getDataFolder() . 'commands.json', CONFIG::JSON);
        $this->parsecmd = parsecmd::new($this, $commands->getAll(), true);
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

    public function getParsecmd(): parsecmd
    {
        return $this->parsecmd;
    }

    public function getLogManager(): LogManager
    {
        return $this->log_manager;
    }

    public function getPunishmentRegistry(): PunishmentRegistry
    {
        return $this->punishment_registry;
    }

    private function getMigrationManager(): MigrationManager
    {
        return $this->migration_manager;
    }

    public function isMigrated(): bool
    {
        return $this->getMigrationManager()->isMigrated();
    }

    public function checkVersion(string $actual, string $minimum): bool
    {
        $actual = explode('.', $actual);
        $minimum = explode('.', $minimum);
        return $actual[0] === $minimum[0] && $actual[1] >= $minimum[1];
    }

    public function formatMessageFromConfig(string $path, array $data): string
    {
        return MessageFactory::fullFormat($this->getMessage($path), $data);
    }

    public function formatAndSend(CommandSender $sender, string $path, array $data = []): void
    {
        $sender->sendMessage($this->formatMessageFromConfig($path, $data));
    }

    /**
     * Logs the supplied message at the supplied severity level in the server's logger
     * @param string $message
     * @param string $severity The severity of the message (defined in SPL/LogLevel), default info
     * @return void
     */
    public function log(string $message, string $severity = 'info'): void
    {
        // TODO: use Logger::log()
        $this->getServer()->getLogger()->log($severity, $message);
    }

    /**
     * Logs the supplied message and disables the plugin
     * @param string $message
     * @param string $severity The severity of the message (defined in SPL/LogLevel), default alert
     * @return void
     * @throws \Exception To disable the plugin
     */
    public function fail(string $message, string $severity = 'alert'): void
    {
        $this->log($message, $severity);
        throw new \Exception($message);
    }

    public function cancelTask(int $id)
    {
        $this->getScheduler()->cancelTask($id);
    }

}