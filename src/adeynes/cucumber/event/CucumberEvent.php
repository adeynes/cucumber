<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use adeynes\cucumber\utils\HasData;
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
        static::$type = $type;
        static::$template = $template;
    }

    public function getType(): string
    {
        return static::$type;
    }

    public function getTemplate(): string
    {
        return static::$template;
    }

}