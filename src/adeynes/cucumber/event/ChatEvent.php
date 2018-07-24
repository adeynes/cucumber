<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use pocketmine\Player;

class ChatEvent extends CucumberPlayerEvent
{

    /** @var string */
    protected $message;

    /** @var string */
    protected static $type;

    /** @var string */
    protected static $template;

    public function __construct(Player $player, string $message)
    {
        $this->message = $message;
        parent::__construct($player);
    }

    public static function init(string $type, string $template): void
    {
        self::$type = $type;
        self::$template = $template;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): string
    {
        return self::$type;
    }

    public function getTemplate(): string
    {
        return self::$template;
    }

    public function getData(): array
    {
        return parent::getData() + ['message' => $this->getMessage()];
    }

}