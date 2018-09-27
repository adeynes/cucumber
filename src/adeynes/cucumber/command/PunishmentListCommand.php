<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

abstract class PunishmentListCommand extends CucumberCommand
{

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        $page = (int) ($command->get(['page'])[0] ?? 1);
        $limit = $this->getPlugin()->getConfig()->getNested('punishment.list.lines-per-page');

        $this->getPlugin()->getConnector()->executeSelect(
            $this->getSelectQuery(),
            ['from' => ($page - 1) * $limit, 'limit' => $limit],
            function (array $rows) use ($sender, $page) {
                $this->sendList($sender, $rows, $page);
            }
        );

        return true;
    }

    abstract protected function getSelectQuery(): string;

    abstract protected function sendList(CommandSender $sender, array $punishment_rows, int $page_number): void;

}