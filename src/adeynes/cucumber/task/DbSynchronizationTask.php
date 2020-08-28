<?php
declare(strict_types=1);

namespace adeynes\cucumber\task;

use adeynes\cucumber\mod\PunishmentRegistry;
use pocketmine\scheduler\Task;
use poggit\libasynql\DataConnector;

class DbSynchronizationTask extends Task
{

    /** @var PunishmentRegistry */
    protected $punishment_registry;

    /** @var DataConnector */
    protected $connector;

    public function __construct(PunishmentRegistry $punishment_registry, DataConnector $connector)
    {
        $this->punishment_registry = $punishment_registry;
        $this->connector = $connector;
    }

    public function onRun(int $tick): void
    {
        $this->punishment_registry->load($this->connector);
    }

}