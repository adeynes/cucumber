<?php

namespace cucumber\event;

use cucumber\Cucumber;
use pocketmine\Player;

class ChatEvent extends CEvent
{

    protected const TYPE = 'chat';

    protected $plugin;
    protected $player;
    protected $message;

    public function __construct(Cucumber $plugin, Player $player, string $message)
    {
        $this->plugin = $plugin;
        $this->player = $player;
        $this->message = $message;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData(): array
    {
        return [
            'type' => self::TYPE,
            'name' => $this->getPlayer()->getName(),
            'message' => $this->getMessage()
        ];
    }

}