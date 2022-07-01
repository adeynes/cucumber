<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use pocketmine\player\Player;
use poggit\libasynql\DataConnector;

class UBanChecker
{

    /** @var PunishmentRegistry */
    protected PunishmentRegistry $punishment_registry;

    /** @var DataConnector */
    protected DataConnector $connector;

    public function __construct(PunishmentRegistry $punishment_registry, DataConnector $connector)
    {
        $this->punishment_registry = $punishment_registry;
        $this->connector = $connector;
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Check if a player is affected by a uban
     * @param Player $player
     * @param bool $do_ban Ban the player if their ip is ubanned?
     * @return bool
     */
    public function checkFor(Player $player, bool $do_ban = true): bool
    {
        $uban = $this->punishment_registry->getUBan($player->getNetworkSession()->getIp());
        if ($uban instanceof UBan) {
            if ($do_ban) {
                $ban = new Ban(strtolower($player->getName()), $uban->getReason(), null, $uban->getModerator(), $uban->getTimeOfCreation());
                /** @noinspection PhpUnhandledExceptionInspection */
                $this->punishment_registry->addBan($ban, true);
                $ban->save($this->connector);
            }
            return true;
        }

        return false;
    }

}