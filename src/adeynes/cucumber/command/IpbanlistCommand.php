<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\parsecmd\CommandBlueprint;
use adeynes\parsecmd\ParsedCommand;
use pocketmine\command\CommandSender;

class IpbanlistCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'ipbanlist',
            'cucumber.command.ipbanlist',
            'See the list of IP bans',
            '/ipbanlist'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        $message = '';
        $ip_bans = $this->getPlugin()->getPunishmentManager()->getIpBans();
        foreach ($ip_bans as $ip => $ip_ban) {
            $data = $ip_ban->getDataFormatted() + ['ip' => $ip];
            $message .= $this->getPlugin()->formatMessageFromConfig('success.ipbanlist.list', $data);
        }

        $this->getPlugin()->formatAndSend($sender, 'success.ipbanlist.intro', ['count' => count($ip_bans)]);
        $sender->sendMessage(trim($message));

        return true;
    }

}