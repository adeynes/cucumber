<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\Cucumber;

trait Expirable
{

    /** @var int */
    protected $expiration;

    public function getExpiration(): int
    {
        return $this->expiration;
    }

    public function getExpirationFormatted(): string
    {
        return date(Cucumber::getInstance()->getMessage('time-format'), $this->getExpiration());
    }

    public function isExpired(): bool
    {
        return time() > $this->getExpiration();
    }

}