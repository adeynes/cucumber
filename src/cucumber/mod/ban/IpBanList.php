<?php

namespace cucumber\mod\ban;

use cucumber\ban\Punishment;
use cucumber\utils\CPlayer;

class IpBanList implements Punishment
{

    protected $ip;
    /**
     * @var Ban[] The list of bans under this IP
     */
    protected $bans;

    public function __construct(string $ip)
    {
        $this->ip = $ip;
    }

    public function addBan(Ban $ban)
    {
        $this->bans[] = $ban;
    }

    public function isPunished(CPlayer $player, bool $ban_if_not = true): bool
    {
        $banned = false;

        if ($player->getIp() === $this->ip) $banned = true;

        if (!$banned || $ban_if_not) {
            foreach ($this->bans as $ban) {
                if ($ban->isBanned($player)) return true;
            }

            $this->addBan(new Ban($player));
        }

        return $banned;
    }

}