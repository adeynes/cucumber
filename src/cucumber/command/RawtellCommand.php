<?php

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
        parent::__construct($plugin, 'rawtell', 'Send a raw message to a player', '/rawtell <player> <message>');
        $this->setPermission('cucumber.command.rawtell');
    }

    public function _execute(CommandSender $sender, string $label, array $args): bool
    {
        $target = $this->plugin->getServer()->getPlayer(array_shift($args));
        if ($target instanceof Player && $target->isOnline()) {
            $target->sendMessage(TextFormat::colorize(trim(implode(' ', $args))));
        }
    }

}