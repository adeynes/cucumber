<?php
declare(strict_types=1);

namespace adeynes\cucumber\utils;

use Exception;

class CucumberException extends Exception
{

    /**
     * @param string $message
     * @param string[] $data The data that will populate the message
     */
    public function __construct(string $message, array $data = [])
    {
        parent::__construct(MessageFactory::fullFormat($message, $data));
    }

}