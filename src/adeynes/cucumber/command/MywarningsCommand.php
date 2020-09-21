<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\Warning;
use adeynes\cucumber\utils\Queries;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class MywarningsCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'mywarnings',
            'cucumber.command.mywarnings',
            'See your warnings',
            '/mywarnings [-all|-a]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        if (!$sender instanceof Player) {
            $this->getPlugin()->formatAndSend($sender, 'error.not-ingame');
            return false;
        }

        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_WARNINGS_BY_PLAYER,
            ['player' => $sender->getLowerCaseName(), 'all' => $command->getFlag('all') !== null],
            function (array $rows) use ($sender) {
                $intro = $this->getPlugin()->formatMessageFromConfig(
                    'success.mywarnings.intro',
                    ['player' => $sender->getLowerCaseName(), 'count' => strval(count($rows))]
                );
                $lines = [$intro];
                foreach ($rows as $row) {
                    $lines[] = $this->getPlugin()->formatMessageFromConfig(
                        'success.mywarnings.list',
                        Warning::from($row)->getFormatData() + ['id' => $row['warning_id']]
                    );
                }

                $sender->sendMessage(implode(PHP_EOL, $lines));
            }
        );

        return true;
    }

}