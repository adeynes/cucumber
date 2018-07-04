<?php

namespace cucumber\mod\utils;

use cucumber\mod\PlayerPunishment;
use cucumber\mod\Punishment;
use cucumber\utils\CException;
use cucumber\utils\CPlayer;

abstract class PlayerPunishmentList implements Punishment
{

    /** @var PlayerPunishment[] */
    protected $punishments;

    /** @var string[] */
    protected static $messages;

    public function __construct(array $punishments = [])
    {
        $this->punishments = $punishments;
        self::initMessages();
    }

    abstract protected static function initMessages(): void;

    /**
     * @param PlayerPunishment $punishment
     * @param bool $repunish Repunish the player is they already are
     * (Does not throw an exception)
     * @throws CException If the player is already punished
     */
    public function punish(PlayerPunishment $punishment, $repunish = false): void
    {
        $player = $punishment->getPlayer();
        if (isset($this->punishments[$player->getUid()]) &&
            !$repunish)
            throw new CException(
                self::$messages['already-punished'],
                ['name' => $player->getName()],
                301
            );
        $this->punishments[$player->getUid()] = $punishment;
    }

    /**
     * @param PlayerPunishment $punishment
     * @throws CException If the player isn't banned
     */
    public function pardon(PlayerPunishment $punishment): void
    {
        $player = $punishment->getPlayer();
        if (isset($this->punishments[$player->getUid()]))
            unset($this->punishments[$player->getUid()]);
        else throw new CException(
            self::$messages['not-punished'],
            ['name' => $player->getName()],
            302
        );
    }

    public function isPunished(CPlayer $player): bool
    {
        return isset($this->punishments[$player->getUid()]) &&
            $this->punishments[$player->getUid()]->isPunished($player);
    }

}