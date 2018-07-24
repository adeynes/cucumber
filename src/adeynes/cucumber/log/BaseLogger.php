<?php
declare(strict_types=1);

namespace adeynes\cucumber\log;

class BaseLogger implements Logger
{

    /** @var LogManager */
    protected $manager;

    /**
     * The file to which log messages are outputted
     * @var string
     */
    protected $file;

    public function __construct(LogManager $manager, string $file = 'log_out.txt')
    {
        $this->manager = $manager;
        $this->file = $this->manager->getDirectory() . $file;
        $this->init();
    }

    // TODO: async I/O
    public function log(string $message): void
    {
        file_put_contents($this->file, $message . PHP_EOL);
    }

    protected function init(): void
    {
        if (!file_exists($this->file))
            file_put_contents($this->file, '');
    }

}