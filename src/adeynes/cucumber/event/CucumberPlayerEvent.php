<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use pocketmine\Player;

abstract class CucumberPlayerEvent extends CucumberEvent
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

    public function getFormatData(): array
    {
        return ['player' => $this->getPlayer()->getName()];
    }

}