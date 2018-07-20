<?php
declare(strict_types=1);

namespace cucumber\task;

use cucumber\Cucumber;
use pocketmine\scheduler\Task;

abstract class CucumberTask extends Task
{

    /** @var Cucumber */
    protected $plugin;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getPlugin(): Cucumber
    {
        return $this->plugin;
    }

    public function cancel(): void
    {
        $this->getPlugin()->cancelTask($this->getTaskId());
    }

}