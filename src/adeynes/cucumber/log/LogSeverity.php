<?php
declare(strict_types=1);

namespace adeynes\cucumber\log;

use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\ds\Enum;

/**
 * @method static self LOG()
 * @method static self NOTICE()
 * @method static self IMPORTANT()
 * @method static self ALERT()
 */
final class LogSeverity extends Enum
{

    /**
     * Chat, normal commands
     * @var int
     */
    private const LOG = 100;

    /**
     * Normal but noticeable events, e.g. gamemode change
     * @var int
     */
    private const NOTICE = 250;

    /**
     * Significant events, e.g. moderation actions
     * @var int
     */
    private const IMPORTANT = 500;

    /**
     * Exceptional events that likely require monitoring, e.g. administrative actions (/op)
     * @var int
     */
    private const ALERT = 550;

    /**
     * @param string $severity
     * @return LogSeverity
     * @throws CucumberException If the severity does not exists
     */
    public static function fromString(string $severity): self
    {
        $uppercase_severity = strtoupper($severity);
        try {
            return self::$uppercase_severity();
        } catch (\BadMethodCallException $exception) {
            throw new CucumberException(
                'Unknown log severity %severity%!',
                ['severity' => $severity]
            );
        }
    }

}