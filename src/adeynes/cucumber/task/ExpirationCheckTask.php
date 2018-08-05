<?php
declare(strict_types=1);

namespace adeynes\cucumber\task;

use adeynes\cucumber\utils\CucumberPlayer;

class ExpirationCheckTask extends CucumberTask
{

    public function onRun(int $tick): void
    {
        $punishment_manager = $this->getPlugin()->getPunishmentManager();

        // It's nasty & hacky if I have an array of getters and iterate over it
        foreach ($punishment_manager->getBans() as $name => $ban) {
            if ($ban->isExpired()) {
                $punishment_manager->unban($name);
            }
        }

        foreach ($punishment_manager->getIpBans() as $ip => $ip_ban) {
            if ($ip_ban->isExpired()) {
                $punishment_manager->ipUnban($ip);
            }
        }

        foreach ($punishment_manager->getMutes() as $name => $mute) {
            if ($mute->isExpired()) {
                $punishment_manager->unmute($name);
                if ($player = CucumberPlayer::getOnlinePlayer($name)) {
                    $this->getPlugin()->formatAndSend($player, 'moderation.mute.unmute.auto');
                }
            }
        }
    }

}