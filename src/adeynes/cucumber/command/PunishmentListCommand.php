<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

abstract class PunishmentListCommand extends CucumberCommand
{

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$page_str] = $command->get(['page']);
        $page_str = $page_str ?? '1';
        if (!is_numeric($page_str) || intval($page_str) < 1) {
            $this->getPlugin()->formatAndSend($sender, 'error.invalid-argument', ['argument' => $page_str]);
            return false;
        }
        $page = intval($page_str);
        $limit = $this->getPlugin()->getConfig()->getNested('punishment.list.lines-per-page');
        $extra_args = [];
        if ($this->isAllable()) {
            if ($command->getFlag('all') !== null) {
                $extra_args += ['all' => true];
            }
        }

        $this->getPlugin()->getConnector()->executeSelect(
            $this->getSelectQuery(),
            ['from' => ($page - 1) * $limit, 'limit' => $limit] + $extra_args,
            function (array $punishment_rows) use ($sender, $page, $extra_args) {
                $this->getPlugin()->getConnector()->executeSelect(
                    $this->getCountQuery(),
                    $extra_args,
                    function (array $count_rows) use ($sender, $punishment_rows, $page) {
                        $this->sendList($sender, $punishment_rows, $count_rows[0]['count'], $page);
                    }
                );
            }
        );

        return true;
    }

    abstract protected function isAllable(): bool;

    abstract protected function getSelectQuery(): string;

    abstract protected function getCountQuery(): string;

    abstract protected function sendList(CommandSender $sender, array $punishment_rows, int $count, int $page_number): void;

}