<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

abstract class PlayerPunishment extends SimplePunishment
{

    /** @var string */
    protected string $player;

    public function __construct(string $player, string $reason, string $moderator, int $time_created)
    {
        $this->player = $player;
        parent::__construct($reason, $moderator, $time_created);
    }

    public function getPlayer(): string
    {
        return $this->player;
    }

    public function getRawData(): array
    {
        return parent::getRawData() + ['player' => $this->getPlayer()];
    }

}