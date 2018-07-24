<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\Queries;
use pocketmine\command\CommandSender;
use poggit\libasynql\result\SqlSelectResult;

class BanlistCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'banlist', 'cucumber.command.banlist', 'See the list of bans',
            0, '/banlist');
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        $display_bans = function(SqlSelectResult $result) use ($sender) {
            $message = '';
            foreach ($result->getRows() as $row)
                $message .= $this->getPlugin()->formatMessageFromConfig('success.banlist.list', $row);

            $this->getPlugin()->formatAndSend($sender, 'success.banlist.intro', ['count' => count($result->getRows())]);
            $sender->sendMessage(trim($message));
        };

        $this->getPlugin()->getConnector()->executeSelect(Queries::CUCUMBER_GET_PUNISHMENTS_BANS, [],
            $display_bans);

        return true;
    }

}