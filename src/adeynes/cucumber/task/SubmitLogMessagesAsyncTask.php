<?php
declare(strict_types=1);

namespace adeynes\cucumber\task;

use adeynes\cucumber\Cucumber;

class SubmitLogMessagesAsyncTask extends CucumberTask
{

    /** @var string */
    protected $file;

    /** @var string[] */
    protected $messages = [];

    public function __construct(Cucumber $plugin, string $file)
    {
        $this->file = $file;
        parent::__construct($plugin);
    }

    public function onRun(int $tick): void
    {
        $this->getPlugin()->getServer()->getAsyncPool()->submitTask(
            new LogMessagesAsyncTask($this->file, $this->messages)
        );
        $this->messages = [];
    }

    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

}