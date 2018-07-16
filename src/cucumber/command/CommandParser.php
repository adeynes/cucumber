<?php
declare(strict_types=1);

namespace src\cucumber\command;

class CommandParser
{

    public static function parse(CucumberCommand $command, array $args): ParsedCommand
    {
        $tags = [];

        foreach ($args as $key => $arg) {
            if (is_null($length = $command->getTag($tag = substr($arg, 1))))
                continue;

            $tags[$tag] = array_slice($args, $key + 1, $length);
            $args = array_diff_key($args, self::makeKeys(range($key, $key + $length)));
        }

        return new ParsedCommand($command->getName(), $args, $tags);
    }

    /**
     * Turns the values of an array into the keys of the
     * return array. Populates values with an empty string
     * @param array $array
     * @return array
     */
    private static function makeKeys(array $array): array
    {
        $return = [];

        foreach ($array as $value) {
            $return[$value] = '';
        }

        return $return;
    }

}