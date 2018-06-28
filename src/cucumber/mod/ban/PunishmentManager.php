<?php

namespace cucumber\mod\ban;

use cucumber\Cucumber;
use cucumber\mod\utils\PlayerPunishmentCompound;
use cucumber\utils\CPlayer;

final class PunishmentManager
{

    /** @var Cucumber */
	private $plugin;
    /** @var PlayerPunishmentCompound */
	private $ban_compound;
    /** @var IpBanList[] */
	private $ip_bans;

	public function __construct(Cucumber $plugin)
	{
		$this->plugin = $plugin;
		$this->loadBans();
	}

    private function loadBans(): void
    {
        $this->ban_compound = new PlayerPunishmentCompound([]);
        $this->ip_bans = [];
    }

    // TODO: test performance
	public function isBanned(CPlayer $player): bool
	{
	    // Player is individually banned
	    if ($this->ban_compound->getPunishment($player) instanceof Ban) return true;

	    // Player's IP matches an index in ip_bans
	    if (isset($this->ip_bans[$player->getIp()])) return true;

	    // Loop through IP bans
	    foreach ($this->ip_bans as $ip_ban)
	        if ($ip_ban->isPunished($player)) return true;

	    return false;
	}

	public function ban(CPlayer $player): void
    {
        $this->ban_compound->addPunishment(new Ban($player));
    }

    public function ipBan(CPlayer $player): void
    {
        $this->ip_bans[$player->getIp()] = new IpBanList($player->getIp(), new Ban($player));
    }

}
