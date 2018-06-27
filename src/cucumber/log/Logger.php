<?php

namespace cucumber\log;

interface Logger
{

    /**
     * Logs the given message
     * @param string $message
     * @return void
     */
    public function log(string $message): void;

}