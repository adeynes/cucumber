<?php

namespace cucumber\mod\ban;

use cucumber\mod\ban\Punishment;
use cucumber\mod\utils\PlayerPunishmentCompound;
use cucumber\utils\CPlayer;

/**
 * A list of PlayerPunishment. Addition of a punishment is triggered
 * if a player attempts to join with an IP that matches the list's
 */
class IpBanList implements Punishment
{

    /** @var string */
    protected $ip;
    /**
     * The list of bans under this IP
     * @var PlayerPunishmentCompound
     */
    protected $compound;

    /**
     * @param string $ip
     * @param Ban[] $bans
     */
    public function __construct(string $ip, array $bans = [])
    {
        $this->ip = $ip;
        $this->compound = new PlayerPunishmentCompound($bans);
    }

    public function addBan(Ban $ban): void
    {
        $this->compound->addPunishment($ban);
    }

    /**
     * Checks if a player is banned
     * This returns true if (a) the player's IP matches
     * the IpBanList's IP or (b) if the player has previously
     * been banned through joining with the list's IP
     * @param CPlayer $player
     * @param bool $ban_if_not Whether or not the ban the player if
     * they are banned but have not previously been recorded in the list.
     * Equivalent to `if ($list->isPunished($player)) $lis->addBan(new Ban($player));`
     * @return bool
     */
    public function isPunished(CPlayer $player, bool $ban_if_not = true): bool
    {
        if ($this->compound->getPunishment($player) instanceof Ban) return true;

        // Check if player's IP is banned. If it is, it means
        // they don't have a Ban entry as we didn't return above
        if ($player->getIp() === $this->ip) {
            if ($ban_if_not) $this->addBan(new Ban($player));
            return true;
        }

        return false;
    }

}