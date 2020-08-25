<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

abstract class PunishmentListCommand extends CucumberCommand
{

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$page] = $command->get(['page']);
        $page = $page ?? '1';
        if (!is_numeric($page) || intval($page) < 1) {
            $this->getPlugin()->formatAndSend($sender, 'error.invalid-argument', ['argument' => $page]);
            return false;
        }
        $page = intval($page);
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
            function (array $rows) use ($sender, $page) {
                $this->sendList($sender, $rows, $page);
            }
        );

        return true;
    }

    abstract protected function isAllable(): bool;

    abstract protected function getSelectQuery(): string;

    abstract protected function sendList(CommandSender $sender, array $punishment_rows, int $page_number): void;

}