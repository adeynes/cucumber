<?php
declare(strict_types=1);

namespace adeynes\cucumber;

use adeynes\cucumber\utils\Queries;

final class DbMigrationManager
{

    /** @var Cucumber */
    private $plugin;

    /** @var bool */
    private $is_migrated;

    /** @var bool */
    private $has_migrated = false;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
        $this->setMigrated($plugin->getConfig()->get('migrated'));
    }

    public function isMigrated(): bool
    {
        return $this->is_migrated;
    }

    private function setMigrated(bool $is_migrated): void
    {
        $this->is_migrated = $is_migrated;
        $this->plugin->getConfig()->set('migrated', true);
        $this->plugin->getConfig()->save();
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
        $queries = [
            'player' => [
                Queries::CUCUMBER_MIGRATE_TABLES_PLAYERS_RENAME,
                Queries::CUCUMBER_MIGRATE_TABLES_PLAYERS_ALTER_MODIFY
            ],
            'ban' => [
                Queries::CUCUMBER_MIGRATE_TABLES_BANS_RENAME,
                Queries::CUCUMBER_MIGRATE_TABLES_BANS_ALTER_CHANGE,
                Queries::CUCUMBER_MIGRATE_TABLES_BANS_ALTER_MODIFY
            ],
            'ip-ban' => [
                Queries::CUCUMBER_MIGRATE_TABLES_IP_BANS_RENAME,
                Queries::CUCUMBER_MIGRATE_TABLES_IP_BANS_ALTER_CHANGE,
                Queries::CUCUMBER_MIGRATE_TABLES_IP_BANS_ALTER_MODIFY
            ],
            'uban' => [
                Queries::CUCUMBER_MIGRATE_TABLES_UBANS_RENAME,
                Queries::CUCUMBER_MIGRATE_TABLES_UBANS_ALTER_CHANGE,
                Queries::CUCUMBER_MIGRATE_TABLES_UBANS_ALTER_MODIFY
            ],
            'mute' => [
                Queries::CUCUMBER_MIGRATE_TABLES_MUTES_RENAME,
                Queries::CUCUMBER_MIGRATE_TABLES_MUTES_ALTER_CHANGE,
                Queries::CUCUMBER_MIGRATE_TABLES_MUTES_ALTER_MODIFY
            ]
        ];

        $connector = $this->plugin->getConnector();

        foreach ($queries as $group => $group_queries) {
            $this->plugin->getLogger()->notice("Proceeding with $group migration...");
            foreach ($group_queries as $query) {
                $connector->executeGeneric($query);
                $connector->waitAll();
            }
        }

        $this->setMigrated(true);
    }

}