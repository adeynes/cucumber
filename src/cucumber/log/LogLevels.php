<?php

namespace cucumber\log;

interface LogLevels
{

    /**
     * Chat, normal commands
     */
    const LOG = 100;

    /**
     * Normal but noticeable events, e.g. gamemode change
     */
    const NOTICE = 250;

    /**
     * Significant events, e.g. moderation actions
     */
    const IMPORTANT = 500;

    /**
     * Exceptional events that likely require monitoring, e.g. administrative actions (/op)
     */
    const ALERT = 550;

    /**
     * @var array IMPORTANCE_LEVELS The list of importance levels
     */
    const IMPORTANCE_LEVELS = [self::LOG, self::NOTICE, self::IMPORTANT, self::ALERT];

}