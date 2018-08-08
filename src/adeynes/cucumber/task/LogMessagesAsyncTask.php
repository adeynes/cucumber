<?php
declare(strict_types=1);

namespace adeynes\cucumber\task;

use pocketmine\scheduler\AsyncTask;

class LogMessagesAsyncTask extends AsyncTask
{

    /** @var string */
    protected $file;

    /** @var string[] */
    protected $messages;

    public function __construct(string $file, array $messages)
    {
        $this->file = $file;
        $this->messages = $messages;
    }

    public function onRun(): void
    {
        $handle = fopen($this->file, 'a');
        foreach ($this->messages as $message) {
            fwrite($handle, $message . PHP_EOL);
        }
        fclose($handle);
    }

}