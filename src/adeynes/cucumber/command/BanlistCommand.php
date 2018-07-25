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
        $message = '';
        $bans = $this->getPlugin()->getPunishmentManager()->getBans();
        foreach ($bans as $player => $ban) {
            $data = ['player' => $player] +
                $ban->getDataFormatted($this->getPlugin()->getMessage('moderation.ban.default-reason'));
            $message .= $this->getPlugin()->formatMessageFromConfig('success.banlist.list', $data);
        }

        $this->getPlugin()->formatAndSend($sender, 'success.banlist.intro', ['count' => count($bans)]);
        $sender->sendMessage(trim($message));

        return true;
    }

}