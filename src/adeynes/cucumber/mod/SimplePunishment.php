<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\HasTimeOfCreation;

abstract class SimplePunishment implements Punishment, HasTimeOfCreation
{

    /** @var string */
    protected string $reason, $moderator;

    /** @var int */
    protected int $time_created;

    public function __construct(string $reason, string $moderator, int $time_created)
    {
        $this->reason = $reason;
        $this->moderator = $moderator;
        $this->time_created = $time_created;
    }

    public function getReason(): string
    {
        return $this->reason;
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

    public function getRawData(): array
    {
        return [
            'reason' => $this->getReason(),
            'moderator' => $this->getModerator(),
            'time_created' => $this->getTimeOfCreation()
        ];
    }

}