<?php

namespace cucumber\mod;

use cucumber\mod\utils\BanList;
use cucumber\utils\CException;
use cucumber\utils\CPlayer;

class IpBan implements Punishment
{

    /** @var string */
    protected $ip;

    /**
     * The list of bans under this IP
     * @var BanList
     */
    protected $bans;

    /**
     * @param string $ip
     * @param Ban[] $bans
     * @throws CException If one of the bans exists twice
     */
    public function __construct(string $ip, array $bans = [])
    {
        $this->ip = $ip;
        $this->bans = new BanList;
        foreach ($bans as $ban)
            $this->ban($ban);
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getBans(): BanList
    {
        return $this->bans;
    }

    public function ban(Ban $ban): void
    {
        $this->bans->ban($ban);
    }

    /**
     * Checks if a player is banned
     * This returns true if (a) the player's IP matches
     * the IpBan's IP or (b) if the player has previously
     * been banned through joining with the list's IP
     * @param CPlayer $player
     * @return bool
     */
    public function isPunished(CPlayer $player): bool
    {
        return $player->getIp() === $this->getIp() || $this->getBans()->isBanned($player);
    }

    public function isBanned(CPlayer $player): bool
    {
        return $this->isPunished($player);
    }

}