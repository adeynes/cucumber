<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\Ban;
use adeynes\cucumber\utils\Queries;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
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
        $limit = (int) $this->getPlugin()->getMessage('success.banlist.entries-per-line');
        [$page] = $command->get(['page']);
        $page = $page ?? 0;

        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_BANS_LIMITED,
            ['from' => ($page - 1) * $limit, 'limit' => $limit],
            function (array $rows) use ($sender, $page) {
                $this->sendBanlist($sender, $rows, $page);
            }
        );

        return true;
    }

    /**
     * @param array $ban_row The database representation of a ban
     * @return string
     */
    protected function makeBanInfoLine(array $ban_row): string {
        return $this->getPlugin()->formatMessageFromConfig(
            'success.banlist.list',
            Ban::from($ban_row)->getDataFormatted()
        );
    }

    protected function sendBanlist(CommandSender $sender, array $bans, int $page_number) {
        $page = trim(
            $this->getPlugin()->formatMessageFromConfig(
                'success.banlist.intro',
                ['page' => $page_number]
            ) . PHP_EOL .
            implode(PHP_EOL, array_map([$this, 'makeBanInfoLine'], $bans))
        );
        $sender->sendMessage($page);
    }
}