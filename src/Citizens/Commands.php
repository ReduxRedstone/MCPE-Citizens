<?php

namespace Citizens;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\Player;


class Commands implements CommandExecutor {

    private $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        switch($command->getName()) {
            case "npc":
                if ($sender instanceof Player) {
                    if (!isset($args[0])) {
                        $sender->sendMessage("§4§l[ERROR]§r§c You must enter an NPC name!§r");
                        return false;
                    }
                    switch ($args[0]) {
                        case 'create':
                            unset($args[0]);
                            $name = implode(" ", $args);
                            $this->plugin->createNPC($sender, $name);
                            break;
                    }
                    return true;
                }
                return false;
        }
    }
}