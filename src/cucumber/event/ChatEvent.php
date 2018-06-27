<?php

namespace cucumber\event;

use pocketmine\Player;

class ChatEvent extends CEvent
{

    /** @var Player */
    protected $player;
    /** @var string */
    protected $message;

    public function __construct(Player $player, string $message)
    {
        $this->player = $player;
        $this->message = $message;
        parent::__construct('chat');
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
            'name' => $this->getPlayer()->getName(),
            'message' => $this->getMessage()
        ];
    }

}