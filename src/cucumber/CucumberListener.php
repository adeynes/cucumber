<?php
declare(strict_types=1);

namespace cucumber;

use cucumber\event\CucumberEvent;
use cucumber\event\ChatAttemptEvent;
use cucumber\event\ChatEvent;
use cucumber\event\CommandEvent;
use cucumber\event\JoinAttemptEvent;
use cucumber\event\JoinEvent;
use cucumber\event\QuitEvent;
use cucumber\utils\CucumberPlayer;
use cucumber\utils\MessageFactory;
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

    // Detect if player is banned
    public function onPreLogin(PlayerPreLoginEvent $ev)
    {
        $player = $ev->getPlayer();

        if ($ban = $this->getPlugin()->getPunishmentManager()->isBanned(new CucumberPlayer($player))) {
            $ev->setKickMessage(
                MessageFactory::format($this->getPlugin()->getMessage('moderation.ban.reason'), $ban->getData())
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