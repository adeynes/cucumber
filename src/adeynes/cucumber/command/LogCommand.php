<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\log\LogSeverity;
use adeynes\cucumber\utils\CucumberException;
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
            '/log <message> [-severity|-s <severity>]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$message] = $command->get(['message']);
        $severity_str = $command->getFlag('severity') ?? 'log';

        try {
            $severity = LogSeverity::fromString($severity_str);
        } catch (CucumberException $exception) {
            $this->getPlugin()->formatAndSend($sender, 'error.unknown-log-severity', ['severity' => $severity_str]);
            return false;
        }

        $this->getPlugin()->getLogDispatcher()->log($message, $severity);

        $this->getPlugin()->formatAndSend($sender, 'success.log', ['message' => $message, 'severity' => $severity_str]);
        return true;
    }

}