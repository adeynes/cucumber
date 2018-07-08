<?php

namespace cucumber\mod;

use cucumber\utils\CPlayer;

class Mute extends PlayerPunishment
{

    public function isMuted(CPlayer $player): bool
    {
        return $this->isPunished($player);
    }

}