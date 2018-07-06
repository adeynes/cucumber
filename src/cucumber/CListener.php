<?php

namespace cucumber;

use cucumber\event\CEvent;
use cucumber\event\ChatEvent;
use cucumber\event\CommandEvent;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

final class CListener implements Listener
{

    private $plugin;

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onChat(PlayerChatEvent $ev)
    {
        $this->callEvent(
            new ChatEvent($ev->getPlayer(), $ev->getMessage())
        );
    }

    public function onCommandPreprocess(PlayerCommandPreprocessEvent $ev)
    {
        if (strpos(($command = $ev->getMessage()), '/') === 0)
            $this->callEvent(
                new CommandEvent($ev->getPlayer(), $command)
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