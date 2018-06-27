<?php

namespace cucumber\utils\ds;

class Stack implements \Iterator, \Countable
{

    protected $stack = [];
    protected $position = 0;

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->peek($this->position);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return !is_null($this->peek($this->position));
    }

    public function count(): int
    {
        return count($this->stack);
    }

    public function pop()
    {
        return array_shift($this->stack);
    }

    public function push($value)
    {
        return array_unshift($this->stack, $value);
    }

    public function peek(int $index = 0)
    {
        return $this->stack[$index] ?? null;
    }

}