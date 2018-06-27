<?php

namespace cucumber\ban;

use cucumber\utils\CPlayer;

abstract class PlayerPunishment
{

    protected $name;
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