<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

class IppardonCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'ippardon',
            'cucumber.command.ippardon',
            'Pardon an IP',
            '/ippardon <ip>'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$ip] = $command->get(['ip']);

        try {
            $this->getPlugin()->getPunishmentRegistry()->removeIpBan($ip);

            $this->getPlugin()->formatAndSend($sender, 'success.ippardon', ['ip' => $ip]);
            return true;
        } catch (CucumberException $exception) {
            $sender->sendMessage($exception->getMessage());
            return false;
        }
    }

}