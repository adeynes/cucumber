<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\Ban;
use adeynes\cucumber\utils\MessageFactory;
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
        $make_ban_info_line = function (array $ban) {
            return $this->getPlugin()->formatMessageFromConfig(
                'success.banlist.list',
                Ban::from($ban)->getDataFormatted()
            );
        };

        $entries_limit = (int) $this->getPlugin()->getMessage('success.banlist.entries-per-line');
        [$page] = $command->get(['page']);
        $page = $page ?? 0;

        $select_bans_and_send = function (int $count) use ($sender, $make_ban_info_line, $entries_limit, $page) {
            $this->getPlugin()->getConnector()->executeSelect(
                Queries::CUCUMBER_GET_PUNISHMENTS_BANS_LIMITED,
                ['limit' => $entries_limit * $page],
                function (array $rows) use ($sender, $make_ban_info_line, $entries_limit, $page, $count) {
                    $page = MessageFactory::makePage(
                        $rows,
                        $make_ban_info_line,
                        $this->getPlugin()->formatMessageFromConfig('success.banlist.intro', ['count' => $count]),
                        $entries_limit,
                        $page
                    );
                    $sender->sendMessage($page);
                }
            );
        };

        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_BANS_COUNT,
            [],
            function (array $rows) use ($select_bans_and_send) {
                $select_bans_and_send($rows[0]['count']);
            }
        );

        return true;
    }

}