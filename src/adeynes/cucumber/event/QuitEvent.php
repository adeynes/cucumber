<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

class QuitEvent extends CucumberPlayerEvent
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