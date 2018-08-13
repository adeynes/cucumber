<?php
declare(strict_types=1);

namespace adeynes\cucumber;

use adeynes\cucumber\command\VanishCommand;
use adeynes\cucumber\event\CucumberEvent;
use adeynes\cucumber\event\ChatAttemptEvent;
use adeynes\cucumber\event\ChatEvent;
use adeynes\cucumber\event\CommandEvent;
use adeynes\cucumber\event\JoinAttemptEvent;
use adeynes\cucumber\event\JoinEvent;
use adeynes\cucumber\event\QuitEvent;
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

    /** @var Cucumber */
    private $plugin;

    /** @var bool */
    private $log_traffic;

    /** @var bool */
    private $log_chat;

    /** @var bool */
    private $log_command;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
        $config = $this->getPlugin()->getConfig();
        $this->log_traffic = $config->getNested('log.traffic');
        $this->log_chat = $config->getNested('log.chat');
        $this->log_command = $config->getNested('log.command');
    }

    public function getPlugin(): Cucumber
    {
        return $this->plugin;
    }

    public function onChat(PlayerChatEvent $ev)
    {
        $player = $ev->getPlayer();
        $message = $ev->getMessage();

        if ($this->getPlugin()->getPunishmentManager()->isMuted($player)) {
            $ev->setCancelled();
            if ($this->log_chat) {
                $this->callEvent(new ChatAttemptEvent($player, $message));
            }
        } elseif ($this->log_chat) {
            $this->callEvent(new ChatEvent($player, $message));
        }
    }

    public function onCommandPreprocess(PlayerCommandPreprocessEvent $ev)
    {
        if (strpos(($command = $ev->getMessage()), '/') === 0 && $this->log_command) {
            $this->callEvent(new CommandEvent($ev->getPlayer(), $command));
        }
    }

    // Check if player is banned
    public function onPreLogin(PlayerPreLoginEvent $ev)
    {
        $player = $ev->getPlayer();
        $punishment_manager = $this->getPlugin()->getPunishmentManager();
        $punishment_manager->checkUban($player);

        if ($ban = $punishment_manager->isBanned($player)) {
            $ev->setKickMessage(
                $this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $ban->getDataFormatted())
            );
            $ev->setCancelled();

            if ($this->log_traffic) {
                $this->callEvent(new JoinAttemptEvent($player));
            }
        }

        $this->getPlugin()->getConnector()->executeInsert(
            Queries::CUCUMBER_ADD_PLAYER,
            ['name' => $player->getLowerCaseName(), 'ip' => $player->getAddress()]
        );
    }

    public function onJoin(PlayerJoinEvent $ev)
    {
        $player = $ev->getPlayer();

        if (VanishCommand::isVanished($player)) {
            VanishCommand::setVanished($player, true);
        }

        if ($this->log_traffic) {
            $this->callEvent(new JoinEvent($player));
        }
    }

    public function onQuit(PlayerQuitEvent $ev)
    {
        if ($this->log_traffic) {
            $this->callEvent(new QuitEvent($ev->getPlayer()));
        }
    }

    public function onCucumberEvent(CucumberEvent $ev)
    {
        $log_manager = $this->getPlugin()->getLogManager();
        $log_manager->log($log_manager->formatEventMessage($ev));
    }

    private function callEvent(Event $ev): void
    {
        $this->getPlugin()->getServer()->getPluginManager()->callEvent($ev);
    }

}