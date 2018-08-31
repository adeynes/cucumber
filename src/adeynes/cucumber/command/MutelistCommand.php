<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

class MutelistCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'mutelist',
            'cucumber.command.mutelist',
            'See the list of mutes',
            '/mutelist'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        $message = '';
        $mutes = $this->getPlugin()->getPunishmentManager()->getMutes();
        foreach ($mutes as $player => $mute) {
            $data = $mute->getDataFormatted() + ['player' => $player];
            $message .= $this->getPlugin()->formatMessageFromConfig('success.mutelist.list', $data);
        }

        $this->getPlugin()->formatAndSend($sender, 'success.mutelist.intro', ['count' => count($mutes)]);
        $sender->sendMessage(trim($message));

        return true;
    }

}