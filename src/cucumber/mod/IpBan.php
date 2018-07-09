<?php

namespace cucumber\mod;

use cucumber\mod\lists\BanList;
use cucumber\utils\CException;
use cucumber\utils\CPlayer;

class IpBan extends SimplePunishment
{

    /** @var string */
    protected $ip;

    public function __construct(string $ip)
    {
        $this->ip = $ip;
        parent::__construct('getIp', $ip);
    }

    public function getIp(): string
    {
        return $this->getCheck();
    }

    public function isBanned(CPlayer $player): bool
    {
        return $this->isPunished($player);
    }

}