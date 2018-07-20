<?php
declare(strict_types=1);

namespace cucumber\command;

use cucumber\Cucumber;
use cucumber\utils\CPlayer;
use cucumber\utils\MessageFactory;
use pocketmine\command\CommandSender;

/**
 * Sends a raw private message to a player
 */
class RawtellCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'rawtell', 'cucumber.command.rawtell', 'Send a raw message to a player',
            1, '/rawtell <player> <message> [-nom] [-p] [-t]', [
                'nom' => 0,
                'p' => 0,
                't' => 0
            ]);
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $message] = $command->get([0, [1, -1]]);
        $message = MessageFactory::colorize($message);

        if (is_null($target = CPlayer::getOnlinePlayer($target_name))) {
            $sender->sendMessage(
                MessageFactory::colorize(
                    MessageFactory::format($this->getPlugin()->getMessage(
                        'error.target-offline'),
                        ['player' => $target_name]
                    )
                )
            );
            return false;
        }

        if (is_null($command->getTag('nom')))
            $target->sendMessage($message);

        if (!is_null($command->getTag('p')))
            $target->sendPopup($message);

        if (!is_null($command->getTag('t')))
            $target->addSubTitle($message); // title is too big

        $sender->sendMessage(
            MessageFactory::format(
                $this->getPlugin()->getMessage('success.rawtell'),
                ['player' => $target_name, 'message' => $message]
            )
        );

        return true;
    }

}