<?php

namespace src\cucumber\command;

use cucumber\Cucumber;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

abstract class CucumberCommand extends Command
{

    /** @var Cucumber */
    protected $plugin;

    /**
     * The list of tags for this command
     * The tag name is the key, and the value
     * is the length of the tag's value
     * @var int[]
     */
    protected $tags;

    protected function __construct(Cucumber $plugin, string $name, string $description = '', string $usageMessage = null,
                                   array $tags)
    {
        $this->plugin = $plugin;
        $this->tags = $tags;
        parent::__construct($name, $description, $usageMessage);
    }

    /**
     * This contains boilerplate code e.g. permission
     * checking, and executes CucumberCommand::_execute()
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

    /**
     * @return int[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function getTag(string $tag): ?int
    {
        return $this->getTags()[$tag] ?? null;
    }

}