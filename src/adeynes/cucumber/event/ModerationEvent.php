<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use pocketmine\Player;

class ModerationEvent extends CucumberPlayerEvent
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