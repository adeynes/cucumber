<?php

namespace cucumber\event;

use pocketmine\Player;

class ModerationEvent extends CEvent
{

    public function __construct(Player $player, array $data)
    {
        parent::__construct('moderation');
    }

    public function getData(): array
    {
        return [];
    }

}