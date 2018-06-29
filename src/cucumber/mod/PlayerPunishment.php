<?php

namespace cucumber\mod;

use cucumber\utils\CPlayer;

/**
 * The base class for any punishment that punishes
 * players through data that uniquely identifies
 * them, namely name and UID (XUID)
 */
abstract class PlayerPunishment
{

    /** @var CPlayer */
    protected $player;

    public function __construct(CPlayer $player)
    {
        $this->player = $player;
    }

    public function getPlayer(): CPlayer
    {
        return $this->player;
    }

    public function isPunished(CPlayer $player): bool
    {
        return
            $player->getName() === $this->getPlayer()->getName() ||
            $player->getUid() === $this->getPlayer()->getUid();
    }

}