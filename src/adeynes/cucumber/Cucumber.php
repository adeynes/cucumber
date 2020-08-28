<?php
declare(strict_types=1);

namespace adeynes\cucumber;

use adeynes\cucumber\log\LogDispatcher;
use adeynes\cucumber\log\LogSeverity;
use adeynes\cucumber\mod\PunishmentRegistry;
use adeynes\cucumber\task\DbSynchronizationTask;
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

    private const CONFIG_VERSION = '3.0';

    private const MESSAGES_VERSION = '3.0';

    private const SUPPORTED_LANGUAGES = ['en' => 'en', 'fr' => 'fr'];

    /** @var Cucumber */
    private static $instance;

    /** @var Config We need to override PocketMine's config: it is private and we can't have it run saveDefaultConfig() */
    private $config_;

    /** @var Config */
    private $messages;

    /** @var DataConnector */
    private $connector;

    /** @var parsecmd */
    private $parsecmd;

    /** @var LogDispatcher */
    private $log_dispatcher;

    /** @var PunishmentRegistry */
    private $punishment_registry;

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

        if ($this->isDisabled()) return;
        $this->getServer()->getPluginManager()->registerEvents(new CucumberListener($this), $this);
    }

    public function onDisable(): void
    {
        $this->getLogDispatcher()->onDisable();
        $this->getConnector()->close();
    }

    private function initConfigs(): void
    {
        if ($this->isDisabled()) return;

        @mkdir($this->getDataFolder());

        $emit_version_edit_warning = false;

        try {
            $config_migration_manager = new ConfigMigrationManager($this, 'config.yml');
            $config_migration_manager->tryMigration(self::CONFIG_VERSION, 'old_config.yml');
            $emit_version_edit_warning |= $config_migration_manager->hasMigrated();
        } catch (\InvalidArgumentException $e) {
            $this->saveResource('config.yml');
            $emit_version_edit_warning |= true;
        }
        $this->config_ = new Config($this->getDataFolder() . 'config.yml');

        foreach (self::SUPPORTED_LANGUAGES as $language) {
            try {
                $language_migration_manager = new ConfigMigrationManager($this, "lang/$language.yml");
                $language_migration_manager->tryMigration(self::MESSAGES_VERSION, "lang/old_$language.yml");
                $emit_version_edit_warning |= $language_migration_manager->hasMigrated();
            } catch (\InvalidArgumentException $e) {
                $this->saveResource("lang/$language.yml");
                $emit_version_edit_warning |= true;
            }
        }

        if (!isset(self::SUPPORTED_LANGUAGES[$language = $this->getConfig()->get('language')])) {
            $language_list = implode(', ', self::SUPPORTED_LANGUAGES);
            $this->fail("Unsupported language $language! Please pick one of the following: $language_list");
            return;
        }

        $this->messages = new Config("{$this->getDataFolder()}lang/$language.yml");

        if ($emit_version_edit_warning) {
            $this->emitVersionEditWarning();
        }
    }

    public function getConfig(): Config
    {
        return $this->config_;
    }

    private function initDatabase(): void
    {
        if ($this->isDisabled()) return;

        try {
            $this->connector = $connector = libasynql::create(
                $this,
                $this->getConfig()->get('database'),
                ['mysql' => 'mysql.sql']
            );
        } catch (\Exception|\Error $e) {
            $this->fail($e->getMessage());
            return;
        }

        $db_migration_manager = new DbMigrationManager($this);
        $db_migration_manager->tryMigration();
        if ($db_migration_manager->hasMigrated()) {
            $this->emitMigratedEditWarning();
        }

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
     * Instantiate LogDispatcher & push loggers defined
     * under log.loggers to the logger stack
     * @return void
     */
    private function initLog(): void
    {
        if ($this->isDisabled()) return;

        $this->log_dispatcher = new LogDispatcher($this);
        // Loggers are defined in the config as [severity => [fqn, [constructor args]]]
        // LogDispatcher instance is always the first arg, user-supplied ones are passed starting with the second arg
        foreach ($this->getConfig()->getNested('log.loggers') as $severity => $loggers) {
            try {
                $severity = LogSeverity::fromString($severity);
            } catch (CucumberException $exception) {
                $this->getLogger()->warning(
                    MessageFactory::colorize("&eUnknown logger severity &b$severity&e, defaulting to &blog")
                );
                $severity = LogSeverity::LOG();
            }

            foreach ($loggers as $logger) {
                $this->getLogDispatcher()->pushLogger(
                    new $logger[0]($this->getLogDispatcher(), ...($logger[1] ?? [])),
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
        if ($this->isDisabled()) return;

        $this->punishment_registry = new PunishmentRegistry($this->getMessageConfig(), $this->getConnector());
        // Check for expired punishments every 5 mins
        $this->getScheduler()->scheduleRepeatingTask(
            new ExpirationCheckTask($this->getPunishmentRegistry(), $this->getMessageConfig()),
            $this->getConfig()->getNested('task.expiration-task-period') * 20
        );
        $this->getScheduler()->scheduleRepeatingTask(
            new DbSynchronizationTask($this->getPunishmentRegistry(), $this->getConnector()),
            $this->getConfig()->getNested('task.db-sync-task-period') * 20
        );
    }

    private function initEvents(): void
    {
        if ($this->isDisabled()) return;

        $events = [
            'join' => ['join', 'JoinEvent'],
            'join-attempt' => ['join attempt', 'JoinAttemptEvent'],
            'quit' => ['quit', 'QuitEvent'],
            'chat' => ['chat', 'ChatEvent'],
            'chat-attempt' => ['chat attempt', 'ChatAttemptEvent'],
            'command' => ['command', 'CommandEvent']
        ];

        foreach ($events as $type => $class) {
            $severity_str = $this->getConfig()->getNested("log.severities.$type", 'log');
            try {
                $severity = LogSeverity::fromString($severity_str);
            } catch (CucumberException $exception) {
                /** @noinspection PhpUndefinedVariableInspection */
                $this->getLogger()->warning(
                    MessageFactory::colorize("&eUnknown logger severity &b$severity_str&e for event &b$type&e, defaulting to &blog")
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
        if ($this->isDisabled()) return;

        $this->saveResource('commands.json', true);
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

    public function getLogDispatcher(): LogDispatcher
    {
        return $this->log_dispatcher;
    }

    public function getPunishmentRegistry(): PunishmentRegistry
    {
        return $this->punishment_registry;
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
     * Logs the supplied message and disables the plugin
     * @param string $message
     * @param string $severity The severity of the message (defined in SPL/LogLevel), default alert
     * @return void
     */
    public function fail(string $message, string $severity = 'alert'): void
    {
        $this->getLogger()->log($severity, $message);
        $this->getServer()->getPluginManager()->disablePlugin($this);
    }

    public function emitVersionEditWarning(): void
    {
        $this->getLogger()->warning('Do not edit the "version" attribute in ANY of the config or lang files');
    }

    public function emitMigratedEditWarning(): void
    {
        $this->getLogger()->warning('Do not edit the "migrated" attribute in config.yml');
    }

    public function cancelTask(int $id)
    {
        $this->getScheduler()->cancelTask($id);
    }

}