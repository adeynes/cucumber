<?php
declare(strict_types=1);

namespace adeynes\cucumber\log;

/**
 * For future use
 */
interface LogSeverities
{

    /**
     * Chat, normal commands
     * @var int
     */
    public const LOG = 100;

    /**
     * Normal but noticeable events, e.g. gamemode change
     * @var int
     */
    public const NOTICE = 250;

    /**
     * Significant events, e.g. moderation actions
     * @var int
     */
    public const IMPORTANT = 500;

    /**
     * Exceptional events that likely require monitoring, e.g. administrative actions (/op)
     * @var int
     */
    public const ALERT = 550;

    public const SEVERITIES = ['log' => LogSeverities::LOG, 'notice' => LogSeverities::NOTICE,
        'important' => LogSeverities::IMPORTANT, 'alert' => LogSeverities::ALERT];

}