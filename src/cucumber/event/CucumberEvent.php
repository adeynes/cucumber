<?php
declare(strict_types=1);

namespace cucumber\event;

use cucumber\utils\HasData;
use pocketmine\event\Event;

/**
 * The parent class for all Cucumber events,
 * used to listen for all of them to log
 * @allowHandle
 */
abstract class CucumberEvent extends Event implements HasData
{

    /** @var string */
    protected static $type;

    /** @var string */
    protected static $template;

    public static function init(string $type, string $template): void
    {
        self::$type = $type;
        self::$template = $template;
    }

    public function getType(): string
    {
        return self::$type;
    }

    public function getTemplate(): string
    {
        return self::$template;
    }

}