<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\Queries;
use adeynes\parsecmd\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

abstract class CucumberCommand extends Command
{

    public const PERMANENT_DURATION_STRINGS = ["inf", "infinite", "perm", "permanent", "-1"];

    /**
     * @return Cucumber
     */
    public function getPlugin(): Plugin
    {
        return parent::getPlugin();
    }

    /**
     * Checks if a player exists in the database. If so,
     * run the given callable. If not, send an error message
     * @param callable $function
     * @param CommandSender $sender
     * @param string $target_name
     */
    public function doIfTargetExists(callable $function, CommandSender $sender, string $target_name): void
    {
        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PLAYER_BY_NAME,
            ['name' => $target_name],
            function (array $rows) use ($function, $sender, $target_name) {
                if (count($rows) === 0) {
                    $this->getPlugin()->formatAndSend(
                        $sender,
                        'error.player-does-not-exist',
                        ['player' => $target_name]
                    );
                    return;
                }

                $function($rows);
            }
        );
    }

}