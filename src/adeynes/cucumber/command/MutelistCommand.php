<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\Queries;
use pocketmine\command\CommandSender;

class MutelistCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'mutelist', 'cucumber.command.mutelist', 'See the list of mutes',
            0, '/mutelist');
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        $display_mutes = function(array $rows) use ($sender) {
            $message = '';
            foreach ($rows as $row)
                $message .= $this->getPlugin()->formatMessageFromConfig('success.mutelist.list', $row);

            $this->getPlugin()->formatAndSend($sender, 'success.mutelist.intro', ['count' => count($rows)]);
            $sender->sendMessage(trim($message));
        };

        $this->getPlugin()->getConnector()->executeSelect(Queries::CUCUMBER_GET_PUNISHMENTS_MUTES, [],
            $display_mutes);

        return true;
    }

}