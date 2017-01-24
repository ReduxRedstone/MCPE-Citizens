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
                        $sender->sendMessage("§4§l[ERROR]§r§c You must enter an NPC command!§r");
                        return false;
                    }
                    switch ($args[0]) {
                        case 'create':
                            if (!isset($args[1])) {
                                $sender->sendMessage("§4§l[ERROR]§r§c You must enter an NPC name!§r");
                                return false;
                            }
                            unset($args[0]);
                            $name = implode(" ", $args);
                            $this->plugin->createNPC($sender, $name);
                            break;
                        case 'remove':
                            if (!isset($args[1])) {
                                $sender->sendMessage("§4§l[ERROR]§r§c You must enter a valid NPC ID!§r");
                                return false;
                            }
                            $npcID = $args[1];
                            $id = $this->getNPC($npcID);
                            if (!is_int($id)) {
                                $sender->sendMessage("§4§l[ERROR]§r§c Invalid NPC ID!§r");
                                return false;
                            }
                            $npc = $this->plugin->npcs[$id];
                            $this->plugin->removeNPC($sender, $npc, $id);
                            break;
                        case 'list':
                            $list = '§a==========§6§lCitizens§r§a==========§r';
                            foreach ($this->plugin->npcs as $npc) {
                                $name = $npc["name"];
                                $id = $npc["npc_id"];
                                $x = $npc["pos"]["x"];
                                $y = $npc["pos"]["y"];
                                $z = $npc["pos"]["z"];
                                $list .= "\n§b".$name."§r§b ID: §9".$id."§r§b Pos: §a".$x.", ".$y.", ".$z."";
                            }
                            $list .= '\n§a============================§r';
                            $sender->sendMessage($list);
                            break;
                    }
                    return true;
                }
                return false;
        }
    }

    public function getNPC($id) {
        while ($npc = current($this->plugin->npcs)) {
            if ($npc["npc_id"] == $id) {
                return key($this->plugin->npcs);
            }
            next($this->plugin->npcs);
        }
    }
}