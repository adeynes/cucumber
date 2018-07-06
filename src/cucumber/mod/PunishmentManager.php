<?php

namespace cucumber\mod;

use cucumber\Cucumber;
use cucumber\mod\utils\BanList;
use cucumber\mod\utils\MuteList;
use cucumber\utils\CPlayer;

final class PunishmentManager
{

    /** @var Cucumber */
    private $plugin;

    /** @var BanList */
    private $bans;

    /** @var IpBan[][]|BanList[] */
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
        $this->bans = new BanList([]);
        $this->ip_bans = ['ip' => [], 'uid' => new BanList()];
        $this->mutes = new MuteList([]);
    }

    public function save(): void
    {

    }

    public function ban(CPlayer $player): void
    {
        $this->bans->punish(new Ban($player));
    }

    public function unban(CPlayer $player): void
    {
        $this->bans->unban($player);
    }

    public function ipBan(CPlayer $player): void
    {
        $ban = new Ban($player);
        if (isset($this->ip_bans[$player->getIp()]))
            $this->ip_bans['ip'][$player->getIp()]->ban($ban);
        else {
            $this->ip_bans['ip'][$player->getIp()] = new IpBan(
                $player->getIp(),
                [$ban]
            );
        }
        $this->ip_bans['uid']->ban(new Ban($player));
    }

    public function ipUnban(CPlayer $player): void
    {
        foreach($this->ip_bans['ip'][$player->getIp()]->getBans() as $ban)
        {
            $this->ip_bans['uid']->unban($ban->getPlayer());
        }
        unset($this->ip_bans['ip'][$player->getIp()]);
    }

    public function mute(CPlayer $player): void
    {
        $this->mutes->punish(new Mute($player));
    }

    public function unmute(CPlayer $player): void
    {
        $this->mutes->unmute($player);
    }

    // TODO (isBanned & isMuted) refactor time check for DRY
    // TODO: test performance
    public function isBanned(CPlayer $player): bool
    {
        $banned = false;

        // Player is individually banned
        if ($this->bans->isBanned($player)) {
            if (self::isTimeOver($this->bans->get($player)))
                $this->bans->unban($player);
            else
                $banned = true;
        }

        // Player's UID matches an index in ip_bans[uid]
        // This needs to be before the IP check to ensure
        // that the same player doesn't have two entries
        // in ip_bans[uid], which could make ip unban a PITA
        else if (isset($this->ip_bans['uid'][$player->getUid()])) $banned = true;

        // Player's IP matches an index in ip_bans[ip]
        // Also checks isBanned() to add an indiv ban if the
        // player's IP is banned but they don't have an entry
        else if (isset($this->ip_bans['ip'][$player->getIp()])
            && $this->ip_bans['ip'][$player->getIp()]->isBanned($player)) $banned = true;

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