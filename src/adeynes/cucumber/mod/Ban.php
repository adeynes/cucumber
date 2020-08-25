<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\utils\Queries;
use poggit\libasynql\DataConnector;

class Ban extends PlayerPunishment
{

    use Expirable;

    public static function from(array $row): Ban
    {
        return new Ban($row['name'], $row['reason'], $row['expiration'], $row['moderator'], $row['time_created']);
    }

    public function __construct(string $player, string $reason, ?int $expiration, string $moderator, int $time_created)
    {
        $this->expiration = $expiration;
        parent::__construct($player, $reason, $moderator, $time_created);
    }

    public function getRawData(): array
    {
        return parent::getRawData() + ['expiration' => $this->getExpiration()];
    }

    public function getFormatData(): array
    {
        return [
            'player' => $this->getPlayer(),
            'reason' => $this->getReason(),
            'expiration' => $this->getExpirationFormatted(),
            'moderator' => $this->getModerator(),
            'time_created' => $this->getTimeOfCreationFormatted()
        ];
    }

    public function save(DataConnector $connector): void
    {
        $connector->executeInsert(
            Queries::CUCUMBER_PUNISH_BAN,
            $this->getRawData()
        );
    }

}