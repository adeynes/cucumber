<?php

namespace cucumber\mod\lists;

use cucumber\mod\Punishment;

// This leaves punish() implementation to each concrete list
abstract class PunishmentList implements Punishment, \Iterator
{

    /** @var int */
    protected $position = 0;

    /** @var Punishment[] */
    protected $punishments = [];

    /**
     * @return Punishment[]
     */
    public function getAll(): array
    {
        return $this->punishments;
    }

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

    public function current(): Punishment
    {
        return $this->punishments[$this->position];
    }

    public function valid(): bool
    {
        return isset($this->punishments[$this->position]);
    }

}