<?php

namespace cucumber\mod;

use cucumber\utils\CPlayer;

interface Punishment
{

    public function isPunished(CPlayer $player): bool;

}