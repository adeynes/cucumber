<?php
declare(strict_types=1);

namespace cucumber\utils;



class CException extends \Exception
{

    /**
     * @param string $message
     * @param string[] $data The data that will populate the message
     * @param int $code
     */
    public function __construct(string $message, array $data = [], int $code = 800)
    {
        $message = "&c$message";
        $apply_colors = function(&$value) {
            $value = "&b$value&c";
        };
        array_walk($data, $apply_colors);
        parent::__construct(
            MessageFactory::format($message, $data),
            $code
        );
    }

}