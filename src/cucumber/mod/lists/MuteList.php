<?php

namespace cucumber\mod\lists;

use cucumber\mod\Mute;
use cucumber\utils\CPlayer;

class MuteList extends PlayerPunishmentList
{

    public function mute(Mute $mute): void
    {
        $this->add($mute);
    }

    public function unmute(string $uid): void
    {
        $this->remove($uid);
    }

    public function isMuted(CPlayer $player): bool
    {
        return $this->isPunished($player);
    }

}