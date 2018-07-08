<?php

namespace src\cucumber\command;

use cucumber\Cucumber;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

abstract class BaseCommand extends Command
{

    /** @var Cucumber */
    protected $plugin;

    public function __construct(Cucumber $plugin, string $name, string $description = '', string $usageMessage = null)
    {
        $this->plugin = $plugin;
        parent::__construct($name, $description, $usageMessage);
    }

    /**
     * This contains boilerplate code e.g. permission
     * checking, and executes BaseCommand::_execute()
     * @param CommandSender $sender
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $label, array $args): bool
    {
        if (!$this->testPermission($sender)) return false;

        return $this->_execute($sender, $label, $args);
    }

    abstract public function _execute(CommandSender $sender, string $label, array $args): bool;

}