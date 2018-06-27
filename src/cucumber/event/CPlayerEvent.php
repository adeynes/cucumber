<?php

namespace cucumber\event;

use pocketmine\Player;

abstract class CPlayerEvent extends CEvent
{

    /** @var Player */
    protected $player;

    public function __construct(string $type, Player $player)
    {
        $this->player = $player;
        parent::__construct($type);
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

}