<?php

namespace cucumber\mod;

use cucumber\utils\CPlayer;

abstract class SimplePunishment implements Punishment
{

    /**
     * The method used on CPlayer in isPunished()
     * @var string
     */
    protected $method; // TODO: figure out how to make this static and how to do init cleanly

    /**
     * The value against which player data will be checked
     * @var mixed
     */
    protected $check;

    public function __construct(string $method, $check)
    {
        $this->method = $method;
        $this->check = $check;
    }

    public function getCheck()
    {
        return $this->check;
    }

    public function isPunished(CPlayer $player): bool
    {
        return $player->{$this->method}() === $this->check;
    }

}