<?php
declare(strict_types=1);

namespace adeynes\cucumber\log;

use adeynes\cucumber\task\SubmitLogMessagesAsyncTask;
use pocketmine\scheduler\CancelTaskException;

class BaseLogger implements Logger
{

    /**
     * The file to which log messages are outputted
     * @var string
     */
    protected string $file;

    /** @var SubmitLogMessagesAsyncTask */
    protected SubmitLogMessagesAsyncTask $submit_log_messages_async_task;

    public function __construct(LogDispatcher $dispatcher, string $file = 'log_out.txt')
    {
        $this->file = $dispatcher->getDirectory() . $file;
        $this->init();
        $this->submit_log_messages_async_task = $task = new SubmitLogMessagesAsyncTask($this->file);
        $dispatcher->getPlugin()->getScheduler()->scheduleRepeatingTask(
            $task,
            $dispatcher->getPlugin()->getConfig()->getNested('task.write-task-period') * 20
        );
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

    /**
     * @throws CancelTaskException
     */
    public function logNow(): void
    {
        $this->submit_log_messages_async_task->onRun(0);
    }

}