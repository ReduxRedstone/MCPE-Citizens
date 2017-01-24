<?php

namespace Citizens;

class Config {

	private $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }
	
	public function load() {
		if (!file_exists("./plugins/Citizens") && !is_dir("./plugins/Citizens")) {
		    mkdir("./plugins/Citizens");
		}
		if (!file_exists("./plugins/Citizens/skins") && !is_dir("./plugins/Citizens/skins")) {
		    mkdir("./plugins/Citizens/skins");
		}
		if (!file_exists("./plugins/Citizens/npcs") && !is_dir("./plugins/Citizens/npcs")) {
		    mkdir("./plugins/Citizens/npcs");
		}
		if (!file_exists("./plugins/Citizens/npcs/_all.json") && !is_dir("./plugins/Citizens/npcs/_all.json")) {
		    $json = fopen("./plugins/Citizens/npcs/_all.json", "wb");
			fwrite($json, json_encode(array()));
			fclose($json);
		}
	}

	public function addNpc($data) {
		$currentNPCs = json_decode(file_get_contents("./plugins/Citizens/npcs/_all.json"), true);
		$currentNPCs[] = $data;
		$json = fopen("./plugins/Citizens/npcs/_all.json", "wb");
		fwrite($json, json_encode($currentNPCs));
		fclose($json);
		return $currentNPCs;
	}
	public function removeNpc($npcID) {
		$currentNPCs = json_decode(file_get_contents("./plugins/Citizens/npcs/_all.json"), true);
		unset($currentNPCs[$npcID]);
		$json = fopen("./plugins/Citizens/npcs/_all.json", "wb");
		fwrite($json, json_encode($currentNPCs));
		fclose($json);
		return $currentNPCs;
	}
	public function updateNpc($data) {
		
	}
}