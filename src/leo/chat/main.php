<?php

namespace YourNamespace;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    private $config;
    private $wordList;
    private $wordIndex;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("ChatBotPlugin enabled!");

        // Load the config.yml file
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        // Initialize word list and index
        $this->wordList = $this->config->get("word_list", []);
        $this->wordIndex = 0;

        // Schedule task to give words every 5 minutes
        $this->getScheduler()->scheduleRepeatingTask(new class($this) extends Task {
            private $plugin;

            public function __construct(Main $plugin) {
                $this->plugin = $plugin;
            }

            public function onRun(int $currentTick) {
                $word = $this->plugin->getNextWord();
                $this->plugin->broadcastWord($word);
            }
        }, 20 * 60 * 5);
    }

    public function onPlayerChat(PlayerChatEvent $event) {
        // Check if the player's message matches the unscrambled word
        $message = $event->getMessage();
        $word = $this->getCurrentWord();

        if (strcasecmp($message, $word) === 0) {
            // Word unscrambled correctly, reward the player
            $player = $event->getPlayer();
            $reward = $this->config->get("reward", 1000);
            $this->getServer()->getPluginManager()->getPlugin("BedrockEconomy")->giveMoney($player, $reward);
            $player->sendMessage("Congratulations! You unscrambled the word and received $" . $reward);
        }
    }

    private function getNextWord() {
        $wordCount = count($this->wordList);
        $word = $this->wordList[$this->wordIndex];
        $this->wordIndex = ($this->wordIndex + 1) % $wordCount;
        return $word;
    }

    private function getCurrentWord() {
        $wordCount = count($this->wordList);
        $wordIndex = ($this->wordIndex - 1 + $wordCount) % $wordCount;
        return $this->wordList[$wordIndex];
    }

    private function broadcastWord($word) {
        $message = $this->config->get("message", "Unscramble the word: {word}");
        $message = str_replace("{word}", $word, $message);
        $this->getServer()->broadcastMessage($message);
    }
}
