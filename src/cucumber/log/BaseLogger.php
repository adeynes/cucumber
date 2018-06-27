<?php

namespace cucumber\log;

class BaseLogger implements Logger
{

    protected $manager;
    protected $file;

    public function __construct(LogManager $manager, string $file = 'log_out.txt')
    {
        $this->manager = $manager;
        $this->file = $this->manager->getDirectory() . $file;
        $this->init();
    }

    public function log(string $message)
    {
        file_put_contents($this->file, $message . PHP_EOL);
    }

    protected function init()
    {
        if (!file_exists($this->file))
            file_put_contents($this->file, '');
    }

}