<?php

namespace cucumber\mod;

use cucumber\Cucumber;
use cucumber\mod\utils\{BanList, MuteList};
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

    public function ipBan(CPlayer $player)
    {
        $ban = new Ban($player);
        if (isset($this->ip_bans[$player->getIp()]))
            $this->ip_bans['ip'][$player->getIp()]->ban($ban);
        else {
            $this->ip_bans['ip'][$player->getIp()] = new IpBan(
                $player->getIp(),
                [$ban]
            );
            $this->ip_bans['uid'][$player->getUid()] = new Ban($player);
        }
    }

    public function mute(CPlayer $player): void
    {
        $this->mutes->punish(new Mute($player));
    }

    // TODO: test performance
	public function isBanned(CPlayer $player): bool
	{
	    // Player is individually banned
	    if ($this->bans->isPunished($player)) return true;

	    // Player's IP matches an index in ip_bans[ip]
	    if (isset($this->ip_bans['ip'][$player->getIp()])) return true;

	    // Player's UID matches an index in ip_bans[uid]
        if (isset($this->ip_bans['uid'][$player->getUid()])) return true;

	    return false;
	}

}