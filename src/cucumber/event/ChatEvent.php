<?php
declare(strict_types=1);

namespace cucumber\event;

use pocketmine\Player;

class ChatEvent extends CucumberPlayerEvent
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
        return parent::getData() + ['message' => $this->getMessage()];
    }

}