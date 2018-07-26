<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use pocketmine\command\CommandSender;

class PardonCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'pardon', 'cucumber.command.pardon', 'Pardon a player',
            1, '/pardon <player>');
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name] = $command->get([0]);
        $target_name = strtolower($target_name);

        try {
            $this->getPlugin()->getPunishmentManager()->unban($target_name);
            $this->getPlugin()->formatAndSend($sender, 'success.pardon', ['player' => $target_name]);
            return true;
        } catch (CucumberException $exception) {
            $sender->sendMessage($exception->getMessage());
            return false;
        }
    }

}