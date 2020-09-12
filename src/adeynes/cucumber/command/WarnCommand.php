<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\Warning;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\CommandParser;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

class WarnCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'warn',
            'cucumber.command.warn',
            'Warn a player',
            '/warn <player> <duration>|inf [reason]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $duration, $reason] = $command->get(['player', 'duration', 'reason']);
        $target_name = strtolower($target_name);
        if ($reason === null) {
            $reason = $this->getPlugin()->getMessage('moderation.warning.default-reason');
        }
        if (in_array($duration, self::PERMANENT_DURATION_STRINGS)) {
            $expiration = null;
        } else {
            try {
                $expiration = $duration ? CommandParser::parseDuration($duration) : null;
            } catch (\InvalidArgumentException $exception) {
                $this->getPlugin()->formatAndSend($sender, 'error.invalid-duration', ['duration' => $duration]);
                return false;
            }
        }

        $warn = function () use ($sender, $target_name, $reason, $expiration) {
            $warning = new Warning($target_name, $reason, $expiration, $sender->getName(), time());
            $warning_data = $warning->getFormatData();
            $warning->save(
                $this->getPlugin()->getConnector(),
                function (int $insert_id, int $affected_rows) use ($sender, $target_name, $reason, $expiration, $warning_data) {
                    $warning_data += ['id' => $insert_id];

                    if ($target = CucumberPlayer::getOnlinePlayer($target_name)) {
                        $this->getPlugin()->formatAndSend($target, 'moderation.warning.message', $warning_data);
                    }

                    $this->getPlugin()->formatAndSend($sender, 'success.warn', $warning_data);
                }
            );
        };

        $this->doIfTargetExists($warn, $sender, $target_name);
        return true;
    }

}