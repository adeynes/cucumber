<?php
declare(strict_types=1);

namespace adeynes\cucumber;

use adeynes\cucumber\event\CucumberEvent;
use adeynes\cucumber\event\ChatAttemptEvent;
use adeynes\cucumber\event\ChatEvent;
use adeynes\cucumber\event\CommandEvent;
use adeynes\cucumber\event\JoinAttemptEvent;
use adeynes\cucumber\event\JoinEvent;
use adeynes\cucumber\event\QuitEvent;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\cucumber\utils\Queries;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

final class CucumberListener implements Listener
{

    private $plugin;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getPlugin(): Cucumber
    {
        return $this->plugin;
    }

    public function onChat(PlayerChatEvent $ev)
    {
        $player = $ev->getPlayer();
        $message = $ev->getMessage();

        if ($this->getPlugin()->getPunishmentManager()->isMuted(new CucumberPlayer($player))) {
            $ev->setCancelled();
            $this->callEvent(
                new ChatAttemptEvent($player, $message)
            );
        } else
            $this->callEvent(
                new ChatEvent($player, $message)
            );
    }

    public function onCommandPreprocess(PlayerCommandPreprocessEvent $ev)
    {
        if (strpos(($command = $ev->getMessage()), '/') === 0)
            $this->callEvent(
                new CommandEvent($ev->getPlayer(), $command)
            );
    }

    // Check if player is banned
    public function onPreLogin(PlayerPreLoginEvent $ev)
    {
        $player = $ev->getPlayer();

        if ($ban = $this->getPlugin()->getPunishmentManager()->isBanned(new CucumberPlayer($player))) {
            $ev->setKickMessage(
                $this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $ban->getDataFormatted())
            );
            $ev->setCancelled();
            $this->callEvent(
                new JoinAttemptEvent($player)
            );
        }
    }

    public function onJoin(PlayerJoinEvent $ev)
    {
        $this->callEvent(
            new JoinEvent($player = $ev->getPlayer())
        );
        $this->getPlugin()->getConnector()->executeInsert(Queries::CUCUMBER_ADD_PLAYER,
            ['name' => $player->getLowerCaseName(), 'ip' => $player->getAddress()]);
    }

    public function onQuit(PlayerQuitEvent $ev)
    {
        $this->callEvent(
            new QuitEvent($ev->getPlayer())
        );
    }

    /**
     * Logs all CucumberEvent
     * @param CucumberEvent $ev
     */
    public function onCucumberEvent(CucumberEvent $ev)
    {
        $log_manager = $this->getPlugin()->getLogManager();
        $log_manager->log(
            $log_manager->formatEventMessage($ev)
        );
    }

    private function callEvent(Event $ev): void
    {
        $this->getPlugin()->getServer()->getPluginManager()->callEvent($ev);
    }

}