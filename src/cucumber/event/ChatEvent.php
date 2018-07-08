<?php

namespace cucumber\event;

use pocketmine\Player;

class ChatEvent extends CPlayerEvent
{

    /** @var string */
    protected $message;

    public function __construct(Player $player, string $message)
    {
        $this->message = $message;
        parent::__construct($player);
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