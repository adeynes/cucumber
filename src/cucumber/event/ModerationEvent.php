<?php

namespace cucumber\event;

use pocketmine\Player;

class ModerationEvent extends CPlayerEvent
{

    public function __construct(Player $player, array $data)
    {
        parent::__construct($player);
    }

    public function getData(): array
    {
        return parent::getData();
    }

}