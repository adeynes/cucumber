<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

abstract class IpPunishment extends SimplePunishment
{

    /** @var string */
    protected $ip;

    public function __construct(string $ip, string $reason, string $moderator, int $time_created)
    {
        $this->ip = $ip;
        parent::__construct($reason, $moderator, $time_created);
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getRawData(): array
    {
        return parent::getRawData() + ['ip' => $this->getIp()];
    }

}