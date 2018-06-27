<?php

namespace cucumber\mod\ban;

use cucumber\Cucumber;
use cucumber\utils\{CPlayer, Utils};

final class BanManager
{

    /** @var Cucumber */
	private $plugin;
    /** @var Ban[] */
	private $bans;
    /** @var IpBanList[] */
	private $ip_bans;

	public function __construct(Cucumber $plugin)
	{
		$this->plugin = $plugin;
		$this->bans = [
		    'name' => [],
            'uid' => [],
            'ip' => []
        ];
	}

	public function isBanned(Player $player): bool
	{
	    $is_ban = function($value) {
	        return $value instanceof Ban;
        };
	    $bans = array_filter(
	        [
	            $this->bans['name'][$player->getName()] ?? false,
                $this->bans['xuid'][Utils::getSafeXuid($player)] ?? false,
                $this->bans['ip'][$player->getAddress()] ?? false
            ],
            $is_ban);
	    foreach ($bans as $ban) if ($ban->isBanned($player) >= $this->min_ban) return true;
		return false;
	}

	public function ban(CPlayer $player): void
    {
        $ban = new Ban($player);

        $this->bans['name'][$player->getName()][] = $ban;
        if (!is_null($player->getUid()))
            $this->bans['uid'][$player->getUid()][] = $ban;
        $this->bans['ip'][$player->getIp()][] = $ban;
    }

    private function loadBans(): void
    {

    }

}
