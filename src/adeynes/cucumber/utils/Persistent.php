<?php
declare(strict_types=1);

namespace adeynes\cucumber\utils;

use poggit\libasynql\DataConnector;

interface Persistent
{

    public function save(DataConnector $connector, ?callable $onSuccess = null): void;

}