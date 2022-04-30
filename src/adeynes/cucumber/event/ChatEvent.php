<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use adeynes\cucumber\log\LogSeverity;
use pocketmine\player\Player;

class ChatEvent extends CucumberPlayerEvent
{

    /** @var string */
    protected static string $type;

    /** @var string */
    protected static string $template;

    /** @var LogSeverity */
    protected static LogSeverity $severity;

    /** @var string */
    protected string $message;

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