<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\Cucumber;

abstract class SimplePunishment implements Punishment, Expirable
{

    /** @var string */
    protected $reason;

    /** @var int */
    protected $expiration;

    /** @var string */
    protected $moderator;

    protected $time_created;

    public function __construct(string $reason, int $expiration, string $moderator, int $time_created)
    {
        $this->reason = $reason;
        $this->expiration = $expiration;
        $this->moderator = $moderator;
        $this->time_created = $time_created;
    }

    abstract public static function from(array $row);

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getExpiration(): int
    {
        return $this->expiration;
    }

    public function getExpirationFormatted(): string
    {
        return date(Cucumber::getInstance()->getMessage('time-format'), $this->getExpiration());
    }

    public function getModerator(): string
    {
        return $this->moderator;
    }

    public function getTimeOfCreation(): int
    {
        return $this->time_created;
    }

    public function getTimeOfCreationFormatted(): string
    {
        return date(Cucumber::getInstance()->getMessage('time-format'), $this->getTimeOfCreation());
    }

    public function isExpired(): bool
    {
        return time() > $this->getExpiration();
    }

    public function getData(): array
    {
        return [
            'reason' => $this->getReason(),
            'expiration' => $this->getExpiration(),
            'moderator' => $this->getModerator(),
            'time_created' => $this->getTimeOfCreation()
        ];
    }

    public function getDataFormatted(): array
    {
        return [
            'reason' => $this->getReason(),
            'expiration' => $this->getExpirationFormatted(),
            'moderator' => $this->getModerator(),
            'time_created' => $this->getTimeOfCreationFormatted()
        ];
    }

}