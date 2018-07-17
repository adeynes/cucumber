<?php
declare(strict_types=1);

namespace cucumber\utils;

use pocketmine\utils\TextFormat;

final class MessageFactory
{

    /**
     * Formats a message template with the given data
     * @param string $template A message template with tags enclosed between 2 %, i.e. %tag%
     * @param array $data An array with the tag name as the key and the replacement as the value,
     * i.e. ["tag" => "my cool tag"] will turn "this is %tag%" into "this is my cool tag"
     * @return string The formatted message
     */
    public static function formatNoColor(string $template, array $data): string
    {
        $tags = [];
        // Find everything between two %
        preg_match_all('/%(.*?)%/', $template,$tags);
        // Make sure we only have unique values; str_replace replaces everything anyways
        $tags = array_unique($tags);
        // Given %tag%, $tags[1] will be "tag" while $tags[0] will be "%tag%"
        foreach ($tags[1] as $key => $tag)
            $template = str_replace($tags[0][$key], $data[$tag], $template);

        return $template;
    }

    public static function colorize(string $string): string
    {
        return TextFormat::colorize($string);
    }

    public static function format(string $template, array $data): string
    {
        return self::colorize(self::formatNoColor($template, $data));
    }

}