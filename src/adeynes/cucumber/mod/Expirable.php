<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

interface Expirable
{

    public function isExpired(): bool;

}