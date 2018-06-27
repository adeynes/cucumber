<?php

namespace cucumber\ban;

use cucumber\utils\CPlayer;

interface Punishment
{

    public function isPunished(CPlayer $player): bool;

}