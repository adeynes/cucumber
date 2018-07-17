<?php
declare(strict_types=1);

namespace cucumber\command;

use cucumber\Cucumber;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\plugin\Plugin;

abstract class CucumberCommand extends Command implements PluginIdentifiableCommand
{

    /** @var Cucumber */
    protected $plugin;

    /**
     * The minimum amount of arguments the command
     * must have. Anything less than this will
     * throw an InvalidCommandSyntaxException
     * @var int
     */
    protected $min_args;

    /**
     * The list of tags for this command
     * The tag name is the key, and the value
     * is the length of the tag's value
     * @var int[]
     */
    protected $tags;

    protected function __construct(Cucumber $plugin, string $name, string $permission = null, string $description = '',
                                   int $min_args = 0, string $usageMessage = null, array $tags = [])
    {
        $this->plugin = $plugin;
        $this->min_args = $min_args;
        $this->tags = $tags;
        $this->setPermission($permission);
        parent::__construct($name, $description, $usageMessage);
    }

    /**
     * @return Cucumber
     */
    public function getPlugin(): Plugin
    {
        return $this->plugin;
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
        if (count($args) < $this->min_args) throw new InvalidCommandSyntaxException();

        return $this->_execute($sender, CommandParser::parse($this, $args));
    }

    abstract public function _execute(CommandSender $sender, ParsedCommand $command): bool;

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