<?php

namespace cucumber\task;

class PunishmentSaveTask extends CTask
{

    public function onRun(int $tick): void
    {
        $this->plugin->log('Saving punishments...');
        $this->plugin->getPunishmentManager()->save();
    }

}