<?php

namespace cucumber\ban;

use cucumber\utils\CPlayer;

/**
 * The base class for any punishment that punishes
 * players through data that uniquely identifies
 * them, namely name and UID (XUID)
 */
abstract class PlayerPunishment
{

    /** @var string */
    protected $name;
    /** @var string|null */
    protected $uid;

    public function __construct(CPlayer $player)
    {
        $this->name = $player->getName();
        $this->uid = $player->getUid();
    }

    public function isPunished(CPlayer $player): bool
    {
        return $player->getName() === $this->name || $player->getUid() === $this->uid;
    }

}