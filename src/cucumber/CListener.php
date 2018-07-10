<?php

namespace cucumber;

use cucumber\event\CEvent;
use cucumber\event\ChatAttemptEvent;
use cucumber\event\ChatEvent;
use cucumber\event\CommandEvent;
use cucumber\event\JoinAttemptEvent;
use cucumber\event\JoinEvent;
use cucumber\event\QuitEvent;
use cucumber\utils\CPlayer;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

final class CListener implements Listener
{

    private $plugin;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onChat(PlayerChatEvent $ev)
    {
        $player = $ev->getPlayer();
        $message = $ev->getMessage();

        if ($this->plugin->getPunishmentManager()->isMuted(new CPlayer($player))) {
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

    // Detect if player is banned
    public function onPreLogin(PlayerPreLoginEvent $ev)
    {
        $player = $ev->getPlayer();

        if ($this->plugin->getPunishmentManager()->isBanned(new CPlayer($player))) {
            $ev->setCancelled();
            $this->callEvent(
                new JoinAttemptEvent($player)
            );
        }
    }

    public function onJoin(PlayerJoinEvent $ev)
    {
        $this->callEvent(
            new JoinEvent($ev->getPlayer())
        );
    }

    public function onQuit(PlayerQuitEvent $ev)
    {
        $this->callEvent(
            new QuitEvent($ev->getPlayer())
        );
    }

    /**
     * Logs all CEvent
     * @param CEvent $ev
     */
    public function onCEvent(CEvent $ev)
    {
        $log_manager = $this->plugin->getLogManager();
        $log_manager->log(
            $log_manager->formatEventMessage($ev)
        );
    }

    private function callEvent(Event $ev): void
    {
        $this->plugin->getServer()->getPluginManager()->callEvent($ev);
    }

}