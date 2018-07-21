<?php
declare(strict_types=1);

namespace cucumber\command;

use cucumber\Cucumber;
use cucumber\utils\CucumberException;
use cucumber\utils\MessageFactory;
use pocketmine\command\CommandSender;

class PardonCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'pardon', 'cucumber.command.pardon', 'Pardon a player (undo a /ban)',
            1, '/pardon <player>');
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name] = $command->get([0]);

        try {
            $this->getPlugin()->getPunishmentManager()->unban($target_name);
            $this->formatAndSend($sender, 'success.pardon', ['player' => $target_name]);
            return true;
        } catch (CucumberException $exception) {
            $sender->sendMessage($exception->getMessage());
            return false;
        }
    }

}