<?php
declare(strict_types=1);

namespace adeynes\cucumber\utils;

interface ErrorCodes
{

    public const ATTEMPT_PUNISH_PUNISHED = 801;
    public const ATTEMPT_PARDON_NOT_PUNISHED = 802;
    
    public const INVALID_PROVIDER_TYPE = 810;
    public const INVALID_PROVIDER_SETTINGS = 811;

}