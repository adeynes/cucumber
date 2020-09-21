<?php
declare(strict_types=1);

namespace adeynes\cucumber\task;

use adeynes\asyncio\asyncio;
use adeynes\asyncio\FileWriteAsyncTask;
use adeynes\asyncio\WriteMode;
use pocketmine\scheduler\Task;

class SubmitLogMessagesAsyncTask extends Task
{

    /** @var string */
    protected $file;

    /** @var string[] */
    protected $messages = [];

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function onRun(int $tick): void
    {
        $message = implode(PHP_EOL, $this->messages);
        if ($message !== '') $message .= PHP_EOL;

        asyncio::submitTask(new FileWriteAsyncTask($this->file, $message, WriteMode::APPEND()));
        $this->messages = [];
    }

    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

}