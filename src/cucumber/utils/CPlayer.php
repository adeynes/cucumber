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

    /** @var string|null */
    protected $ip;

    /**
     * @param Player|string $player
     * @param string|null $ip
     */
    public function __construct($player, ?string $ip = null)
    {
        if ($player instanceof Player)
            $properties = [$player->getName(), $player->getAddress()];
        else
            $properties = [$player, $ip];

        [$this->name, $this->ip] = $properties;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public static function from(array $row): self
    {
        return new self($row['name'], $row['ip']);
    }

    public static function getOnlinePlayer(string $name): ?Player
    {
        $player = Server::getInstance()->getPlayer($name);
        if (!is_null($player) && $player instanceof Player && $player->isOnline())
            return $player;
        else
            return null;
    }

}