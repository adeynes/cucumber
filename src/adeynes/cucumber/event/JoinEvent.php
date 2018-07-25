<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

class JoinEvent extends CucumberPlayerEvent
{

    /** @var string */
    protected static $type;

    /** @var string */
    protected static $template;

}