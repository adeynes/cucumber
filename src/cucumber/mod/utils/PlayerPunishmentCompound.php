<?php

namespace cucumber\mod\utils;

use cucumber\mod\PlayerPunishment;
use cucumber\utils\CPlayer;

class PlayerPunishmentCompound
{

    /** @var PlayerPunishment[] */
    protected $name = [];
    /** @var PlayerPunishment[] */
    protected $uid = [];

    /**
     * @param PlayerPunishment[] $punishments
     */
    public function __construct(array $punishments = [])
    {
        foreach ($punishments as $punishment)
            $this->addPunishment($punishment);
    }

    /**
     * Get a player's punishment, or null if they aren't punished
     * @param CPlayer $player
     * @return PlayerPunishment|null
     */
    public function getPunishment(CPlayer $player): ?PlayerPunishment
    {
        // UID has higher priority as it never changes

        // Could be a lot more compact but I'd rather it be simple
        if (isset($this->uid[$player->getUid()])) {
            $punishment = $this->uid[$player->getUid()];
            if ($punishment->isPunished($player)) return $punishment;
        }

        if (isset($this->name[$player->getName()])) {
            $punishment = $this->name[$player->getName()];
            if ($punishment->isPunished($player)) return $punishment;
        }

        return null;
    }

    public function addPunishment(PlayerPunishment $punishment): void
    {
        $this->name[$punishment->getPlayer()->getName()] = $punishment;
        $this->uid[$punishment->getPlayer()->getUid()] = $punishment;
    }

}