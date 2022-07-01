<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\Cucumber;

trait Expirable
{

    /** @var ?int */
    protected ?int $expiration;

    public function getExpiration(): ?int
    {
        return $this->expiration;
    }

    public function getExpirationFormatted(): string
    {
        return $this->getExpiration() === null ?
               Cucumber::getInstance()->getMessage('moderation.no-expiration') :
               date(Cucumber::getInstance()->getMessage('time-format'), $this->getExpiration());
    }

    public function isExpired(): bool
    {
        return $this->getExpiration() === null ? false : time() > $this->getExpiration();
    }

    public function getExpiredFormatted(): string
    {
        return Cucumber::getInstance()->getMessage($this->isExpired() ? 'moderation.expired' : 'moderation.active');
    }

}