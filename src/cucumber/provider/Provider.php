<?php

namespace cucumber\provider;

use cucumber\Cucumber;
use cucumber\mod\SimplePunishment;
use poggit\libasynql\libasynql;

final class Provider implements IProvider
{

    /** @var Cucumber */
    private $plugin;

    /** @var \mysqli */
    private $database;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
        $this->database = libasynql::create($plugin, $plugin->getConfig(), ['mysql' => 'mysql.sql']);
    }

    private function initTables(): void
    {

    }

    public function close(): void
    {
        $this->database->close();
    }

}