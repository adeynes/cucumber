<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\cucumber\utils\MessageFactory;
use pocketmine\command\CommandSender;

// TODO: ban offline player by getting IP from db
class UbanCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'uban', 'cucumber.command.uban', 'Ban any player that joins using an IP. Irreversible',
            0, '/uban <-p <player>|-ip <ip>> [reason]', [
                'p' => 1,
                'ip' => 1
            ]);
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$reason] = $command->get([[0, -1]]);
        [$target_name, $ip] = [$command->getTag('p'), $command->getTag('ip')];
        if ($reason === '') $reason = null;

        $uban = function(string $ip) use ($sender, $reason) {
            try {
                $ban_data = $this->getPlugin()->getPunishmentManager()
                    ->addUban($ip, $reason, $sender->getName())->getDataFormatted();
                $ban_data = $ban_data + ['ip' => $ip];

                foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
                    if ($this->getPlugin()->getPunishmentManager()->checkUban(new CucumberPlayer($player)))
                        $player->kick(
                            $this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $ban_data),
                            false // don't say Kicked by admin
                        );
                }

                $this->getPlugin()->formatAndSend($sender, 'success.uban', $ban_data);

                return true;
            } catch (CucumberException $exception) {
                $sender->sendMessage($exception->getMessage());
                return false;
            }
        };

        if ($target_name) {
            if ($target = CucumberPlayer::getOnlinePlayer($target_name))
                $uban($target->getAddress());
            else
                $this->getPlugin()->formatAndSend($sender, 'error.player-offline', ['player' => $target_name]);
            // don't return in case ip flag is set
        }

        if ($ip)
            $uban($ip);

        if (!$target_name && !$ip)
            $sender->sendMessage(
                MessageFactory::colorize("&cAt least one of flag &b-p &cand flag &b-ip&c must be set!")
            );

        return true;
    }

}