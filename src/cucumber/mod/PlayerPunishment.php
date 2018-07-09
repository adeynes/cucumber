<?php

namespace cucumber\mod;

use cucumber\utils\CPlayer;

/**
 * The base class for any punishment that punishes players
 * through data that uniquely identifies them (XUID)
 */
abstract class PlayerPunishment extends SimplePunishment implements Expirable
{

    /** @var CPlayer */
    protected $player;

    /** @var int */
    public $expiration;

    public function __construct(CPlayer $player, int $expiration = null)
    {
        $this->player = $player;
        $this->expiration = $expiration ?? strtotime('+10 years');
        parent::__construct('getUid', $player->getUid());
    }

    public function getPlayer(): CPlayer
    {
        return $this->player;
    }

    public function getUid(): string
    {
        return $this->getCheck();
    }

    public function isExpired(): bool
    {
        return time() > $this->expiration;
    }

}