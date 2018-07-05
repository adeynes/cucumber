<?php

namespace cucumber\mod\utils;

use cucumber\mod\PlayerPunishment;
use cucumber\mod\Punishment;
use cucumber\utils\CException;
use cucumber\utils\CPlayer;

abstract class PlayerPunishmentList implements Punishment, \Iterator
{

    /** @var int */
    protected $position = 0;

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
     * @param CPlayer $player
     * @throws CException If the player isn't banned
     */
    public function pardon(CPlayer $player): void
    {
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

    /**
     * @return PlayerPunishment[]
     */
    public function all(): array
    {
        return $this->punishments;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): PlayerPunishment
    {
        return $this->punishments[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->punishments[$this->position]);
    }

}