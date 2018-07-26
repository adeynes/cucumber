<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use pocketmine\command\CommandSender;

class BanCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'ban', 'cucumber.command.ban', 'Ban a player by name',
            1, '/ban <player> [reason] [-d <duration>]', [
                'd' => 1
            ]);
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $reason] = $command->get([0, [1, -1]]);
        $target_name = strtolower($target_name);
        if ($reason === '') $reason = null;
        $duration = $command->getTag('d');
        $expiration = $duration ? CommandParser::parseDuration($duration) : null;

        $ban = function() use ($sender, $target_name, $reason, $expiration) {
            try {
                $ban_data = $this->getPlugin()->getPunishmentManager()
                    ->ban($target_name, $reason, $expiration, $sender->getName())->getDataFormatted();
                $ban_data = $ban_data + ['player' => $target_name];

                if ($target = CucumberPlayer::getOnlinePlayer($target_name))
                    $target->kick($this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $ban_data));

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