<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\parsecmd\ParsedCommand;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\Player;

class VanishCommand extends CucumberCommand
{

    /**
     * @var Player[]
     */
    protected static $vanished = [];

    public function __construct(Cucumber $plugin)
    {
        parent::__construct(
            $plugin,
            'vanish',
            'cucumber.command.vanish',
            'Vanish from other player\'s sight',
            0,
            '/vanish'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        if (!$sender instanceof Player) {
            $this->getPlugin()->formatAndSend($sender, 'error.not-ingame');
            return false;
        }

        self::setVanished($sender, !self::isVanished(new CucumberPlayer($sender)));
        $this->getPlugin()->formatAndSend($sender, 'success.vanish');
    }

    public static function setVanished(Player $player, bool $value = true): void
    {
        $player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, $value);
        $player->setNameTagVisible(!$value);
    }

    public static function isVanished(CucumberPlayer $player): bool
    {
        return isset(self::$vanished[$player->getName()]);
    }

}