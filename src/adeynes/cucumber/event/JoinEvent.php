<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use adeynes\cucumber\log\LogSeverity;

class JoinEvent extends CucumberPlayerEvent
{

    /** @var string */
    protected static $type;

    /** @var string */
    protected static $template;

    /** @var LogSeverity */
    protected static $severity;

}