<?php

namespace cucumber\mod;

use cucumber\mod\utils\PlayerPunishmentList;
use cucumber\utils\CPlayer;

/**
 * A list of PlayerPunishment. Addition of a punishment is triggered
 * if a player attempts to join with an IP that matches the list's
 */
class IpBan implements Punishment
{

    /** @var string */
    protected $ip;
    /**
     * The list of bans under this IP
     * @var PlayerPunishmentList[]
     */
    protected $bans;

    /**
     * @param string $ip
     * @param Ban[] $bans
     */
    public function __construct(string $ip, array $bans = [])
    {
        $this->ip = $ip;
        $this->bans = new PlayerPunishmentList([]);
        foreach ($bans as $ban)
            $this->ban($ban);
    }

    public function ban(Ban $ban): void
    {
        $this->bans->punish($ban);
    }

    /**
     * Checks if a player is banned
     * This returns true if (a) the player's IP matches
     * the IpBan's IP or (b) if the player has previously
     * been banned through joining with the list's IP
     * @param CPlayer $player
     * @param bool $ban_if_not Whether or not the ban the player if
     * they are banned but have not previously been recorded in the list.
     * Equivalent to `if (!$list->isPunished($player)) $list->punish(new Ban($player));`
     * @return bool
     */
    public function isPunished(CPlayer $player, bool $ban_if_not = true): bool
    {
        if($this->bans->isPunished($player))
            return true;

        // Check if player's IP is banned. If it is, it means
        // they don't have a Ban entry as we didn't return above
        if ($player->getIp() === $this->ip) {
            if ($ban_if_not) $this->ban(new Ban($player));
            return true;
        }

        return false;
    }

}