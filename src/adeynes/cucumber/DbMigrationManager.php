<?php
declare(strict_types=1);

namespace adeynes\cucumber;

use adeynes\cucumber\utils\Queries;
use Error;

final class DbMigrationManager
{

    private const VERSION_1_TABLES = ['players', 'bans', 'ip_bans', 'ubans', 'mutes'];

    private const VERSION_2_TABLES = [
        'cucumber_meta',
        'cucumber_players',
        'cucumber_bans',
        'cucumber_ip_bans',
        'cucumber_ubans',
        'cucumber_mutes',
        'cucumber_warnings'
    ];

    /** @var Cucumber */
    private $plugin;

    /** @var bool */
    private $has_migrated = false;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
    }

    public function isMigrated(): bool
    {
        $is_migrated = false;
        $this->plugin->getConnector()->executeSelect(
            Queries::CUCUMBER_META_GET_VERSION,
            [],
            function (array $rows) use (&$is_migrated) {
                if (count($rows) === 0) {
                    $is_migrated = false;
                    return;
                }
                $is_migrated = $rows[0]['db_version'] === Cucumber::DB_VERSION;
            }
        );
        $this->plugin->getConnector()->waitAll();
        return $is_migrated;
    }

    private function setMigrated(int $new_version): void
    {
        $this->plugin->getConnector()->executeInsert(
            Queries::CUCUMBER_META_SET_VERSION,
            ['version' => $new_version]
        );
    }

    public function hasMigrated(): bool
    {
        return $this->has_migrated;
    }

    private function hasTables(array $tables): bool
    {
        $has_tables = false;
        $connector = $this->plugin->getConnector();
        $connector->executeSelect(
            Queries::CUCUMBER_MIGRATE_GET_TABLES,
            [],
            function (array $rows) use ($tables, &$has_tables) {
                if (count($rows) === 0) {
                    $has_tables = true;
                    return;
                }
                $column_name = array_keys($rows[0])[0];
                $current_tables = array_column($rows, $column_name);
                $has_tables = count(array_intersect($current_tables, $tables)) === count($tables);
            }
        );
        $connector->waitAll();
        return $has_tables;
    }

    private function hasV1Tables(): bool
    {
        return $this->hasTables(self::VERSION_1_TABLES);
    }

    private function hasV2Tables(): bool
    {
        return $this->hasTables(self::VERSION_2_TABLES);
    }

    /**
     * @throws Error
     */
    public function tryMigration(): void
    {
        if ($this->isMigrated()) return;

        $this->plugin->getLogger()->notice('cucumber\'s database has not been upgraded to support 2.0 on this system. Proceeding with the migration...');

        $this->migrate();
        $this->has_migrated = true;
    }

    private function migrate(): void {
        $connector = $this->plugin->getConnector();
        $transfer = $this->hasV1Tables();

        $queries = [
            'player' => [
                Queries::CUCUMBER_INIT_PLAYERS => true,
                Queries::CUCUMBER_MIGRATE_TRANSFER_PLAYERS => $transfer
            ],
            'ban' => [
                Queries::CUCUMBER_INIT_PUNISHMENTS_BANS => true,
                Queries::CUCUMBER_MIGRATE_TRANSFER_BANS => $transfer
            ],
            'ip-ban' => [
                Queries::CUCUMBER_INIT_PUNISHMENTS_IP_BANS => true,
                Queries::CUCUMBER_MIGRATE_TRANSFER_IP_BANS => $transfer
            ],
            'uban' => [
                Queries::CUCUMBER_INIT_PUNISHMENTS_UBANS => true,
                Queries::CUCUMBER_MIGRATE_TRANSFER_UBANS => $transfer
            ],
            'mute' => [
                Queries::CUCUMBER_INIT_PUNISHMENTS_MUTES => true,
                Queries::CUCUMBER_MIGRATE_TRANSFER_MUTES => $transfer
            ],
            'warnings' => [
                Queries::CUCUMBER_INIT_PUNISHMENTS_WARNINGS => true
            ]
        ];

        foreach ($queries as $group => $group_queries) {
            $this->plugin->getLogger()->notice("Proceeding with $group migration...");
            foreach ($group_queries as $query => $do) {
                if (!$do) continue;
                $error = null;
                $connector->executeGeneric($query);
            }
        }

        if (!$this->hasV2Tables()) {
            throw new Error('All the tables could not be correctly built');
        }

        $this->setMigrated(Cucumber::DB_VERSION);
    }

}