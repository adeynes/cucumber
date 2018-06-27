<?php

namespace cucumber\event;

use cucumber\Cucumber;
use pocketmine\Player;

class CommandEvent extends CEvent
{

    protected const TYPE = 'command';

    protected $plugin;
    protected $player;
    protected $command;

    public function __construct(Cucumber $plugin, Player $player, string $command)
    {
        $this->plugin = $plugin;
        $this->player = $player;
        $this->command = $command;
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
            'type' => self::TYPE,
            'name' => $this->getPlayer()->getName(),
            'command' => $this->getCommand()
        ];
    }

}