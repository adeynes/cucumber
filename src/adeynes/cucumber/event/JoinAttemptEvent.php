<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

class JoinAttemptEvent extends JoinEvent
{

    /** @var string */
    protected static $type;

    /** @var string */
    protected static $template;

}