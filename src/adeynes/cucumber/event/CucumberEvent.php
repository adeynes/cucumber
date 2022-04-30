<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use adeynes\cucumber\log\LogSeverity;
use adeynes\cucumber\utils\Formattable;
use pocketmine\event\Event;

/**
 * The parent class for all Cucumber events,
 * used to listen for all of them to log
 * @allowHandle
 */
abstract class CucumberEvent extends Event implements Formattable
{

    /** @var string */
    protected static string $type;

    /** @var string */
    protected static string $template;

    /** @var LogSeverity */
    protected static LogSeverity $severity;

    public static function init(string $type, string $template, LogSeverity $severity): void
    {
        static::$type = $type;
        static::$template = $template;
        static::$severity = $severity;
    }

    public function getType(): string
    {
        return static::$type;
    }

    public function getTemplate(): string
    {
        return static::$template;
    }

    public function getSeverity(): LogSeverity
    {
        return static::$severity;
    }

}