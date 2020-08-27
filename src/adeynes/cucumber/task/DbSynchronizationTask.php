<?php
declare(strict_types=1);

namespace adeynes\cucumber\task;

class DbSynchronizationTask extends CucumberTask
{

    public function onRun(int $tick): void
    {
        $this->getPlugin()->getLogger()->debug("Synchronizing punishments");
        $this->getPlugin()->getPunishmentRegistry()->load($this->getPlugin()->getConnector());
    }

}