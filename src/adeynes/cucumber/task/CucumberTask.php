<?php
declare(strict_types=1);

namespace adeynes\cucumber\task;

use adeynes\cucumber\Cucumber;
use pocketmine\scheduler\Task;

abstract class CucumberTask extends Task
{

    /** @var Cucumber */
    protected Cucumber $plugin;

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
        $this->getHandler()->cancel();
    }

}