<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\Queries;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

class DelwarnCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'delwarn',
            'cucumber.command.delwarn',
            'Delete a specific warning',
            '/delwarn <id>'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$id_str] = $command->get(['id']);

        if (!is_numeric($id_str)) {
            $this->getPlugin()->formatAndSend($sender, 'error.invalid-argument', ['argument' => $id_str]);
            return false;
        }

        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_WARNINGS_BY_ID,
            ['id' => intval($id_str)],
            function (array $rows) use ($sender, $id_str) {
                if (count($rows) === 0) {
                    $this->getPlugin()->formatAndSend($sender, 'error.warning.does-not-exist', ['id' => $id_str]);
                    return;
                }

                $player = $rows[0]['player_name'];
                $this->getPlugin()->getConnector()->executeChange(
                    Queries::CUCUMBER_PUNISH_DELWARN,
                    ['id' => intval($id_str)],
                    function (int $affected_rows) use ($sender, $id_str, $player) {
                        $this->getPlugin()->formatAndSend($sender, 'success.delwarn', ['player' => $player, 'id' => $id_str]);
                    }
                );
            }
        );

        return true;
    }

}