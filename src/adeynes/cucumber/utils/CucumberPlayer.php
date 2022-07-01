<?php
declare(strict_types=1);

namespace adeynes\cucumber\utils;

use pocketmine\player\Player;
use pocketmine\Server;

class CucumberPlayer
{

    public static function getOnlinePlayer(string $name): ?Player
    {
        $player = Server::getInstance()->getPlayerByPrefix($name);
        if ($player && $player instanceof Player && $player->isOnline()) return $player;
        else return null;
    }

}