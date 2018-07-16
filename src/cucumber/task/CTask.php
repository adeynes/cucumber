<?php
declare(strict_types=1);

namespace cucumber\task;

use cucumber\Cucumber;
use pocketmine\scheduler\Task;

abstract class CTask extends Task
{

    /** @var Cucumber */
    protected $plugin;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
    }

    public function cancel(): void
    {
        $this->plugin->cancelTask($this->getTaskId());
    }

}