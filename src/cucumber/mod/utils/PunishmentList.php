<?php

namespace cucumber\mod\utils;

use cucumber\mod\Punishment;

// This leaves punish() implementation to each concrete list
abstract class PunishmentList implements Punishment, \Iterator
{

    /** @var string[] */
    protected static $messages;

    /** @var int */
    protected $position = 0;

    abstract protected static function initMessages(): void;

    /**
     * @return Punishment[]
     */
    abstract public function getAll(): array;

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

}