<?php
declare(strict_types=1);

namespace cucumber\utils;

use pocketmine\Player;
use pocketmine\Server;

/**
 * A wrapper for player data (name, IP, UID) that can
 * be universally passed around across Cucumber's APIs
 */
class CPlayer
{

    /** @var string */
    protected $name;

    /** @var string */
    protected $ip;

    /** @var string */
    protected $uid;

    /**
     * CPlayer constructor.
     * @param $player
     * @param string $ip
     * @param string|null $uid
     */
    public function __construct($player, string $ip = null, string $uid = null)
    {
        if ($player instanceof Player)
            $unpacked = [$player->getName(), $player->getAddress(), self::getSafeXuid($player)];
        else
            $unpacked = [$player, $ip, $uid];

        $this->init(...$unpacked);
    }

    protected function init(string $name, string $ip, ?string $uid): void
    {
        $this->name = $name;
        $this->ip = $ip;
        $this->uid = $uid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public static function getOnlinePlayer(string $name): ?Player
    {
        $player = Server::getInstance()->getPlayer($name);
        if (!is_null($player) && $player instanceof Player && $player->isOnline())
            return $player;
        else
            return null;
    }

    /**
     * Returns the player's unique ID (hashed XUID) if
     * they are logged in to XBL, otherwise returns null
     * @param Player $player
     * @return string|null
     */
    public static function getSafeXuid(Player $player): ?string
    {
        return $player->getXuid() !== '' ? (string) $player->getUniqueId() : null;
    }

}