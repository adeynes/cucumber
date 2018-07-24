<?php
declare(strict_types=1);

namespace adeynes\cucumber\utils\ds;

class Stack implements \Iterator, \Countable
{

    /** @var array */
    protected $stack = [];

    /** @var int */
    protected $position = 0;

    public function rewind(): void
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

    public function next(): void
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

    /**
     * Returns the first element of the internal array
     * @return Stack
     */
    public function pop(): self
    {
        array_shift($this->stack);
        return $this;
    }

    /**
     * @param mixed $value Inserts the value at the beginning of the internal array
     * @return Stack
     */
    public function push($value): self
    {
        array_unshift($this->stack, $value);
        return $this;
    }

    /**
     * @param int $index
     * @return mixed|null
     */
    public function peek(int $index = 0)
    {
        return $this->stack[$index] ?? null;
    }

}