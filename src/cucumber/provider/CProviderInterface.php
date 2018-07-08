<?php

namespace cucumber\provider;

use cucumber\mod\utils\BanList;
use cucumber\mod\utils\IpBanList;
use cucumber\mod\utils\MuteList;

interface CProviderInterface
{

    public function close(): void;

    public function loadBans(): BanList;
    public function loadIpBans(): IpBanList;
    public function loadMutes(): MuteList;

    public function saveBans(BanList $bans): void;
    public function saveIpBans(IpBanList $ip_bans): void;
    public function saveMutes(MuteList $mutes): void;

}