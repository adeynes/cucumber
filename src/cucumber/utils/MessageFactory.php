<?php

namespace cucumber\log;

use cucumber\Cucumber;
use cucumber\event\CEvent;

final class MessageFactory
{

    private $plugin;
    private $config;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
        $this->config = $this->plugin->getConfig()->getNested('log.messages');
    }

    public function format(string $template, array $data): string
    {
        $tags = [];
        // Find everything between two %
        preg_match_all('/%(.*?)%/', $template,$tags);
        // Make sure we only have unique values, because str_replace replaces everything anyways
        $tags = array_unique($tags);
        // Given %tag%, $tags[1] will be "tag" while $tags[0] will be "%tag%"
        foreach ($tags[1] as $key => $tag)
            $template = str_replace($tags[0][$key], $data[$tag], $template);

        return $template;
    }

}