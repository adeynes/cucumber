<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\utils\Queries;
use poggit\libasynql\DataConnector;

class UBan extends IpPunishment
{

    public function getFormatData(): array
    {
        return [
            'ip' => $this->getIp(),
            'reason' => $this->getReason(),
            'moderator' => $this->getModerator(),
            'time_created' => $this->getTimeOfCreationFormatted()
        ];
    }

    public function save(DataConnector $connector): void
    {
        $connector->executeInsert(
            Queries::CUCUMBER_PUNISH_UBAN,
            $this->getRawData()
        );
    }

}