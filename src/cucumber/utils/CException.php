<?php

namespace cucumber\utils;

use pocketmine\utils\TextFormat;

class CException extends \Exception
{

    /** @var array */
    protected $data;

    public function __construct(string $message, array $data, int $code = 0)
    {
        array_walk(
            $data,
            function (&$value, $key) {
                $value = TextFormat::AQUA . $value . TextFormat::RESET;
            }
        );
        $this->data = $data;
        parent::__construct($message, $code);
    }

    public function getData()
    {
        return $this->data;
    }

}