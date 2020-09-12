<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\utils\Queries;
use poggit\libasynql\DataConnector;

class IpBan extends IpPunishment
{

    use Expirable;

    public static function from(array $row): IpBan
    {
        return new IpBan($row['ip'], $row['reason'], $row['expiration'], $row['moderator'], $row['time_created']);
    }

    public function __construct(string $ip, string $reason, ?int $expiration, string $moderator, int $time_created)
    {
        $this->expiration = $expiration;
        parent::__construct($ip, $reason, $moderator, $time_created);
    }

    public function getRawData(): array
    {
        return parent::getRawData() + ['expiration' => $this->getExpiration()];
    }

    public function getFormatData(): array
    {
        return [
            'ip' => $this->getIp(),
            'reason' => $this->getReason(),
            'expiration' => $this->getExpirationFormatted(),
            'expired' => $this->getExpiredFormatted(),
            'moderator' => $this->getModerator(),
            'time_created' => $this->getTimeOfCreationFormatted()
        ];
    }

    public function save(DataConnector $connector, ?callable $onSuccess = null): void
    {
        $connector->executeInsert(
            Queries::CUCUMBER_PUNISH_IP_BAN,
            $this->getRawData(),
            $onSuccess
        );
    }

}