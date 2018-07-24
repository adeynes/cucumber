<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\cucumber\utils\MessageFactory;
use pocketmine\command\CommandSender;

class IpbanCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'ipban', 'cucumber.command.ipban', 'Ban an IP', 0,
            '/ipban <-p <player>|-ip <ip>> [reason]', [
                'p' => 1,
                'ip' => 1
            ]);
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$reason] = $command->get([[0, -1]]);
        [$target_name, $ip] = [$command->getTag('p'), $command->getTag('ip')];
        $duration = $command->getTag('d');
        $expiration = $duration ? CommandParser::parseDuration($duration) : null;

        $ip_ban = function(string $ip) use ($sender, $reason, $expiration) {
            try {
                $ban_data = $this->getPlugin()->getPunishmentManager()
                        ->ipBan($ip, $reason, $expiration, $sender->getName())
                        ->getDataFormatted($this->getPlugin()->getMessage('moderation.ban.default-reason'));
                $ban_data = $ban_data + ['ip' => $ip];

                foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
                    if ($player->getAddress() === $ip)
                        $player->kick($this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $ban_data));
                }

                $this->getPlugin()->formatAndSend($sender, 'success.ipban', $ban_data);

                return true;
            } catch (CucumberException $exception) {
                $sender->sendMessage($exception->getMessage());
                return false;
            }
        };

        if ($target_name) {
            if ($target = CucumberPlayer::getOnlinePlayer($target_name))
                $ip_ban($target->getAddress());
            else
                $this->getPlugin()->formatAndSend($sender, 'error.player-offline', ['player' => $target_name]);
                // don't return in case ip flag is set
        }

        if ($ip)
            $ip_ban($ip);

        if (!$target_name && !$ip)
            $sender->sendMessage(
                MessageFactory::colorize("&cAt least one of flag &b-p &cand flag &b-ip must be set!")
            );

        return true;
    }

}