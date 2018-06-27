<?php

namespace cucumber\event;

use cucumber\Cucumber;
use pocketmine\Player;

class ModerationEvent extends CEvent
{

    protected const TYPE = 'moderation';

    public function __construct(Cucumber $plugin, Player $player, array $data)
    {

    }

    public function getData(): array
    {
        return [
            'type' => self::TYPE
        ];
    }

}