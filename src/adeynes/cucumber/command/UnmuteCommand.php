<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\parsecmd\CommandBlueprint;
use adeynes\parsecmd\ParsedCommand;
use pocketmine\command\CommandSender;

class UnmuteCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'unmute',
            'cucumber.command.unmute',
            'Unmute a player',
            '/unmute <player>'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name] = $command->get(['player']);
        $target_name = strtolower($target_name);

        try {
            $this->getPlugin()->getPunishmentManager()->unmute($target_name);

            if ($target = CucumberPlayer::getOnlinePlayer($target_name)) {
                $this->getPlugin()->formatAndSend(
                    $target,
                    'moderation.mute.unmute.manual',
                    ['moderator' => $sender->getName()]
                );
            }

            $this->getPlugin()->formatAndSend($sender, 'success.unmute', ['player' => $target_name]);
            return true;
        } catch (CucumberException $exception) {
            $sender->sendMessage($exception->getMessage());
            return false;
        }
    }

}