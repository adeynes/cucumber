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
    }

    public function tryMigration(): void
    {
        if ($this->getPlugin()->getConfig()->get('migrated')) {
            $this->setMigrated(true);
            return;
        }

        // Check table integrity
        $tables = [
            'cucumber_players' => [
                'id', 'name', 'ip', 'first_join', 'last_join'
            ],
            'cucumber_bans' => [
                'id', 'player_id', 'reason', 'expiration', 'moderator', 'time_created'
            ],
            'cucumber_ip_bans' => [
                'id', 'ip', 'reason', 'expiration', 'moderator', 'time_created'
            ],
            'cucumber_ubans' => [
                'id', 'ip', 'reason', 'moderator', 'time_created'
            ],
            'cucumber_mutes' => [
                'id', 'player_id', 'reason', 'expiration', 'moderator', 'time_created'
            ]
        ];

        $check_integrity = function (array $rows) use ($tables) {
            $db_tables = [];
            foreach ($rows as $row) {
                $db_tables[] = reset($row);
            }

            foreach ($tables as $table => $columns) {
                if (!isset($db_tables[$table])) return false;

                $valid = true;
                $this->getPlugin()->getConnector()->executeSelect(
                    Queries::CUCUMBER_MIGRATE_GET_COLUMNS_FROM_TABLE,
                    ['table' => $table],
                    function (array $rows) use ($columns, &$valid) {
                        $db_rows = [];
                        foreach ($rows as $row) {
                            $db_rows[] = $row['Field'];
                        }

                        foreach ($columns as $column) {
                            $valid &= isset($db_rows[$column]);
                        }
                    }
                );
                $this->getPlugin()->getConnector()->waitAll(); // give the async fetch time to update $valid

                if (!$valid) return $valid;
            }

            return true;
        };

        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_MIGRATE_GET_TABLES,
            [],
            function (array $rows) use ($check_integrity) {
                if ($check_integrity($rows)) return;
                $this->migrate();
            }
        );

        $this->getPlugin()->getConnector()->waitAll();
    }

    private function migrate(): void {
        $queries = [
            'player' => [
                Queries::CUCUMBER_MIGRATE_TABLES_PLAYERS_RENAME
            ],
            'ban' => [
                Queries::CUCUMBER_MIGRATE_TABLES_BANS_RENAME,
                Queries::CUCUMBER_MIGRATE_TABLES_BANS_ALTER
            ],
            'ip-ban' => [
                Queries::CUCUMBER_MIGRATE_TABLES_IP_BANS_RENAME,
                Queries::CUCUMBER_MIGRATE_TABLES_IP_BANS_ALTER
            ],
            'uban' => [
                Queries::CUCUMBER_MIGRATE_TABLES_UBANS_RENAME,
                Queries::CUCUMBER_MIGRATE_TABLES_UBANS_ALTER
            ],
            'mute' => [
                Queries::CUCUMBER_MIGRATE_TABLES_MUTES_RENAME,
                Queries::CUCUMBER_MIGRATE_TABLES_MUTES_ALTER
            ]
        ];

        $connector = $this->getPlugin()->getConnector();

        foreach ($queries as $group) {
            foreach ($group as $query) {
                $connector->executeGeneric($query);
            }
            $connector->waitAll();
        }
    }

}