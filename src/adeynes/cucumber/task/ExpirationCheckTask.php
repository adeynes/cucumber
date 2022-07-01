<?php
declare(strict_types=1);

namespace adeynes\cucumber\task;

use adeynes\cucumber\mod\PunishmentRegistry;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\cucumber\utils\MessageFactory;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

class ExpirationCheckTask extends Task
{

    /** @var PunishmentRegistry */
    protected PunishmentRegistry $punishment_registry;

    /** @var Config */
    protected Config $message_config;

    public function __construct(PunishmentRegistry $punishment_registry, Config $message_config)
    {
        $this->punishment_registry = $punishment_registry;
        $this->message_config = $message_config;
    }

    public function onRun(): void
    {
        foreach ($this->punishment_registry->getBans() as $name => $ban) {
            if ($ban->isExpired()) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $this->punishment_registry->removeBan($name);
            }
        }

        foreach ($this->punishment_registry->getIpBans() as $ip => $ip_ban) {
            if ($ip_ban->isExpired()) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $this->punishment_registry->removeIpBan($ip);
            }
        }

        foreach ($this->punishment_registry->getMutes() as $name => $mute) {
            if ($mute->isExpired()) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $this->punishment_registry->removeMute($name);
                if ($player = CucumberPlayer::getOnlinePlayer($name)) {
                    $player->sendMessage(MessageFactory::colorize(
                        $this->message_config->getNested('moderation.mute.unmute.auto')
                    ));
                }
            }
        }
    }

}