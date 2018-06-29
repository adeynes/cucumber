<?php

namespace cucumber\mod;

use cucumber\Cucumber;
use cucumber\mod\utils\PlayerPunishmentList;
use cucumber\utils\CPlayer;

final class PunishmentManager
{

    /** @var Cucumber */
	private $plugin;
    /** @var PlayerPunishmentList<Ban> */
	private $bans;
    /** @var IpBan[][]|Ban[][] */
	private $ip_bans;
	/** @var PlayerPunishmentList<Mute> */
	private $mutes;

	public function __construct(Cucumber $plugin)
	{
		$this->plugin = $plugin;
		$this->loadBans();
	}

    private function loadBans(): void
    {
        $this->bans = new PlayerPunishmentList([]);
        $this->ip_bans = ["ip" => [], "uid" => []];
        $this->mutes = new PlayerPunishmentList([]);
    }

    public function saveBans(): void
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
            $this->ip_bans["ip"][$player->getIp()]->ban($ban);
        else {
            $this->ip_bans["ip"][$player->getIp()] = new IpBan(
                $player->getIp(),
                [$ban]
            );
            $this->ip_bans["uid"][$player->getUid()] = new Ban($player);
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

	    // Player's IP matches an index in ip_bans[
	    if (isset($this->ip_bans["ip"][$player->getIp()])) return true;

	    // Player's UID matches an index in ip_bans[uid]
        if (isset($this->ip_bans["uid"][$player->getUid()])) return true;

	    return false;
	}

}