<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

class CommandParser
{

    public static function parse(CucumberCommand $command, array $args): ParsedCommand
    {
        $tags = [];

        foreach ($args as $i => $arg) {
            if (strpos($arg, '-') !== 0)
                continue;

            // Avoid getting finding the tag twice; only first time counts
            if (isset($tags[$tag = substr($arg, 1)]))
                continue;

            // Use is_null because $length can be 0 so !0 would be true
            if (is_null($length = $command->getTag($tag)))
                continue;

            $tags[$tag] = implode(' ', array_slice($args, $i + 1, $length));

            // Remove tag & tag parameters
            // array_diff_key() doesn't reorder the keys
            $args = array_diff_key($args, self::makeKeys(range($i, $i + $length)));
        }

        return new ParsedCommand($command->getName(), $args, $tags);
    }

    public static function parseDuration(string $duration): int
    {
        $parts = str_split($duration);
        $current = 0;
        $time_units = ['y' => 'year', 'M' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'm' => 'minute'];
        $time = '';

        foreach ($time_units as $symbol => $unit) {
            if (($length = array_search($symbol, $parts)) === false)
                continue;

            $n = implode('', array_slice($parts, $current, $length)); // not $length-1 bc it's a length not an offset
            $time .= "$n $unit ";
            array_splice($parts, $current, $length + 1);
        }

        $time = trim($time);
        return $time === '' ? time() : strtotime($time);
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