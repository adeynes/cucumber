<?php
declare(strict_types=1);

namespace adeynes\cucumber\log;

interface Logger
{

    /**
     * Logs the given message
     * @param string $message
     * @return void
     */
    public function log(string $message): void;

    public function logNow(): void;

}