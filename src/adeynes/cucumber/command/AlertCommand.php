<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\MessageFactory;
use pocketmine\command\CommandSender;

class AlertCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'alert', 'cucumber.command.alert', 'Broadcast a message to the server',
            1, '/alert <message> [-nom] [-p] [-t]', [
                'nom' => 0,
                'p' => 0,
                't' => 0
            ]);
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$message] = $command->get([[0, -1]]);
        $message = MessageFactory::colorize($message);
        $server = $this->getPlugin()->getServer();

        // Can't use ! because empty string evals to false
        if (is_null($command->getTag('nom')))
            $server->broadcastMessage($message);

        if (!is_null($command->getTag('p')))
            $server->broadcastPopup($message);

        if (!is_null($command->getTag('t')))
            $server->broadcastTitle('', $message); // broadcast a subtitle

        $this->getPlugin()->formatAndSend($sender, 'success.alert', ['message' => $message]);

        return true;
    }

}