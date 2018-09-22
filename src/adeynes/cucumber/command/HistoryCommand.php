<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\Ban;
use adeynes\cucumber\mod\IpBan;
use adeynes\cucumber\mod\Mute;
use adeynes\cucumber\mod\SimplePunishment;
use adeynes\cucumber\utils\Queries;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

class HistoryCommand extends CucumberCommand
{

    protected const PUNISHMENT_LIST_PATHS = [
        Ban::class => 'success.banlist.list',
        IpBan::class => 'success.ipbanlist.list',
        Mute::class => 'success.mutelist.list'
    ];

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'history',
            'cucumber.command.history',
            'See a player\'s punishment history',
            '/history <player>'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        $player = strtolower($command->get(['player'])[0]);

        $processAndSendHistory = function (array $player_rows) use ($sender) {
            $player = $player_rows[0];
            /** @var SimplePunishment[] $history */
            $history = [];

            $this->addBansToHistory(
                $player['name'],
                $history,
                function (array &$history) use ($player, $sender) {
                    $this->addIpBansToHistory(
                        $player['ip'],
                        $history,
                        function (array &$history) use ($player, $sender) {
                            $this->addMutesToHistory(
                                $player['name'],
                                $history,
                                function (array $history) use ($sender) {
                                    $this->showHistory($sender, $history);
                                }
                            );
                        }
                    );
                }
            );
        };

        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PLAYER_BY_NAME,
            ['name' => $player],
            $processAndSendHistory
        );

        return true;
    }

    /**
     * @param CommandSender $sender
     * @param SimplePunishment[] $history
     */
    protected function showHistory(CommandSender $sender, array $history) {
        usort(
            $history,
            function (SimplePunishment $a, SimplePunishment $b) {
                return ($a->getExpiration() <=> $b->getExpiration()) * -1; // desc order
            }
        );

        foreach ($history as $punishment) {
            $this->getPlugin()->formatAndSend(
                $sender,
                self::PUNISHMENT_LIST_PATHS[get_class($punishment)],
                $punishment->getDataFormatted()
            );
        }
    }

    protected function addBansToHistory(string $player, array &$history, ?callable $next): void
    {
        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_BANS_BY_PLAYER,
            ['player' => $player],
            function (array $rows) use (&$history, $next) {
                foreach ($rows as $row) {
                    $history[] = Ban::from($row);
                }

                if (!is_null($next)) $next($history);
            }
        );
    }

    protected function addIpBansToHistory(string $ip, array &$history, ?callable $next): void
    {
        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_IP_BANS_BY_IP,
            ['ip' => $ip],
            function (array $rows) use (&$history, $next) {
                foreach ($rows as $row) {
                    $history[] = IpBan::from($row);
                }

                if (!is_null($next)) $next($history);
            }
        );
    }

    protected function addMutesToHistory(string $player, array &$history, ?callable $next): void
    {
        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_MUTES_BY_PLAYER,
            ['player' => $player],
            function (array $rows) use (&$history, $next) {
                foreach ($rows as $row) {
                    $history[] = Mute::from($row);
                }

                if (!is_null($next)) $next($history);
            }
        );
    }

}