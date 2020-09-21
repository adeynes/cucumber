<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\Warning;
use adeynes\cucumber\utils\Queries;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

class WarningsCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'warnings',
            'cucumber.command.warnings',
            'See a player\'s warnings',
            '/warnings <player> [-all|-a]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        $player = strtolower($command->get(['player'])[0]);
        $extra_args = ['all' => $command->getFlag('all') !== null];

        $sendWarnings = function (array $player_rows) use ($sender, $extra_args) {
            $player_name = $player_rows[0]['name'];

            $this->getPlugin()->getConnector()->executeSelect(
                Queries::CUCUMBER_GET_PUNISHMENTS_WARNINGS_BY_PLAYER,
                ['player' => $player_name] + $extra_args,
                function (array $rows) use ($sender, $player_name) {
                    $intro = $this->getPlugin()->formatMessageFromConfig(
                        'success.warnings.intro',
                        ['player' => $player_name, 'count' => strval(count($rows))]
                    );
                    $lines = [$intro];
                    foreach ($rows as $row) {
                        $lines[] = $this->getPlugin()->formatMessageFromConfig(
                            'success.warnings.list',
                            Warning::from($row)->getFormatData() + ['id' => $row['warning_id']]
                        );
                    }

                    $sender->sendMessage(implode(PHP_EOL, $lines));
                }
            );
        };

        $this->doIfTargetExists($sendWarnings, $sender, $player);

        return true;
    }

}