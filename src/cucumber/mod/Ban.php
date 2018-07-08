<?php

namespace cucumber\mod;

use cucumber\utils\CPlayer;

class Ban extends PlayerPunishment
{

    public function isBanned(CPlayer $player): bool
    {
        return $this->isPunished($player);
    }

}
