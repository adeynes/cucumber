<?php

namespace cucumber\mod\lists;

use cucumber\mod\IpBan;
use cucumber\utils\CPlayer;

class IpBanList extends SimplePunishmentList
{

    public function ban(IpBan $ip_ban): void
    {
        $this->add($ip_ban);
    }

    public function unban(string $ip): void
    {
        $this->remove($ip);
    }

    public function isPunished(CPlayer $player): bool
    {
        $check = $player->getIp();
        return isset($this->punishments[$check]) &&
            $this->punishments[$check]->isPunished($player);
    }

    public function isBanned(CPlayer $player): bool
    {
        return $this->isPunished($player);
    }

    public function get(CPlayer $player): ?IpBan
    {
        return $this->isPunished($player) ? $this->punishments[$player->getIp()] : null;
    }
    
}