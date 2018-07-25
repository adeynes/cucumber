<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

class ChatAttemptEvent extends ChatEvent
{

    /** @var string */
    protected static $type;

    /** @var string */
    protected static $template;

}