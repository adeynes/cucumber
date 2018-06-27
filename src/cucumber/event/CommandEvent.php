<?php

namespace cucumber\event;

use cucumber\Cucumber;
use pocketmine\Player;

class CommandEvent extends CPlayerEvent
{

    /** @var string */
    protected $command;

    public function __construct(Player $player, string $command)
    {
        $this->command = $command;
        parent::__construct('command', $player);
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getData(): array
    {
        return [
            'name' => $this->getPlayer()->getName(),
            'command' => $this->getCommand()
        ];
    }

}