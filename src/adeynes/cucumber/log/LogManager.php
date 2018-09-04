<?php
declare(strict_types=1);

namespace adeynes\cucumber\log;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\event\CucumberEvent;
use adeynes\cucumber\utils\ds\Stack;
use adeynes\cucumber\utils\MessageFactory;

final class LogManager
{

    /** @var Cucumber */
    private $plugin;

    /** @var string */
    private $dir;

    /** @var Stack<Logger>[] */
    private $loggers;

    /** @var string */
    private $global_template;

    /** @var string */
    private $time_format;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
        $this->dir = $this->getPlugin()->getConfig()->getNested('log.path') ?? 'log/';
        @mkdir($this->getDirectory());

        $this->loggers = [];
        foreach (LogSeverity::values() as $severity) {
            $this->loggers[$severity->getValue()] = new Stack;
        }
        // Highest severities first, but can't use Stack because we need associative
        $this->loggers = array_reverse($this->loggers, true);

        $this->global_template = $this->getPlugin()->getMessage('log.templates.global');
        $this->time_format = $this->getPlugin()->getMessage('time-format') ?? 'Y-m-d\TH:i:s';
    }

    public function getPlugin(): Cucumber
    {
        return $this->plugin;
    }

    public function getDirectory(): string
    {
        return $this->getPlugin()->getDataFolder() . $this->dir;
    }

    public function log(string $message, LogSeverity $severity = null): void
    {
        if (is_null($severity)) $severity = LogSeverity::LOG();

        foreach ($this->loggers as $current_severity => $loggers) {
            // Don't log to higher severities
            if ($severity->getValue() < $current_severity) continue;

            /** @var Logger $logger */
            foreach ($loggers as $logger) {
                if ($logger->log($message) === false) break 2;
            }
        }
    }

    public function pushLogger(Logger $logger, LogSeverity $severity): self
    {
        /*
        try {
            $severity = LogSeverity::fromString($severity);
        } catch (CucumberException $exception) {
            $this->getPlugin()->log(
                MessageFactory::colorize("&eUnknown logger severity &b$severity&e, defaulting to &blog")
            );
            $severity = LogSeverity::LOG();
        }
        */

        $this->loggers[$severity->getValue()]->push($logger);

        return $this;
    }

    /**
     * Creates a log message for the given event
     * Uses the template for the event type
     * specified in the lang file and populates
     * it with the data in CucumberEvent::getData()
     * @param CucumberEvent $ev
     * @return string
     */
    public function formatEventMessage(CucumberEvent $ev): string
    {
        return MessageFactory::format(
            $this->global_template,
            $this->generateGlobalTemplateData($ev->getTemplate(), $ev->getType(), $ev->getData())
        );
    }

    /**
     * Generates the data for the global template
     * @param string $template The event message template
     * @param string $type
     * @param array $data
     * @return array
     */
    private function generateGlobalTemplateData(string $template, string $type, array $data): array
    {
        return [
            'time' => date($this->time_format),
            'type' => $type,
            '...' => MessageFactory::format(
                $template,
                $data
            )
        ];
    }

}