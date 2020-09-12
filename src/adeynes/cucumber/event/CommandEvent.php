<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use adeynes\cucumber\log\LogSeverity;
use pocketmine\Player;

class CommandEvent extends CucumberPlayerEvent
{

    /** @var string */
    protected static $type;

    /** @var string */
    protected static $template;

    /** @var LogSeverity */
    protected static $severity;

    /** @var string */
    protected $command;

    public function __construct(Player $player, string $command)
    {
        $this->command = $command;
        parent::__construct($player);
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getFormatData(): array
    {
        return parent::getFormatData() + ['command' => $this->getCommand()];
    }

    public function getMessagesPath(): string
    {
        return 'log.command';
    }

}