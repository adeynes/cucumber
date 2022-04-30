<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\UBan;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

class UbanCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'uban',
            'cucumber.command.uban',
            'Ban any player that joins using an IP. Irreversible',
            '/uban <player>|<ip> [reason]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target, $reason] = $command->get(['target', 'reason']);
        if ($reason === null) {
            $reason = $this->getPlugin()->getMessage('moderation.ban.default-reason');
        }

        $uban = function(string $ip) use ($sender, $reason) {
            try {
                $uban = new UBan($ip, $reason, $sender->getName(), time());
                $uban_data = $uban->getFormatData();
                $this->getPlugin()->getPunishmentRegistry()->addUBan($uban);
                $uban->save($this->getPlugin()->getConnector());

                foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
                    if ($this->getPlugin()->getPunishmentRegistry()->getUBanChecker()->checkFor($player)) {
                        $player->kick(
                            $this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $uban_data),
                            false // don't say Kicked by admin
                        );
                    }
                }

                $this->getPlugin()->formatAndSend($sender, 'success.uban', $uban_data);
                return true;
            } catch (CucumberException $exception) {
                $sender->sendMessage($exception->getMessage());
                return false;
            }
        };

        $ip_matches = [];
        preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $target, $ip_matches);
        if ($ip_matches === []) {
            if ($player = CucumberPlayer::getOnlinePlayer($target)) {
                $uban($player->getNetworkSession()->getIp());
            } else {
                $this->doIfTargetExists(
                    function (array $rows) use ($uban) {
                        $uban($rows[0]['ip']);
                    },
                    $sender,
                    $target
                );
            }
        } else {
            $uban($ip_matches[0]);
        }

        /*if ($target_name) {
            if ($target = CucumberPlayer::getOnlinePlayer($target_name)) {
                $ip_ban($target->getAddress());
            } else {
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
        }*/

        return true;
    }

}