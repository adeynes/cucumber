<?php

namespace cucumber\mod;

class SimplePunishment implements Expirable
{

    /** @var string|null */
    protected $reason;

    /** @var int */
    protected $expiration;

    public function __construct(string $reason = null, int $expiration = null)
    {
        $this->reason = $reason;
        $this->expiration = $expiration ?? strtotime('+10 years');
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function isExpired(): bool
    {
        return time() > $this->expiration;
    }

}