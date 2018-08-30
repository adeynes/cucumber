<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

class IpPunishment extends SimplePunishment
{

    /** @var string */
    protected $ip;

    public function __construct(string $ip, string $reason, int $expiration, string $moderator)
    {
        $this->ip = $ip;
        parent::__construct($reason, $expiration, $moderator);
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getData(): array
    {
        return parent::getData() + ['ip' => $this->getIp()];
    }

    public function getDataFormatted(): array
    {
        return parent::getDataFormatted() + ['ip' => $this->getIp()];
    }

}