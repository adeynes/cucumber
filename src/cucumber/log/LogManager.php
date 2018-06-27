<?php

namespace cucumber\log;

use cucumber\Cucumber;
use cucumber\event\CEvent;
use cucumber\utils\ds\Stack;

final class LogManager
{

    private $plugin;
    private $dir;
    /**
     * @var Stack Called from top to bottom
     */
    private $loggers;
    /**
     * @var String[]
     */
    private $templates;
    /**
     * @var String
     */
    private $time_format;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
        $this->dir = $this->plugin->getConfig()->getNested('log.path') ?? 'log/';
        $this->loggers = new Stack();
        $messages = $this->plugin->messages;
        $this->templates = $messages->getNested('log.templates');
        $this->time_format = $messages->getNested('log.time-format') ?? 'Y-m-d\TH:i:s';
    }

    public function getDirectory()
    {
        return $this->plugin->getDataFolder() . $this->dir;
    }

    public function log(string $message)
    {
        foreach ($this->loggers as $logger) {
            $logger->log($message);
        }
    }

    public function addLogger(Logger $logger): self
    {
        $this->loggers->push($logger);
        return $this;
    }

    public function formatEventMessage(CEvent $ev): string
    {
        $data = $ev->getData();
        $type = $data['type'];
        unset($data['type']);
        return $this->plugin->getMessageFactory()->format(
            $this->templates['global'],
            $this->generateTemplateData($type, $data)
        );
    }

    private function generateTemplateData(string $type, array $data): array
    {
        return [
            'time' => date($this->time_format),
            'type' => $type,
            '...' => $this->plugin->getMessageFactory()->format(
                $this->templates[$type],
                $data
            )
        ];
    }

}