<?php

namespace cucumber\utils;

use pocketmine\utils\TextFormat;

class CException extends \Exception
{

    /**
     * @param string $message
     * @param string[] $data The data that will populate the message
     * @param int $code
     */
    public function __construct(string $message, array $data = [], int $code = 800)
    {
        $apply_colors = function(&$value) {
            $value = TextFormat::AQUA . $value . TextFormat::RED;
        };
        array_walk($data, $apply_colors);
        parent::__construct(
            MessageFactory::format($message, $data),
            $code
        );
    }

}