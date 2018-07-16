<?php
declare(strict_types=1);

namespace cucumber\log;

/**
 * For future use
 */
interface LogLevels
{

    /**
     * Chat, normal commands
     * @var int
     */
    const LOG = 100;

    /**
     * Normal but noticeable events, e.g. gamemode change
     * @var int
     */
    const NOTICE = 250;

    /**
     * Significant events, e.g. moderation actions
     * @var int
     */
    const IMPORTANT = 500;

    /**
     * Exceptional events that likely require monitoring, e.g. administrative actions (/op)
     * @var int
     */
    const ALERT = 550;

    /**
     * The list of importance levels
     * @var int[]
     */
    const IMPORTANCE_LEVELS = [self::LOG, self::NOTICE, self::IMPORTANT, self::ALERT];

}