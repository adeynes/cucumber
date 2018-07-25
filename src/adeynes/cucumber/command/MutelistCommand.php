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
        $message = '';
        $mutes = $this->getPlugin()->getPunishmentManager()->getMutes();
        foreach ($mutes as $player => $mute) {
            $data = ['player' => $player] +
                $mute->getDataFormatted($this->getPlugin()->getMessage('moderation.mute.mute.default-reason'));
            $message .= $this->getPlugin()->formatMessageFromConfig('success.mutelist.list', $data);
        }

        $this->getPlugin()->formatAndSend($sender, 'success.mutelist.intro', ['count' => count($mutes)]);
        $sender->sendMessage(trim($message));

        return true;
    }

}