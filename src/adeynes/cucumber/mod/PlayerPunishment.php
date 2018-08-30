<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

class PlayerPunishment extends SimplePunishment
{

    /** @var string */
    protected $player;

    public function __construct(string $player, string $reason, int $expiration, string $moderator)
    {
        $this->player = $player;
        parent::__construct($reason, $expiration, $moderator);
    }

    public function getPlayer(): string
    {
        return $this->player;
    }

    public function getData(): array
    {
        return parent::getData() + ['player' => $this->getPlayer()];
    }

    public function getDataFormatted(): array
    {
        return parent::getDataFormatted() + ['player' => $this->getPlayer()];
    }

}