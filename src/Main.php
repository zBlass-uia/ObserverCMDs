<?php

namespace ObserverCMD;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener {

    private $logFile;

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->logFile = $this->getDataFolder() . "commands.log";

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TF::GREEN . "ObserverCMD enabled.");
    }

    public function onDisable() {
        $this->getLogger()->info(TF::RED . "ObserverCMD disabled.");
    }

    public function onCommandExecute(PlayerCommandPreprocessEvent $event) {
        $player = $event->getPlayer();
        $command = $event->getMessage();
        $time = date("Y-m-d H:i:s");

        $log = "[{$time}] " . TF::AQUA . $player->getName() . TF::WHITE . " executed: {$command}";
        file_put_contents($this->logFile, strip_tags($log) . "\n", FILE_APPEND);
        $this->getLogger()->info($log);
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        if(strtolower($command->getName()) === "ob") {
            if(!isset($args[0])) {
                $sender->sendMessage(TF::RED . "Usage: /ob <player>");
                return true;
            }

            $targetName = $args[0];
            if(!file_exists($this->logFile)) {
                $sender->sendMessage(TF::RED . "No command logs found.");
                return true;
            }

            $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $found = false;

            $sender->sendMessage(TF::GREEN . "Commands of {$targetName}:");
            foreach(array_reverse($lines) as $line) {
                if(strpos($line, $targetName) !== false) {
         
                    $line = str_replace($targetName, TF::AQUA . $targetName . TF::WHITE, $line);
                    $sender->sendMessage($line);
                    $found = true;
                }
            }

            if(!$found) {
                $sender->sendMessage(TF::RED . "No commands found for {$targetName}.");
            }

            return true;
        }
        return false;
    }
}
