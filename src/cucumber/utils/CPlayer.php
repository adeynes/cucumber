<?php

namespace cucumber\utils;

use pocketmine\Player;

class CPlayer
{

    protected $name;
    protected $ip;
    protected $uid;

    public function __construct($player, string $ip, string $uid = null)
    {
        if ($player instanceof Player)
            $unpacked = [$player->getName(), $player->getAddress(), self::getSafeXuid($player)];
        else
            $unpacked = [$player, $ip, $uid];

        $this->init(...$unpacked);
    }

    protected function init(string $name, string $ip, ?string $uid)
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

    public static function getSafeXuid(Player $player)
    {
        return $player->getXuid() !== '' ? $player->getUniqueId() : null;
    }

}