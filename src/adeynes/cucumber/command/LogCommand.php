<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\log\LogSeverities;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

class LogCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'log',
            'cucumber.command.log',
            'Log a message',
            '/log <message>'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$message] = $command->get(['message']);
        $severity = $command->getFlag('severity') ?? 'log';

        if (!isset(LogSeverities::SEVERITIES[$severity])) {
            $this->getPlugin()->formatAndSend($sender, 'error.unknown-log-severity', ['severity' => $severity]);
            return false;
        }

        $this->getPlugin()->getLogManager()->log($message, LogSeverities::SEVERITIES[$severity]);

        $this->getPlugin()->formatAndSend($sender, 'success.log', ['message' => $message]);
        return true;
    }

}