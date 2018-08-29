<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\HasData;

class SimplePunishment implements Punishment, Expirable
{

    /** @var string */
    protected $reason;

    /** @var int */
    protected $expiration;

    /** @var string */
    protected $moderator;

    public function __construct(string $reason, int $expiration, string $moderator)
    {
        $this->reason = $reason;
        $this->expiration = $expiration;
        $this->moderator = $moderator;
    }

    public static function from(array $row): self
    {
        return new self($row['reason'], $row['expiration'], $row['moderator']);
    }

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

    public function isExpired(): bool
    {
        return time() > $this->getExpiration();
    }

    public function getData(): array
    {
        return [
            'reason' => $this->getReason(),
            'expiration' => $this->getExpiration(),
            'moderator' => $this->getModerator()
        ];
    }

    public function getDataFormatted(): array
    {
        return [
            'reason' => $this->getReason(),
            'expiration' => $this->getExpirationFormatted(),
            'moderator' => $this->getModerator()
        ];
    }

}