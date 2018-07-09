<?php

namespace cucumber\mod\lists;

use cucumber\mod\Ban;
use cucumber\utils\CPlayer;

class BanList extends PlayerPunishmentList
{

    public function ban(Ban $ban): void
    {
        $this->add($ban);
    }

    public function unban(string $uid): void
    {
        $this->remove($uid);
    }

    public function isBanned(CPlayer $player): bool
    {
        return $this->isPunished($player);
    }

}