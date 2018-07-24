<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\Queries;
use pocketmine\command\CommandSender;

class BanlistCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'banlist', 'cucumber.command.banlist', 'See the list of bans',
            0, '/banlist');
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        $display_bans = function(array $rows) use ($sender) {
            $message = '';
            foreach ($rows as $row)
                $message .= $this->getPlugin()->formatMessageFromConfig('success.banlist.list', $row);

            $this->getPlugin()->formatAndSend($sender, 'success.banlist.intro', ['count' => count($rows)]);
            $sender->sendMessage(trim($message));
        };

        $this->getPlugin()->getConnector()->executeSelect(Queries::CUCUMBER_GET_PUNISHMENTS_BANS, [],
            $display_bans);

        return true;
    }

}