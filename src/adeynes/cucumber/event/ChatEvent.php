<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use adeynes\cucumber\log\LogSeverity;
use pocketmine\Player;

class ChatEvent extends CucumberPlayerEvent
{

    /** @var string */
    protected static $type;

    /** @var string */
    protected static $template;

    /** @var LogSeverity */
    protected static $severity;

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

    public function getFormatData(): array
    {
        return parent::getFormatData() + ['message' => $this->getMessage()];
    }

    public function getMessagesPath(): string
    {
        return 'log.chat';
    }

}