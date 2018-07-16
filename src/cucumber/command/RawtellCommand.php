<?php
declare(strict_types=1);

namespace src\cucumber\command;

use cucumber\Cucumber;
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
        $target = $this->plugin->getServer()->getPlayer($target_name);
        if (is_null($target) || !$target->isOnline())
            $target->sendMessage($message);

        return true;
    }

}