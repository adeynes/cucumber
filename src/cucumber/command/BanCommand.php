<?php
declare(strict_types=1);

namespace cucumber\command;

use cucumber\Cucumber;
use cucumber\utils\CException;
use cucumber\utils\CPlayer;
use cucumber\utils\MessageFactory;
use pocketmine\command\CommandSender;

class BanCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'ban', 'cucumber.command.ban', 'Ban a player by unique identification (XUID)',
            1, '/ban <player> [reason] [-d <duration>]', [
                'd' => 1
            ]);
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $reason] = $command->get([0, [1, -1]]);
        $expiration = CommandParser::parseDuration($command->getTag('d') ?? '');

        try {
            $ban = $this->getPlugin()->getPunishmentManager()->ban($target_name, $reason, $expiration, $sender->getName());
            $ban_data = $ban->getData() + ['player' => $target_name];

            if (!is_null($target = CPlayer::getOnlinePlayer($target_name)))
                $target->kick(
                    MessageFactory::format($this->getPlugin()->getMessage('moderation.ban.reason'),
                        $ban_data + ['moderator' => $sender->getName()])
                );

            $sender->sendMessage(
                MessageFactory::format($this->getPlugin()->getMessage('success.ban'), $ban_data)
            );
        } catch(CException $exception) {
            $sender->sendMessage($exception->getMessage());
            return false;
        }

        return true;
    }

}