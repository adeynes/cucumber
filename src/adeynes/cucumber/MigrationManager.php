<?php
declare(strict_types=1);

namespace adeynes\cucumber;

use adeynes\cucumber\utils\Queries;

final class MigrationManager
{

    /** @var Cucumber */
    private $plugin;

    /** @var bool */
    private $is_migrated;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
        $this->setMigrated($this->getPlugin()->getConfig()->get('migrated'));
    }

    public function getPlugin(): Cucumber
    {
        return $this->plugin;
    }

    public function isMigrated(): bool
    {
        return $this->is_migrated;
    }

    private function setMigrated(bool $is_migrated): void
    {
        $this->is_migrated = $is_migrated;
        $this->getPlugin()->getConfig()->set('migrated', true);
        $this->getPlugin()->getConfig()->save();
    }

    public function tryMigration(): void
    {
        if ($this->isMigrated()) return;

        $this->getPlugin()->log(
            'cucumber\'s database has not been upgraded to support 2.0 on this system. Proceeding with the migration...',
            'notice'
        );

        $this->migrate();
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

        $connector = $this->getPlugin()->getConnector();

        foreach ($queries as $group => $group_queries) {
            $this->getPlugin()->log("Proceeding with $group migration...", 'notice');
            foreach ($group_queries as $query) {
                $connector->executeGeneric($query);
                $connector->waitAll();
            }
        }

        $this->setMigrated(true);
    }

}