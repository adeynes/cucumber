<?php

namespace cucumber\provider;

use cucumber\Cucumber;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class CProvider
{

    /** @var Cucumber */
    private $plugin;

    /** @var DataConnector */
    private $database;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
        $this->database = libasynql::create($plugin, $plugin->getConfig()->get('database'),
            [
                'mysql' => 'mysql.sql'
            ]);
    }

    public function close()
    {
        $this->database->close();
    }

}