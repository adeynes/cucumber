<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\parsecmd\ParsedCommand;
use pocketmine\command\CommandSender;

class IppardonCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct(
            $plugin,
            'ippardon',
            'cucumber.command.ippardon',
            'Pardon an IP',
            1,
            '/ippardon <ip>'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$ip] = $command->get([0]);

        try {
            $this->getPlugin()->getPunishmentManager()->ipUnban($ip);

            $this->getPlugin()->formatAndSend($sender, 'success.ippardon', ['ip' => $ip]);
            return true;
        } catch (CucumberException $exception) {
            $sender->sendMessage($exception->getMessage());
            return false;
        }
    }

}