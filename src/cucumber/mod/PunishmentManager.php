<?php

namespace cucumber\mod;

use cucumber\Cucumber;
use cucumber\utils\CException;
use cucumber\utils\CPlayer;
use cucumber\utils\ErrorCodes;

final class PunishmentManager
{

    /** @var Cucumber */
    private $plugin;

    /** @var string[][] */
    private $messages;

    /** @var SimplePunishment[] */
    private $bans;

    /** @var SimplePunishment[] */
    private $ip_bans;

    /** @var SimplePunishment[] */
    private $mutes;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
        $this->initMessages();
        $this->load();
    }

    private function initMessages(): void
    {
        $this->messages = [
            'ban' => [
                'already-banned' => '%player% is already banned!',
                'not-banned' => '%player% is not banned!'
            ],
            'ip-ban' => [
                'already-banned' => 'IP %ip% is already banned!',
                'not-banned' => 'IP %ip% is not banned!',
            ],
            'mute' => [
                'already-muted' => '%player% is already muted!',
                'not-muted' => '%player% is not muted!'
            ]
        ];
    }

    private function load(): void
    {
        $this->bans = [];
        $this->ip_bans = [];
        $this->mutes = [];
    }

    public function save(): void
    {
        $provider = $this->plugin->getProvider();
        $provider->saveBans($this->bans);
        $provider->saveIpBans($this->ip_bans);
        $provider->saveMutes($this->mutes);
    }

    public function playerPunish(CPlayer $player, SimplePunishment $punishment, array &$storage, string $error_message)
    {
        $uid = $player->getUid();

        if (isset($storage[$uid]))
            throw new CException(
                $error_message,
                ['player' => $player->getName()],
                ErrorCodes::ATTEMPT_PUNISH_PUNISHED
            );

        $storage[$uid] = $punishment;
    }

    public function playerPardon(CPlayer $player, array &$storage, string $error_message)
    {
        $uid = $player->getUid();

        if (!isset($storage[$uid]))
            throw new CException(
                $error_message,
                ['player' => $player->getName()],
                ErrorCodes::ATTEMPT_PARDON_NOT_PUNISHED
            );

        unset($storage[$uid]);
    }

    /**
     * @param CPlayer $player
     * @param string|null $reason
     * @param int|null $expiration
     * @throws CException If the player is already banned
     */
    public function ban(CPlayer $player, string $reason = null, int $expiration = null): void
    {
        $this->playerPunish(
            $player,
            new SimplePunishment($reason, $expiration),
            $this->bans,
            $this->messages['ban']['already-banned']
        );
    }

    /**
     * @param CPlayer $player
     * @throws CException If the player is not banned
     */
    public function unban(CPlayer $player): void
    {
        $this->playerPardon($player, $this->bans, $this->messages['ban']['not-banned']);
    }

    /**
     * @param int $ip
     * @param string|null $reason
     * @param int|null $expiration
     * @throws CException If the IP is already banned
     */
    public function ipBan(int $ip, string $reason = null, int $expiration = null): void
    {
        if (isset($this->ip_bans[$ip]))
            throw new CException(
                $this->messages['ip-ban']['already-banned'],
                ['ip' => $ip],
                ErrorCodes::ATTEMPT_PUNISH_PUNISHED
            );

        $this->ip_bans[$ip] = new SimplePunishment($reason, $expiration);
    }

    /**
     * @param int $ip
     * @throws CException If the IP is not banned
     */
    public function ipUnban(int $ip)
    {
        if (!isset($this->ip_bans[$ip]))
            throw new CException(
                $this->messages['ip-ban']['not-banned'],
                ['ip' => $ip],
                ErrorCodes::ATTEMPT_PARDON_NOT_PUNISHED
            );

        unset($this->ip_bans[$ip]);
    }

    /**
     * @param CPlayer $player
     * @param string|null $reason
     * @param int|null $expiration
     * @throws CException If the player is already muted
     */
    public function mute(CPlayer $player, string $reason = null, int $expiration = null): void
    {
        $this->playerPunish(
            $player,
            new SimplePunishment($reason, $expiration),
            $this->mutes,
            $this->messages['mute']['already-muted']
        );
    }

    /**
     * @param CPlayer $player
     * @throws CException If the player is not muted
     */
    public function unmute(CPlayer $player): void
    {
        $this->playerPardon($player, $this->mutes, $this->messages['mute']['not-muted']);
    }

    public function isBanned(CPlayer $player): bool
    {
        $banned = false;
        $uid = $player->getUid();
        $ip = $player->getIp();

        if (isset($this->bans[$uid])) {
            if ($this->bans[$uid]->isExpired())
                $this->unban($player);
            else $banned = true;
        }

        if (isset($this->ip_bans[$ip])) {
            if ($this->ip_bans[$ip]->isExpired())
                $this->ipUnban($ip);
            else $banned = true;
        }

        return $banned;
    }

    public function isMuted(CPlayer $player): bool
    {
        $banned = false;
        $uid = $player->getUid();

        if (isset($this->mutes[$uid])) {
            if ($this->bans[$uid]->isExpired())
                $this->unmute($player);
            else $banned = true;
        }

        return $banned;
    }

}