<?php
declare(strict_types=1);

namespace adeynes\cucumber\task;

class PunishmentSaveTask extends CucumberTask
{

    public function onRun(int $tick): void
    {
        $this->getPlugin()->log('Saving punishments...');
        $this->getPlugin()->getPunishmentManager()->save();
    }

}