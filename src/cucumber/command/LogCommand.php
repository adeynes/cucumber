<?php
declare(strict_types=1);

namespace src\cucumber\command;

use cucumber\Cucumber;
use pocketmine\command\CommandSender;

class LogCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'log', 'cucumber.command.log', 'Log a message',
            '/log <message>');
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$message] = $command->get([0, -1]);
        $this->getPlugin()->getLogManager()->log($message);
        return true;
    }

}