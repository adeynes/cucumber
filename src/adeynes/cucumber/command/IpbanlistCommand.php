<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\IpBan;
use adeynes\cucumber\utils\Queries;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use pocketmine\command\CommandSender;

class IpbanlistCommand extends PunishmentListCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'ipbanlist',
            'cucumber.command.ipbanlist',
            'See the list of IP bans',
            '/ipbanlist'
        );
    }

    public function getSelectQuery(): string
    {
        return Queries::CUCUMBER_GET_PUNISHMENTS_IP_BANS_LIMITED;
    }

    /**
     * @param array $ip_ban_row The database representation of an IP ban
     * @return string
     */
    protected function makeIpBanInfoLine(array $ip_ban_row): string {
        return $this->getPlugin()->formatMessageFromConfig(
            'success.ipbanlist.list',
            IpBan::from($ip_ban_row)->getDataFormatted()
        );
    }

    protected function sendList(CommandSender $sender, array $ip_bans, int $page_number): void {
        $page = trim(
            $this->getPlugin()->formatMessageFromConfig(
                'success.ipbanlist.intro',
                ['page' => $page_number]
            ) . PHP_EOL .
            implode(PHP_EOL, array_map([$this, 'makeIpBanInfoLine'], $ip_bans))
        );
        $sender->sendMessage($page);
    }

}