<?php
declare(strict_types=1);

namespace src\cucumber\command;

use cucumber\Cucumber;
use cucumber\utils\CPlayer;
use cucumber\utils\MessageFactory;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

/**
 * Sends a raw private message to a player
 */
class RawtellCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'rawtell', 'Send a raw message to a player',
            '/rawtell <player> <message>', [
                'nom' => 0,
                'p' => 0,
                't' => 0
            ]);
        $this->setPermission('cucumber.command.rawtell');
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $message] = $command->get([0, [1, -1]]);
        $message = MessageFactory::colorize($message);
        if (!is_null($target = CPlayer::getOnlinePlayer($target_name))) {
            $sender->sendMessage(
                MessageFactory::colorize(
                    MessageFactory::format($this->plugin->getMessage(
                        'error.target-offline'),
                        ['player' => $target_name]
                    )
                )
            );
            return true;
        }

        if (!is_null($command->getTag('nom')))
            $target->sendMessage($message);

        if (!is_null($command->getTag('p')))
            $target->sendPopup($message);

        if (!is_null($command->getTag('t')))
            $target->addSubTitle($message);

        return true;
    }

}