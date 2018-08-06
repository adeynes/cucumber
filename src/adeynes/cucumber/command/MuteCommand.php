<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\parsecmd\CommandParser;
use adeynes\parsecmd\ParsedCommand;
use pocketmine\command\CommandSender;

class MuteCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct(
            $plugin,
            'mute',
            'cucumber.command.mute',
            'Mute a player',
            1,
            '/mute <player> [reason] [-d <duration>]',
            ['d' => 1]
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $reason] = $command->get([0, [1, -1]]);
        $target_name = strtolower($target_name);
        if ($reason === '') $reason = null;
        $duration = $command->getTag('d');
        $expiration = $duration ? CommandParser::parseDuration($duration) : null;

        $mute = function () use ($sender, $target_name, $reason, $expiration) {
            try {
                $mute_data = $this->getPlugin()->getPunishmentManager()
                    ->mute($target_name, $reason, $expiration, $sender->getName())
                    ->getDataFormatted();
                $mute_data = $mute_data + ['player' => $target_name];

                if ($target = CucumberPlayer::getOnlinePlayer($target_name)) {
                    $this->getPlugin()->formatAndSend($target, 'moderation.mute.mute.message', $mute_data);
                }

                $this->getPlugin()->formatAndSend($sender, 'success.mute', $mute_data);
                return true;
            } catch(CucumberException $exception) {
                $sender->sendMessage($exception->getMessage());
                return false;
            }
        };

        $this->doIfTargetExists($mute, $sender, $target_name);
        return true;
    }

}