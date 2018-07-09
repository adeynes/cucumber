<?php

namespace cucumber\mod\lists;

use cucumber\mod\PlayerPunishment;
use cucumber\utils\CPlayer;

class PlayerPunishmentList extends SimplePunishmentList
{

    public function isPunished(CPlayer $player): bool
    {
        $uid = $player->getUid();
        return isset($this->punishments[$uid]) &&
            $this->punishments[$uid]->isPunished($player);
    }

    public function get(CPlayer $player): ?PlayerPunishment
    {
        return $this->isPunished($player) ? $this->punishments[$player->{$this->method}()] : null;
    }

}