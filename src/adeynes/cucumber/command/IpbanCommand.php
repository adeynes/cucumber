<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\cucumber\utils\MessageFactory;
use adeynes\parsecmd\CommandBlueprint;
use adeynes\parsecmd\CommandParser;
use adeynes\parsecmd\ParsedCommand;
use pocketmine\command\CommandSender;

// TODO: ban offline player by getting IP from db
class IpbanCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'ipban',
            'cucumber.command.ipban',
            'Ban an IP',
            '/ipban <-p <player>|-ip <ip>> [reason]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$reason] = $command->get(['reason']);
        [$target_name, $ip] = [$command->getFlag('player'), $command->getFlag('ip')];
        if ($reason === '') $reason = null;
        $duration = $command->getFlag('duration');
        $expiration = $duration ? CommandParser::parseDuration($duration) : null;

        $ip_ban = function (string $ip) use ($sender, $reason, $expiration) {
            try {
                $ban_data = $this->getPlugin()->getPunishmentManager()
                    ->ipBan($ip, $reason, $expiration, $sender->getName())
                    ->getDataFormatted();
                $ban_data = $ban_data + ['ip' => $ip];

                foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
                    if ($player->getAddress() === $ip) {
                        $player->kick(
                            $this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $ban_data),
                            false // don't say Kicked by admin
                        );
                    }
                }

                $this->getPlugin()->formatAndSend($sender, 'success.ipban', $ban_data);
                return true;
            } catch (CucumberException $exception) {
                $sender->sendMessage($exception->getMessage());
                return false;
            }
        };

        if ($target_name) {
            if ($target = CucumberPlayer::getOnlinePlayer($target_name)) {
                $ip_ban($target->getAddress());
            }
            else {
                $this->doIfTargetExists(
                    function (array $rows) use ($ip_ban) {
                        $ip_ban($rows[0]['ip']);
                    },
                    $sender,
                    $target_name
                );
            }
            // don't return in case ip flag is set
        }

        if ($ip) {
            $ip_ban($ip);
        }

        if (!$target_name && !$ip) {
            $sender->sendMessage(
                MessageFactory::colorize("&cAt least one of flag &b-p &cand flag &b-ip&c must be set!")
            );
        }

        return true;
    }

}