<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use adeynes\cucumber\log\LogSeverity;
use pocketmine\Player;

/**
 * For future use
 */
class ModerationEvent extends CucumberPlayerEvent
{

    /** @var string */
    protected static $type;

    /** @var string */
    protected static $template;

    /** @var LogSeverity */
    protected static $severity;

    public function __construct(Player $player, array $data)
    {
        parent::__construct($player);
    }

    public function getData(): array
    {
        return parent::getData();
    }

}