<?php

namespace cucumber\mod\utils;

use cucumber\mod\Ban;
use cucumber\mod\IpBan;
use cucumber\mod\Punishment;
use cucumber\utils\CException;
use cucumber\utils\CPlayer;
use cucumber\utils\ErrorCodes;

// TODO: Refactor this & PlayerPunishmentList, *we /really/ need generics*
class IpBanList implements Punishment
{

    /** @var string[] */
    protected static $messages;

    /** @var int */
    protected $position = 0;

    /** @var IpBan[] */
    protected $ip_bans = [];

    /** @var BanList */
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

        $this->indiv_bans->ban($ban, true);
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
            $this->indiv_bans->unban($ban->getPlayer()->getUid());
        unset($this->ip_bans[$ip]);
    }

    public function isPunished(CPlayer $player): bool
    {
        return
            (isset($this->ip_bans[$player->getIp()]) && $this->ip_bans[$player->getIp()]->isBanned($player)) ||
            $this->indiv_bans->isBanned($player);
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

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): Punishment
    {
        return $this->ip_bans[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->ip_bans[$this->position]);
    }
    
}