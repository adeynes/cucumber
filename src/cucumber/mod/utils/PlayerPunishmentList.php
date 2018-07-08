<?php

namespace cucumber\mod\utils;

use cucumber\mod\PlayerPunishment;
use cucumber\utils\CException;
use cucumber\utils\CPlayer;
use cucumber\utils\ErrorCodes;

abstract class PlayerPunishmentList extends PunishmentList
{

    /** @var PlayerPunishment[] */
    protected $punishments = [];

    /**
     * @param PlayerPunishment[] $punishments
     * @throws CException If one of the punishments exists twice
     */
    public function __construct(array $punishments = [])
    {
        self::initMessages();
        foreach ($punishments as $punishment)
            $this->punish($punishment);
    }

    /**
     * @param PlayerPunishment $punishment
     * @param bool $repunish Repunish the player is they already are (does not throw exception)
     * @throws CException If the player is already punished
     */

    public function punish(PlayerPunishment $punishment, bool $repunish = false): void
    {
        $player = $punishment->getPlayer();
        $uid = $player->getUid();
        if (isset($this->punishments[$uid]) && !$repunish)
            throw new CException(
                self::$messages['already-punished'],
                ['name' => $player->getName()],
                ErrorCodes::ATTEMPT_PUNISH_PUNISHED
            );

        $this->punishments[$uid] = $punishment;
    }

    /**
     * @param string $uid
     * @throws CException If the player isn't punished
     */
    public function pardon(string $uid): void
    {
        if (!isset($this->punishments[$uid]))
            throw new CException(
                self::$messages['not-punished'],
                ['uid' => $uid],
                ErrorCodes::ATTEMPT_PARDON_NOT_PUNISHED
            );

        unset($this->punishments[$uid]);
    }

    public function isPunished(CPlayer $player): bool
    {
        $uid = $player->getUid();
        return isset($this->punishments[$uid]) &&
            $this->punishments[$uid]->isPunished($player);
    }

    public function get(CPlayer $player): ?PlayerPunishment
    {
        return $this->isPunished($player) ? $this->punishments[$player->getUid()] : null;
    }

    /**
     * @return PlayerPunishment[]
     */
    public function getAll(): array
    {
        return $this->punishments;
    }

    public function current(): PlayerPunishment
    {
        return $this->punishments[$this->position];
    }

    public function valid(): bool
    {
        return isset($this->punishments[$this->position]);
    }

}