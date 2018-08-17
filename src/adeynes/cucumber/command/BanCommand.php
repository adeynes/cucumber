<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\parsecmd\CommandBlueprint;
use adeynes\parsecmd\CommandParser;
use adeynes\parsecmd\ParsedCommand;
use pocketmine\command\CommandSender;

class BanCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'ban',
            'cucumber.command.ban',
            'Ban a player by name',
            '/ban <player> [reason] [-d <duration>]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $reason] = $command->get(['player', 'reason']);
        $target_name = strtolower($target_name);
        if ($reason === '') $reason = null;
        $duration = $command->getFlag('duration');
        $expiration = $duration ? CommandParser::parseDuration($duration) : null;

        $ban = function () use ($sender, $target_name, $reason, $expiration) {
            try {
                $ban_data = $this->getPlugin()->getPunishmentManager()
                    ->ban($target_name, $reason, $expiration, $sender->getName())
                    ->getDataFormatted();
                $ban_data = $ban_data + ['player' => $target_name];

                if ($target = CucumberPlayer::getOnlinePlayer($target_name)) {
                    $target->kick(
                        $this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $ban_data),
                        false // don't say Kicked by admin
                    );
                }

                $this->getPlugin()->formatAndSend($sender, 'success.ban', $ban_data);
                return true;
            } catch(CucumberException $exception) {
                $sender->sendMessage($exception->getMessage());
                return false;
            }
        };

        $this->doIfTargetExists($ban, $sender, $target_name);
        return true;
    }

}