<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

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

    public function __construct(Player $player, array $data)
    {
        parent::__construct($player);
    }

    public function getData(): array
    {
        return parent::getData();
    }

}