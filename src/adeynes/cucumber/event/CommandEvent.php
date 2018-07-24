<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use pocketmine\Player;

class CommandEvent extends CucumberPlayerEvent
{

    /** @var string */
    protected $command;

    /** @var string */
    protected static $type;

    /** @var string */
    protected static $template;

    public function __construct(Player $player, string $command)
    {
        $this->command = $command;
        parent::__construct($player);
    }

    public static function init(string $type, string $template): void
    {
        self::$type = $type;
        self::$template = $template;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getCommand(): string
    {
        return $this->command;
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
        return parent::getData() + ['command' => $this->getCommand()];
    }

}