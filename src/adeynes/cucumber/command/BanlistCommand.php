<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\parsecmd\CommandBlueprint;
use adeynes\parsecmd\ParsedCommand;
use pocketmine\command\CommandSender;

class BanlistCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'banlist',
            'cucumber.command.banlist',
            'See the list of bans',
            '/banlist'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        $message = '';
        $bans = $this->getPlugin()->getPunishmentManager()->getBans();
        foreach ($bans as $player => $ban) {
            $data = $ban->getDataFormatted() + ['player' => $player];
            $message .= $this->getPlugin()->formatMessageFromConfig('success.banlist.list', $data);
        }

        $this->getPlugin()->formatAndSend($sender, 'success.banlist.intro', ['count' => count($bans)]);
        $sender->sendMessage(trim($message));

        return true;
    }

}