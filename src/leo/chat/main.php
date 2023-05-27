<?php

namespace leo\chat;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("ChatBotPlugin enabled!");

        // Load the config.yml file
        $this->saveDefaultConfig();
        $this->reloadConfig();
    }

    public function onPlayerChat(PlayerChatEvent $event) {
        $message = $event->getMessage();
        $message = strtolower($message);

        // Load the conversations from the config file
        $conversations = $this->getConfig()->get("conversations", []);

        // Iterate through the conversations and check if the message matches any of the questions
        foreach ($conversations as $conversation) {
            $question = strtolower($conversation['question']);
            $response = $conversation['response'];

            if (strpos($message, $question) !== false) {
                $player = $event->getPlayer();
                $response = str_replace("{player}", $player->getName(), $response);
                $player->sendMessage($response);
                break;
            }
        }
    }
}
