<?php
declare(strict_types=1);

namespace cucumber\command;

use cucumber\Cucumber;
use cucumber\utils\CException;
use cucumber\utils\CPlayer;
use cucumber\utils\MessageFactory;
use cucumber\utils\Queries;
use pocketmine\command\CommandSender;
use poggit\libasynql\result\SqlSelectResult;

class BanCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin)
    {
        parent::__construct($plugin, 'ban', 'cucumber.command.ban', 'Ban a player by unique identification (XUID)',
            1, '/ban <player> [reason] [-d <duration>]', [
                'd' => 1
            ]);
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $reason] = $command->get([0, [1, -1]]);
        $expiration = CommandParser::parseDuration($command->getTag('d') ?? '');

        if (is_null($target = CPlayer::getOnlinePlayer($target_name))) {
            $ban = function(SqlSelectResult $result) use ($sender, $target_name, $reason, $expiration) {
                if (empty($rows = $result->getRows())) {
                    $sender->sendMessage(
                        MessageFactory::format(
                            $this->getPlugin()->getMessage('error.target-does-not-exist'),
                            ['player' => $target_name]
                        )
                    );
                    return;
                }

                $row = $rows[0];
                $target = new CPlayer($row['name'], $row['ip'], $row['uid']);

                try {
                    $this->getPlugin()->getPunishmentManager()->ban($target, $reason, $expiration, $sender->getName());
                    $sender->sendMessage($this->getPlugin()->getMessage('success.ban'));
                } catch(CException $exception) {
                    $sender->sendMessage($exception->getMessage());
                }
            };

            $this->plugin->getConnector()->executeSelect(Queries::CUCUMBER_GET_FIND_PLAYER_BY_NAME,
                    ['name' => $target_name], $ban);
        } else {
            try {
                $this->getPlugin()->getPunishmentManager()->ban(new CPlayer($target), $reason, $expiration, $sender->getName());
                $sender->sendMessage($this->getPlugin()->getMessage('success.ban'));
            } catch(CException $exception) {
                $sender->sendMessage($exception->getMessage());
                return false;
            }
        }

        return true;
    }

}