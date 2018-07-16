<?php
declare(strict_types=1);

namespace cucumber\mod;

class SimplePunishment implements Punishment, Expirable
{

    /** @var string|null */
    protected $reason;

    /** @var int */
    protected $expiration;

    /** @var int */
    protected $moderator_id;

    public function __construct(?string $reason, ?int $expiration, int $moderator_id)
    {
        $this->reason = $reason;
        $this->expiration = $expiration ?? strtotime('+10 years');
        $this->moderator_id = $moderator_id;
    }

    public static function from(array $properties)
    {
        return new self($properties['reason'], $properties['expiration'], $properties['moderator']);
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getExpiration(): int
    {
        return $this->expiration;
    }

    public function getModeratorId(): string
    {
        return $this->moderator_id;
    }

    public function isExpired(): bool
    {
        return time() > $this->getExpiration();
    }

}