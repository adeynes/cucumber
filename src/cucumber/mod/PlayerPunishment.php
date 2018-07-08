<?php

namespace cucumber\mod;

use cucumber\utils\CPlayer;

/**
 * The base class for any punishment that punishes players
 * through data that uniquely identifies them (XUID)
 */
abstract class PlayerPunishment implements Punishment
{

    /** @var CPlayer */
    protected $player;

    /** @var int */
    public $expiration;

    public function __construct(CPlayer $player, int $expiration = null)
    {
        $this->player = $player;
        $this->expiration = $expiration ?? strtotime('+10 years');
    }

    public function getPlayer(): CPlayer
    {
        return $this->player;
    }

    public function isPunished(CPlayer $player): bool
    {
        return $player->getUid() === $this->getPlayer()->getUid();
    }

    public function isExpired(): bool
    {
        return time() > $this->expiration;
    }

}