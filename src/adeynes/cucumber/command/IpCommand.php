<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\cucumber\utils\Queries;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class IpCommand extends CucumberCommand
{
    
    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'ip',
            'cucumber.command.ip',
            'Get the a player\'s IP',
            '/ip <player>'
        );
    }
    
    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name] = $command->get(['player']);

        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PLAYER_BY_NAME,
            ['name' => $target_name],
            function (array $rows) use ($sender, $target_name) {
                if (count($rows) < 1) {
                    $this->getPlugin()->formatAndSend($sender, 'error.player-does-not-exist', ['player' => $target_name]);
                    return;
                }

                $ip = $rows[0]['ip'];

                $this->getPlugin()->formatAndSend($sender, 'success.ip.ip', ['player' => $target_name, 'ip' => $ip]);

                $this->getPlugin()->getConnector()->executeSelect(
                    Queries::CUCUMBER_GET_PLAYER_BY_IP,
                    ['ip' => $ip],
                    function (array $rows) use ($sender, $target_name, $ip) {
                        $alts = [];
                        foreach ($rows as $row) {
                            $color = CucumberPlayer::getOnlinePlayer($row['name']) instanceof Player ? '&a' : '&c';
                            $alts[] = $color . $row['name'];
                        }
                        $this->getPlugin()->formatAndSend(
                            $sender,
                            'success.ip.alts',
                            ['player' => $target_name, 'ip' => $ip, 'alts' => implode('&7,', $alts)]
                        );
                    }
                );
            }
        );

        return true;
    }

}