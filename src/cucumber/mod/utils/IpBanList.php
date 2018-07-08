<?php

namespace cucumber\mod\utils;

use cucumber\mod\Ban;
use cucumber\mod\IpBan;
use cucumber\utils\CException;
use cucumber\utils\CPlayer;
use cucumber\utils\ErrorCodes;

class IpBanList extends PunishmentList
{

    /** @var IpBan[] */
    protected $ip_bans = [];

    /** @var Ban[][] */
    protected $indiv_bans;

    /**
     * @param IpBan[] $ip_bans
     * @throws CException If one of the ip_bans exists twice
     */
    public function __construct(array $ip_bans = [])
    {
        self::initMessages();
        $this->indiv_bans = new BanList;
        foreach ($ip_bans as $ip_ban)
            $this->ban($ip_ban);
    }

    protected static function initMessages(): void
    {
        self::$messages = [
            'not-banned' => 'IP %ip% is not banned!'
        ];
    }

    public function ban(CPlayer $player): void
    {
        $ip = $player->getIp();
        $ban = new Ban($player);

        if (isset($this->ip_bans[$ip]))
            $this->ip_bans[$ip]->ban($ban);
        else
            $this->ip_bans[$ip] = new IpBan($ip, $ban);

        $this->indiv_bans[$player->getUid()][$ip] = $ban;
    }

    /**
     * @param string $ip
     * @throws CException If the IP isn't banned
     */
    public function unban(string $ip): void
    {
        if (!isset($this->ip_bans[$ip]))
            throw new CException(
                self::$messages['not-punished'],
                ['ip' => $ip],
                ErrorCodes::ATTEMPT_PARDON_NOT_PUNISHED
            );

        foreach ($this->ip_bans[$ip]->getBans()->getAll() as $ban)
            unset($this->indiv_bans[$ban->getPlayer()->getUid()][$ip]);

        unset($this->ip_bans[$ip]);
    }

    public function isPunished(CPlayer $player): bool
    {
        return
            (isset($this->ip_bans[$player->getIp()]) && $this->ip_bans[$player->getIp()]->isBanned($player)) ||
            !empty($this->indiv_bans[$player->getUid()]);
    }

    public function isBanned(CPlayer $player): bool
    {
        return $this->isPunished($player);
    }

    public function get(CPlayer $player): ?IpBan
    {
        return $this->isBanned($player) ? $this->ip_bans[$player->getIp()]: null;
    }

    /**
     * @return IpBan[]
     */
    public function getAll(): array
    {
        return $this->ip_bans;
    }

    public function current(): IpBan
    {
        return $this->ip_bans[$this->position];
    }

    public function valid(): bool
    {
        return isset($this->ip_bans[$this->position]);
    }
    
}