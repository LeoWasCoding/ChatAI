<?php

namespace leo\chat;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("ChatBotPlugin enabled!");
    }

    public function onPlayerChat(PlayerChatEvent $event) {
        $message = $event->getMessage();

        // Convert the message to lowercase for case-insensitive comparison
        $message = strtolower($message);

        // Check if the message contains "hi"
        if (strpos($message, 'hi') !== false) {
            $player = $event->getPlayer();
            $player->sendMessage("Hi there, ".$player->getName()."!");
        }
    }
}

