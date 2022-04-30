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
use adeynes\cucumber\mod\Punishment;
use adeynes\cucumber\utils\MessageFactory;
use adeynes\cucumber\utils\Queries;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

final class CucumberListener implements Listener
{

    /** @var Cucumber */
    private Cucumber $plugin;

    /** @var bool */
    private bool $log_traffic, $log_chat, $log_command;

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

        if ($this->getPlugin()->getPunishmentRegistry()->isMuted($player, $mute)) {
            /** @var Punishment $mute */
            $ev->cancel();
            $messages = $this->getPlugin()->getMessageConfig();
            $player->sendMessage(MessageFactory::fullFormat(
                $messages->getNested('moderation.mute.chat-attempt') ?? $messages->getNested('moderation.mute.mute.message'),
                $mute->getFormatData()
            ));
            if ($this->log_chat) {
                (new ChatAttemptEvent($player, $message))->call();
            }
        } elseif ($this->log_chat) {
            (new ChatEvent($player, $message))->call();
        }
    }

    public function onCommandPreprocess(PlayerCommandPreprocessEvent $ev)
    {
        if (str_starts_with(($command = $ev->getMessage()), '/') && $this->log_command) {
            (new CommandEvent($ev->getPlayer(), $command))->call();
        }
    }

    // Check if player is banned
    public function onPreLogin(PlayerLoginEvent $ev)
    {
        $player = $ev->getPlayer();
        $punishment_registry = $this->getPlugin()->getPunishmentRegistry();
        $punishment_registry->getUBanChecker()->checkFor($player);

        if ($punishment_registry->isBanned($player, $ban)) {
            /** @var Punishment $ban */
            $ev->setKickMessage(
                $this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $ban->getFormatData())
            );
            $ev->cancel();

            if ($this->log_traffic) {
                (new JoinAttemptEvent($player))->call();
            }
        }

        $this->getPlugin()->getConnector()->executeInsert(
            Queries::CUCUMBER_ADD_PLAYER,
            ['name' => strtolower($player->getName()), 'ip' => $player->getNetworkSession()->getIp()]
        );
    }

    public function onJoin(PlayerJoinEvent $ev)
    {
        $player = $ev->getPlayer();

        if (VanishCommand::isVanished($player)) {
            VanishCommand::setVanished($player, true);
        }

        if ($this->log_traffic) {
            (new JoinEvent($player))->call();
        }
    }

    public function onQuit(PlayerQuitEvent $ev)
    {
        if ($this->log_traffic) {
            (new QuitEvent($ev->getPlayer()))->call();
        }
    }

    public function onCucumberEvent(CucumberEvent $ev)
    {
        $log_dispatcher = $this->getPlugin()->getLogDispatcher();
        $log_dispatcher->log($log_dispatcher->formatEventMessage($ev), $ev->getSeverity());
    }

}