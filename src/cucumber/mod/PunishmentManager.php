<?php

namespace cucumber\mod;

use cucumber\Cucumber;
use cucumber\mod\utils\BanList;
use cucumber\mod\utils\IpBanList;
use cucumber\mod\utils\MuteList;
use cucumber\utils\CPlayer;

final class PunishmentManager
{

    /** @var Cucumber */
    private $plugin;

    /** @var BanList */
    private $bans;

    /** @var IpBanList */
    private $ip_bans;

    /** @var MuteList */
    private $mutes;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
        $this->load();
    }

    private function load(): void
    {
        $this->bans = new BanList;
        $this->ip_bans = new IpBanList;
        $this->mutes = new MuteList;
    }

    public function save(): void
    {
        $provider = $this->plugin->getProvider();
        $provider->saveBans($this->bans);
        $provider->saveIpBans($this->ip_bans);
        $provider->saveMutes($this->mutes);
    }

    public function ban(CPlayer $player, int $until = null): void
    {
        $this->bans->ban(new Ban($player, $until));
    }

    public function unban(CPlayer $player): void
    {
        $this->bans->unban($player->getUid());
    }

    public function ipBan(CPlayer $player): void
    {
        $this->ip_bans->ban($player);
    }

    public function ipUnban(string $ip): void
    {
        $this->ip_bans->unban($ip);
    }

    public function mute(CPlayer $player, int $until = null): void
    {
        $this->mutes->punish(new Mute($player, $until));
    }

    public function unmute(CPlayer $player): void
    {
        $this->mutes->unmute($player->getUid());
    }

    // TODO (isBanned & isMuted) refactor time check for DRY
    // TODO: test performance
    public function isBanned(CPlayer $player): bool
    {
        $banned = false;

        if ($this->bans->isBanned($player)) {
            if (self::isTimeOver($this->bans->get($player)))
                $this->bans->unban($player);
            else
                $banned = true;
        }

        if ($this->ip_bans->isBanned($player)) $banned = true;

        return $banned;
    }

    public function isMuted(CPlayer $player): bool
    {
        $muted = false;

        if ($this->mutes->isMuted($player)) {
            if (self::isTimeOver($this->mutes->get($player)))
                $this->unmute($player);
            else
                $muted = true;
        }

        return $muted;
    }

    /**
     * Checks whether a punishment is over
     * @param PlayerPunishment $punishment
     * @return bool
     */
    public static function isTimeOver(PlayerPunishment $punishment): bool
    {
        return time() >= $punishment->until;
    }

}