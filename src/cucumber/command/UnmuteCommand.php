<?php
declare(strict_types=1);

namespace cucumber\command;

use cucumber\Cucumber;
use cucumber\utils\CucumberException;
use cucumber\utils\CucumberPlayer;
use pocketmine\command\CommandSender;

class UnmuteCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'unmute', 'cucumber.command.unmute', 'Unmute a player',
            1, '/unmute <player>');
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name] = $command->get([0]);

        try {
            $this->getPlugin()->getPunishmentManager()->unmute($target_name);

            if ($target = CucumberPlayer::getOnlinePlayer($target_name))
                $this->formatAndSend($target, 'moderation.mute.unmute.manual');

            $this->formatAndSend($sender, 'success.unmute', ['player' => $target_name]);
            return true;
        } catch (CucumberException $exception) {
            $sender->sendMessage($exception->getMessage());
            return false;
        }
    }

}