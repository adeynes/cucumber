<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\cucumber\utils\MessageFactory;
use adeynes\parsecmd\CommandBlueprint;
use adeynes\parsecmd\ParsedCommand;
use pocketmine\command\CommandSender;

class RawtellCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'rawtell',
            'cucumber.command.rawtell',
            'Send a raw message to a player',
            '/rawtell <player> <message> [-nomessage|-nom] [-popup|-p] [-title|-t]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $message] = $command->get(['player', 'message']);
        $message = MessageFactory::colorize($message);

        if (!$target = CucumberPlayer::getOnlinePlayer($target_name)) {
            $this->getPlugin()->formatAndSend($sender, 'error.player-offline', ['player' => $target_name]);
            return false;
        }

        if (is_null($command->getFlag('nomessage'))) {
            $target->sendMessage($message);
        }

        if (!is_null($command->getFlag('popup'))) {
            $target->sendPopup($message);
        }

        if (!is_null($command->getFlag('title'))) {
            $target->addTitle('', $message); // title is too big
        }

        $this->getPlugin()->formatAndSend(
            $sender,
            'success.rawtell',
            ['player' => $target_name, 'message' => $message]
        );
        return true;
    }

}