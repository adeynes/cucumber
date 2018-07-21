<?php
declare(strict_types=1);

namespace cucumber\command;

use cucumber\Cucumber;
use cucumber\utils\CucumberException;
use cucumber\utils\CucumberPlayer;
use cucumber\utils\MessageFactory;
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
        $duration = $command->getTag('d');
        $expiration = $duration ? CommandParser::parseDuration($duration) : null;

        try {
            $ban_data = $this->getPlugin()->getPunishmentManager()
                ->ban($target_name, $reason, $expiration, $sender->getName())
                ->getDataFormatted($this->getPlugin()->getMessage('ban.ban.reason.default-reason'));
            $ban_data = $ban_data + ['player' => $target_name, 'moderator' => $sender->getName()];

            if ($target = CucumberPlayer::getOnlinePlayer($target_name))
                $target->kick($this->formatMessage('moderation.ban.reason.message', $ban_data));

            $this->formatAndSend($sender, 'success.ban', $ban_data);
            return true;
        } catch(CucumberException $exception) {
            $sender->sendMessage($exception->getMessage());
            return false;
        }
    }

}