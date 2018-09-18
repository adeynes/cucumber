<?php
declare(strict_types=1);

namespace adeynes\cucumber;

final class MigrationManager
{

    /** @var Cucumber */
    private $plugin;

    /** @var bool */
    private $is_migrated;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
    }

    public function migrate(): void
    {

    }

    public function isMigrated(): bool
    {
        return $this->is_migrated;
    }

}