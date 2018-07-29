<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\Queries;
use pocketmine\command\CommandSender;

class IpCommand extends CucumberCommand
{
    
    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'ip', 'cucumber.command.ip', 'Get the a player\'s IP',
            1, '/ip <player>');
    }
    
    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name] = $command->get([0]);

        $this->getPlugin()->getConnector()->executeSelect(Queries::CUCUMBER_GET_PLAYER_BY_NAME,
            ['name' => $target_name],
            function(array $rows) use ($sender, $target_name) {
                if (count($rows) < 0)
                    $this->getPlugin()->formatAndSend($sender, 'error.player-does-not-exist', ['player' => $target_name]);

                $this->getPlugin()->formatAndSend($sender, 'success.ip', ['ip' => $rows[0]['ip']]);
            }
        );

        return true;
    }

}