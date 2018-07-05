<?php

namespace cucumber\mod\utils;

use cucumber\mod\Ban;
use cucumber\utils\CPlayer;

class BanList extends PlayerPunishmentList
{

    protected static function initMessages(): void
    {
        self::$messages = [
            'already-punished' => '%name% is already banned!',
            'not-punished' => '%name% has not been banned!'
        ];
    }

    public function ban(Ban $ban, $reban = false): void
    {
        $this->punish($ban, $reban);
    }

    public function unban(CPlayer $player): void
    {
        $this->pardon($player);
    }

    public function isBanned(CPlayer $player): bool
    {
        return $this->isPunished($player);
    }

}