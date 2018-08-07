<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\cucumber\utils\MessageFactory;
use adeynes\parsecmd\ParsedCommand;
use pocketmine\command\CommandSender;

class RawtellCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct(
            $plugin,
            'rawtell',
            'cucumber.command.rawtell',
            'Send a raw message to a player',
            2,
            '/rawtell <player> <message> [-nom] [-p] [-t]',
            ['nom' => 0, 'nomessage' => 0, 'p' => 0, 'popup' => 0, 't' => 0, 'title' => 0]
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $message] = $command->get([0, [1, -1]]);
        $message = MessageFactory::colorize($message);

        if (!$target = CucumberPlayer::getOnlinePlayer($target_name)) {
            $this->getPlugin()->formatAndSend($sender, 'error.player-offline', ['player' => $target_name]);
            return false;
        }

        if (is_null($command->getTag('nom')) && is_null($command->getTag('nomessage'))) {
            $target->sendMessage($message);
        }

        if (!is_null($command->getTag('p')) || !is_null($command->getTag('popup'))) {
            $target->sendPopup($message);
        }

        if (!is_null($command->getTag('t')) || !is_null($command->getTag('title'))) {
            $target->addSubTitle($message); // title is too big
        }

        $this->getPlugin()->formatAndSend(
            $sender,
            'success.rawtell',
            ['player' => $target_name, 'message' => $message]
        );
        return true;
    }

}