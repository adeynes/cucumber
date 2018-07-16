<?php
declare(strict_types=1);

namespace cucumber\mod;

interface Expirable
{

    public function isExpired(): bool;

}