<?php
declare(strict_types=1);

namespace adeynes\cucumber\log;

use adeynes\cucumber\task\LogMessagesAsyncTask;
use adeynes\cucumber\task\SubmitLogMessagesAsyncTask;

class BaseLogger implements Logger
{

    /** @var LogManager */
    protected $manager;

    /**
     * The file to which log messages are outputted
     * @var string
     */
    protected $file;

    /** @var SubmitLogMessagesAsyncTask */
    protected $submit_log_messages_async_task;

    public function __construct(LogManager $manager, string $file = 'log_out.txt')
    {
        $this->manager = $manager;
        $this->file = $this->manager->getDirectory() . $file;
        $plugin = $this->manager->getPlugin();
        $this->init();
        $this->submit_log_messages_async_task = $task = new SubmitLogMessagesAsyncTask($plugin, $this->file);
        $this->manager->getPlugin()->getScheduler()->scheduleRepeatingTask($task, 10 * 20);
    }

    protected function init(): void
    {
        if (!file_exists($this->file)) {
            fclose(fopen($this->file, 'w'));
        }
    }

    public function log(string $message): void
    {
        $this->submit_log_messages_async_task->addMessage($message);
    }

}