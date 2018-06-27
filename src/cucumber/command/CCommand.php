<?php

namespace src\cucumber\command;

use cucumber\Cucumber;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

abstract class CCommand extends Command
{

    protected $plugin;

    public function __construct(Cucumber $plugin, string $name, string $description = "", string $usageMessage = null)
    {
        $this->plugin = $plugin;
        parent::__construct($name, $description, $usageMessage);
    }

    public function execute(CommandSender $sender, string $label, array $args)
    {
        if (!$this->testPermission($sender)) return false;

        $this->_execute($sender, $label, $args);
    }

    abstract public function _execute(CommandSender $sender, string $label, array $args);

}