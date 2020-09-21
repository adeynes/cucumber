<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\Persistent;
use adeynes\cucumber\utils\Queries;
use poggit\libasynql\DataConnector;

class UBan extends IpPunishment implements Persistent
{

    public static function from(array $row): UBan
    {
        return new UBan($row['ip'], $row['reason'], $row['moderator'], $row['time_created']);
    }

    public function getFormatData(): array
    {
        return [
            'ip' => $this->getIp(),
            'reason' => $this->getReason(),
            'expiration' => Cucumber::getInstance()->getMessage('moderation.no-expiration'),
            'expired' => Cucumber::getInstance()->getMessage('moderation.active'),
            'moderator' => $this->getModerator(),
            'time_created' => $this->getTimeOfCreationFormatted()
        ];
    }

    // TODO: this is DISGUSTING
    public function getMessagesPath(): string
    {
        return 'success.ipbanlist.list';
    }

    public function save(DataConnector $connector, ?callable $onSuccess = null): void
    {
        $connector->executeInsert(
            Queries::CUCUMBER_PUNISH_UBAN,
            $this->getRawData(),
            $onSuccess
        );
    }

}