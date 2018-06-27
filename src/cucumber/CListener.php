<?php

namespace cucumber;

use cucumber\event\CEvent;
use pocketmine\event\Listener;
use pocketmine\event\Event;
use pocketmine\event\player\{PlayerChatEvent, PlayerCommandPreprocessEvent};

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
            new PPMChatEvent($this->plugin, $ev->getPlayer(), $ev->getMessage())
        );
    }

    public function onCommandPreprocess(PlayerCommandPreprocessEvent $ev)
    {
        if (strpos(($command = $ev->getMessage()), '/') === 0)
            $this->callEvent(
                new PPMCommandEvent($this->plugin, $ev->getPlayer(), $command)
            );
    }

    public function onCEvent(CEvent $ev)
    {
        $log_manager = $this->plugin->getLogManager();
        $log_manager->log(
            $log_manager->formatEventMessage($ev)
        );
    }

    private function callEvent(Event $ev)
    {
        $this->plugin->getServer()->getPluginManager()->callEvent($ev);
    }

}