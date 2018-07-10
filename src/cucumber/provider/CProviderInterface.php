<?php

namespace cucumber\provider;


use cucumber\mod\SimplePunishment;

interface CProviderInterface
{

    public function close(): void;

    /**
     * @return SimplePunishment[]
     */
    public function loadBans(): array;

    /**
     * @return SimplePunishment[]
     */
    public function loadIpBans(): array;

    /**
     * @return SimplePunishment[]
     */
    public function loadMutes(): array;

    /**
     * @param SimplePunishment[] $bans
     */
    public function saveBans(array $bans): void;

    /**
     * @param SimplePunishment[] $ip_bans
     */
    public function saveIpBans(array $ip_bans): void;

    /**
     * @param SimplePunishment[] $mutes
     */
    public function saveMutes(array $mutes): void;

}