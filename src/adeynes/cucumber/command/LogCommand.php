<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\log\LogSeverities;
use pocketmine\command\CommandSender;

class LogCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct(
            $plugin,
            'log',
            'cucumber.command.log',
            'Log a message',
            1,
            '/log <message>',
            ['s' => 1]
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$message] = $command->get([[0, -1]]);
        $severity = $command->getTag('s') ?? 'log';

        if (!isset(LogSeverities::SEVERITIES[$severity])) {
            $this->getPlugin()->formatAndSend($sender, 'error.unknown-log-severity', ['severity' => $severity]);
            return false;
        }

        $this->getPlugin()->getLogManager()->log($message, LogSeverities::SEVERITIES[$severity]);

        $this->getPlugin()->formatAndSend($sender, 'success.log', ['message' => $message]);
        return true;
    }

}