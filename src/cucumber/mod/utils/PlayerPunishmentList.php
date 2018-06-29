<?php

namespace cucumber\mod\utils;

use cucumber\mod\PlayerPunishment;
use cucumber\mod\Punishment;
use cucumber\utils\CPlayer;

class PlayerPunishmentList implements Punishment
{

    /** @var PlayerPunishment[] */
    protected $punishments;

    public function __construct(array $punishments = [])
    {
        $this->punishments = $punishments;
    }

    public function punish(PlayerPunishment $punishment): void
    {
        $this->punishments[$punishment->getPlayer()->getUid()] = $punishment;
    }

    public function pardon(PlayerPunishment $punishment): void
    {
        if (isset($this->punishments[$punishment->getPlayer()->getUid()]))
            unset($this->punishments[$punishment->getPlayer()->getUid()]);
    }

    public function isPunished(CPlayer $player): bool
    {
        return isset($this->punishments[$player->getUid()]) &&
            $this->punishments[$player->getUid()]->isPunished($player);
    }

}