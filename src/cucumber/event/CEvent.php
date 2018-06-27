<?php

namespace cucumber\event;

/**
 * The parent class for all Cucumber events,
 * used to listen for all of them to log
 * @allowHandle
 */
abstract class CEvent
{

    /** @var string */
    protected static $type;

    public function __construct(string $type)
    {
        self::$type = $type;
    }

    /**
     * Returns the values that will replace populate the message template
     * @return array
     */
    abstract public function getData(): array;

    public function getType(): string
    {
        return self::$type;
    }

}