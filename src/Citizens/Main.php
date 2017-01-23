<?php

namespace Citizens;

use Citizens\Commands;
use Citizens\Config;

use pocketmine\plugin\PluginBase;

use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\InteractPacket;

use pocketmine\entity\Entity;

use pocketmine\item\Item;
use pocketmine\utils\UUID;

use pocketmine\Player;


class Main extends PluginBase implements Listener {

    public $config;
    public $npcs;

    public function onLoad() {
        $this->getLogger()->info("Citizens by Redux now loaded.");
    }

    public function onEnable() {
        $this->config = new Config();
        $this->config->load();

        $this->npcs = json_decode(file_get_contents("./plugins/Citizens/npcs/_all.json"), true);

        $this->getCommand("npc")->setExecutor(new Commands($this));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->getLogger()->info("Citizens by Redux now enabled.");
    }

    public function onDisable() {
        $this->getLogger()->info("Citizens by Redux now disabled.");
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        if ($player instanceof Player and !empty($this->npcs)) {
            foreach ($this->npcs as $npc) {
                $this->spawnNPC($player, $npc);
            }
        }
    }

    public function onPacketReceived(DataPacketReceiveEvent $event) {
        // Detects if a player clicked an NPC working on an emit system so plugins can hook into this click
        $packet = $event->getPacket();
        if (isset($packet->action)) {
            $action = $packet->action;
        }
        if ($packet instanceof InteractPacket and isset($action) and $action === InteractPacket::ACTION_LEFT_CLICK) {
            if (array_search($packet->target, array_column($this->npcs, 'eid'))) {
               $this->getLogger()->info("Clicked an NPC");
            }
        }
    }

    public function spawnNPC($player, $npc) {

        $flags = 0;
        $flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
        $flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
        $flags |= 1 << Entity::DATA_FLAG_IMMOBILE;
        $flags |= 1 << Entity::DATA_FLAG_RIDING;

        $packet = new AddPlayerPacket();
        $packet->eid = $npc["eid"];
        $packet->uuid = UUID::fromString($npc["uuid"]);
        $packet->x = $npc["pos"]["x"];
        $packet->y = $npc["pos"]["y"];
        $packet->z = $npc["pos"]["z"];
        $packet->speedX = 0;
        $packet->speedY = 0;
        $packet->speedZ = 0;
        $packet->yaw = $npc["pos"]["yaw"];
        $packet->pitch = $npc["pos"]["pitch"];
        $packet->item = Item::get(0);

        $packet->metadata = [
            Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
            Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $npc["name"]],
            Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, 12],
        ];

        $player->dataPacket($packet);
    }


    public function createNPC($player, $name) {

        $uuid = $this->guidv4(random_bytes(16));
        $eid = rand(0, 9999999);

        $data = array();
        $data["pos"] = array("x"=>$player->x,"y"=>$player->y,"z"=>$player->z,"pitch"=>$player->pitch,"yaw"=>$player->yaw);
        $data["uuid"] = $uuid;
        $data["eid"] = $eid;
        $data["name"] = $name;
        //$data["commands"] = array();

        $this->config->addNpc($data);
        $this->npcs = json_decode(file_get_contents("./plugins/Citizens/npcs/_all.json"), true);
        foreach ($player->getLevel()->getPlayers() as $player) {
            $this->spawnNPC($player, $data);
        }
    }

    public function guidv4($data) { //THANK YOU StackOverflow
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}