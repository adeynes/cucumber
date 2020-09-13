<?php
declare(strict_types=1);

namespace adeynes\cucumber;

use adeynes\cucumber\utils\Queries;

final class DbMigrationManager
{

    private const VERSION_1_TABLES = ['players', 'bans', 'ip_bans', 'ubans', 'mutes'];

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

    public function tryMigration(): void
    {
        if ($this->isMigrated()) return;

        $this->plugin->getLogger()->notice('cucumber\'s database has not been upgraded to support 2.0 on this system. Proceeding with the migration...');

        $this->migrate();
        $this->has_migrated = true;
    }

    private function migrate(): void {
        $connector = $this->plugin->getConnector();
        $transfer = false;
        $connector->executeSelect(
            Queries::CUCUMBER_MIGRATE_GET_TABLES,
            [],
            function (array $rows) use (&$transfer) {
                if (count($rows) === 0) {
                    $transfer = true;
                    return;
                }
                $column_name = array_keys($rows[0])[0];
                $tables = array_column($rows, $column_name);
                $transfer = count(array_intersect($tables, self::VERSION_1_TABLES)) === count(self::VERSION_1_TABLES);
            }
        );
        $connector->waitAll();

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
                $connector->executeGeneric($query);
                $connector->waitAll();
            }
        }

        $this->setMigrated(Cucumber::DB_VERSION);
    }

}