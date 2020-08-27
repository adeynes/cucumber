<?php
declare(strict_types=1);

namespace adeynes\cucumber\task;

use adeynes\cucumber\utils\CucumberPlayer;

class ExpirationCheckTask extends CucumberTask
{

    public function onRun(int $tick): void
    {
        $punishment_registry = $this->getPlugin()->getPunishmentRegistry();

        foreach ($punishment_registry->getBans() as $name => $ban) {
            if ($ban->isExpired()) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $punishment_registry->removeBan($name);
            }
        }

        foreach ($punishment_registry->getIpBans() as $ip => $ip_ban) {
            if ($ip_ban->isExpired()) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $punishment_registry->removeIpBan($ip);
            }
        }

        foreach ($punishment_registry->getMutes() as $name => $mute) {
            if ($mute->isExpired()) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $punishment_registry->removeMute($name);
                if ($player = CucumberPlayer::getOnlinePlayer($name)) {
                    $this->getPlugin()->formatAndSend($player, 'moderation.mute.unmute.auto');
                }
            }
        }
    }

}