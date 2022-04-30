<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use adeynes\cucumber\log\LogSeverity;

class JoinEvent extends CucumberPlayerEvent
{

    /** @var string */
    protected static string $type;

    /** @var string */
    protected static string $template;

    /** @var LogSeverity */
    protected static LogSeverity $severity;

    public function getMessagesPath(): string
    {
        return 'log.join';
    }

}