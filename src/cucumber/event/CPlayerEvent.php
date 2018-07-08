<?php

namespace cucumber\event;

use pocketmine\Player;

abstract class CPlayerEvent extends CEvent
{

    /** @var Player */
    protected $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getData(): array
    {
        return ['name' => $this->getPlayer()->getName()];
    }

}