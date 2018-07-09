<?php

namespace cucumber\provider;

use cucumber\mod\lists\BanList;
use cucumber\mod\lists\IpBanList;
use cucumber\mod\lists\MuteList;

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