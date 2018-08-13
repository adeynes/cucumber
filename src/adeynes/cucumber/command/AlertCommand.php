<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\MessageFactory;
use adeynes\parsecmd\CommandBlueprint;
use adeynes\parsecmd\ParsedCommand;
use pocketmine\command\CommandSender;

class AlertCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'alert',
            'cucumber.command.alert',
            'Broadcast a message to the server',
            '/alert <message> [-nomessage|-nom] [-popup|-p] [-title|-t]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$message] = $command->get(['message']);
        $message = MessageFactory::colorize($message);
        $server = $this->getPlugin()->getServer();

        // Can't use ! because empty string evals to false
        if (is_null($command->getFlag('nomessage')) && is_null($command->getFlag('nom'))) {
            $server->broadcastMessage($message);
        }

        if (!is_null($command->getFlag('popup')) || !is_null($command->getFlag('p'))) {
            $server->broadcastPopup($message);
        }

        if (!is_null($command->getFlag('title')) || !is_null($command->getFlag('t'))) {
            $server->broadcastTitle('', $message); // broadcast a subtitle, title is too big
        }

        $this->getPlugin()->formatAndSend($sender, 'success.alert', ['message' => $message]);
        return true;
    }

}