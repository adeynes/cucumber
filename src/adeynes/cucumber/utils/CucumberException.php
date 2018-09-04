<?php
declare(strict_types=1);

namespace adeynes\cucumber\utils;



class CucumberException extends \Exception
{

    /**
     * @param string $message
     * @param string[] $data The data that will populate the message
     */
    public function __construct(string $message, array $data = [])
    {
        $message = "&c$message";
        $apply_colors = function (&$value) {
            $value = "&b$value&c";
        };
        array_walk($data, $apply_colors);
        parent::__construct(MessageFactory::fullFormat($message, $data));
    }

}