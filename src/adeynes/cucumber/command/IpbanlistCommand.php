<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use pocketmine\command\CommandSender;

class IpbanlistCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'ipbanlist', 'cucumber.command.ipbanlist', 'See the list of IP bans',
            0, '/ipbanlist');
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        $message = '';
        $ip_bans = $this->getPlugin()->getPunishmentManager()->getIpBans();
        foreach ($ip_bans as $ip => $ip_ban) {
            $data = ['ip'=> $ip] +
                $ip_ban->getDataFormatted();
            $message .= $this->getPlugin()->formatMessageFromConfig('success.ipbanlist.list', $data);
        }

        $this->getPlugin()->formatAndSend($sender, 'success.ipbanlist.intro', ['count' => count($ip_bans)]);
        $sender->sendMessage(trim($message));

        return true;
    }

}