<?php

namespace cucumber\mod;

interface Expirable
{

    public function isExpired(): bool;

}